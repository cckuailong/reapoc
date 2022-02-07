<?php
/**
 * Copyright 2012-2014 Rackspace US, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace OpenCloud\Common\Collection;

use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Url;
use Iterator;
use OpenCloud\Common\Http\Message\Formatter;

/**
 * Class ResourceIterator is tasked with iterating over resource collections - many of which are paginated. Based on
 * a base URL, the iterator will append elements based on further requests to the API. Each time this happens,
 * query parameters (marker) are updated based on the current value.
 *
 * @package OpenCloud\Common\Collection
 * @since   1.8.0
 */
class PaginatedIterator extends ResourceIterator implements Iterator
{
    const MARKER = 'marker';
    const LIMIT = 'limit';

    /**
     * @var string Used for requests which append elements.
     */
    protected $currentMarker;

    /**
     * @var \Guzzle\Http\Url The next URL for pagination
     */
    protected $nextUrl;

    protected $defaults = array(
        // Collection limits
        'limit.total'           => 10000,
        'limit.page'            => 100,

        // The "links" element key in response
        'key.links'             => 'links',

        // JSON structure
        'key.collection'        => null,
        'key.collectionElement' => null,

        // The property used as the marker
        'key.marker'            => 'name',

        // Options for "next page" request
        'request.method'        => 'GET',
        'request.headers'       => array(),
        'request.body'          => null,
        'request.curlOptions'   => array()
    );

    protected $required = array('resourceClass', 'baseUrl');

    /**
     * Basic factory method to easily instantiate a new ResourceIterator.
     *
     * @param       $parent  The parent object
     * @param array $options Iterator options
     * @param array $data    Optional data to set initially
     * @return static
     */
    public static function factory($parent, array $options = array(), array $data = null)
    {
        $list = new static();

        $list->setOptions($list->parseOptions($options))
            ->setResourceParent($parent)
            ->rewind();

        if ($data) {
            $list->setElements($data);
        } else {
            $list->appendNewCollection();
        }

        return $list;
    }


    /**
     * @param Url $url
     * @return $this
     */
    public function setBaseUrl(Url $url)
    {
        $this->baseUrl = $url;

        return $this;
    }

    public function current()
    {
        return parent::current();
    }

    public function key()
    {
        return parent::key();
    }

    /**
     * {@inheritDoc}
     * Also update the current marker.
     */
    public function next()
    {
        if (!$this->valid()) {
            return false;
        }

        $current = $this->current();

        $this->position++;
        $this->updateMarkerToCurrent();

        return $current;
    }

    /**
     * Update the current marker based on the current element. The marker will be based on a particular property of this
     * current element, so you must retrieve it first.
     */
    public function updateMarkerToCurrent()
    {
        if (!isset($this->elements[$this->position])) {
            return;
        }

        $element = $this->elements[$this->position];
        $this->setMarkerFromElement($element);
    }

    protected function setMarkerFromElement($element)
    {
        $key = $this->getOption('key.marker');

        if (isset($element->$key)) {
            $this->currentMarker = $element->$key;
        }
    }

    /**
     * {@inheritDoc}
     * Also reset current marker.
     */
    public function rewind()
    {
        parent::rewind();
        $this->currentMarker = null;
    }

    public function valid()
    {
        $totalLimit = $this->getOption('limit.total');
        if ($totalLimit !== false && $this->position >= $totalLimit) {
            return false;
        } elseif (isset($this->elements[$this->position])) {
            return true;
        } elseif ($this->shouldAppend() === true) {
            $before = $this->count();
            $this->appendNewCollection();
            return ($this->count() > $before) ? true : false;
        }

        return false;
    }

    protected function shouldAppend()
    {
        return $this->currentMarker && (
            $this->nextUrl ||
            $this->position % $this->getOption('limit.page') == 0
        );
    }

    /**
     * Append an array of standard objects to the current collection.
     *
     * @param array $elements
     * @return $this
     */
    public function appendElements(array $elements)
    {
        $this->elements = array_merge($this->elements, $elements);

        return $this;
    }

    /**
     * Retrieve a new page of elements from the API (based on a new request), parse its response, and append them to the
     * collection.
     *
     * @return $this|bool
     */
    public function appendNewCollection()
    {
        $request = $this->resourceParent
            ->getClient()
            ->createRequest(
                $this->getOption('request.method'),
                $this->constructNextUrl(),
                $this->getOption('request.headers'),
                $this->getOption('request.body'),
                $this->getOption('request.curlOptions')
            );

        try {
            $response = $request->send();
        } catch (ClientErrorResponseException $e) {
            return false;
        }

        if (!($body = Formatter::decode($response)) || $response->getStatusCode() == 204) {
            return false;
        }

        $this->nextUrl = $this->extractNextLink($body);

        return $this->appendElements($this->parseResponseBody($body));
    }

    /**
     * Based on the response body, extract the explicitly set "link" value if provided.
     *
     * @param $body
     * @return bool
     */
    public function extractNextLink($body)
    {
        $key = $this->getOption('key.links');

        $value = null;

        if (isset($body->$key)) {
            foreach ($body->$key as $link) {
                if (isset($link->rel) && $link->rel == 'next') {
                    $value = $link->href;
                    break;
                }
            }
        }

        return $value;
    }

    /**
     * Make the next page URL.
     *
     * @return Url|string
     */
    public function constructNextUrl()
    {
        if (!$url = $this->nextUrl) {
            $url = clone $this->getOption('baseUrl');
            $query = $url->getQuery();

            if (isset($this->currentMarker)) {
                $query[static::MARKER] = $this->currentMarker;
            }

            if (($limit = $this->getOption('limit.page')) && !$query->hasKey(static::LIMIT)) {
                $query[static::LIMIT] = $limit;
            }

            $url->setQuery($query);
        }

        return $url;
    }

    /**
     * Based on the response from the API, parse it for the data we need (i.e. an meaningful array of elements).
     *
     * @param $body
     * @return array
     */
    public function parseResponseBody($body)
    {
        $collectionKey = $this->getOption('key.collection');

        $data = array();

        if (is_array($body)) {
            $data = $body;
        } elseif (isset($body->$collectionKey)) {
            if (null !== ($elementKey = $this->getOption('key.collectionElement'))) {
                // The object has element levels which need to be iterated over
                foreach ($body->$collectionKey as $item) {
                    $subValues = $item->$elementKey;
                    unset($item->$elementKey);
                    $data[] = array_merge((array) $item, (array) $subValues);
                }
            } else {
                // The object has a top-level collection name only
                $data = $body->$collectionKey;
            }
        }

        return $data;
    }

    /**
     * Walk the entire collection, populating everything.
     */
    public function populateAll()
    {
        while ($this->valid()) {
            $this->next();
        }
    }
}
