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

namespace OpenCloud\ObjectStore\Resource;

use Guzzle\Http\EntityBody;
use Guzzle\Http\Message\Response;
use Guzzle\Http\Url;
use OpenCloud\Common\Constants\Header as HeaderConst;
use OpenCloud\Common\Exceptions;
use OpenCloud\Common\Lang;
use OpenCloud\ObjectStore\Constants\UrlType;
use OpenCloud\ObjectStore\Exception\ObjectNotEmptyException;

/**
 * Objects are the basic storage entities in Cloud Files. They represent the
 * files and their optional metadata you upload to the system. When you upload
 * objects to Cloud Files, the data is stored as-is (without compression or
 * encryption) and consists of a location (container), the object's name, and
 * any metadata you assign consisting of key/value pairs.
 */
class DataObject extends AbstractResource
{
    const METADATA_LABEL = 'Object';

    /**
     * @var Container
     */
    private $container;

    /**
     * @var The file name of the object
     */
    protected $name;

    /**
     * @var EntityBody
     */
    protected $content;

    /**
     * @var bool Whether or not this object is a "pseudo-directory"
     * @link http://docs.openstack.org/trunk/openstack-object-storage/developer/content/pseudo-hierarchical-folders-directories.html
     */
    protected $directory = false;

    /**
     * @var string The object's content type
     */
    protected $contentType;

    /**
     * @var The size of this object.
     */
    protected $contentLength;

    /**
     * @var string Date of last modification.
     */
    protected $lastModified;

    /**
     * @var string Etag.
     */
    protected $etag;
    
    /**
     * @var string Manifest. Can be null so we use false to mean unset.
     */
    protected $manifest = false;

    /**
     * Also need to set Container parent and handle pseudo-directories.
     * {@inheritDoc}
     *
     * @param Container $container
     * @param null      $data
     */
    public function __construct(Container $container, $data = null)
    {
        $this->setContainer($container);

        parent::__construct($container->getService());

        // For pseudo-directories, we need to ensure the name is set
        if (!empty($data->subdir)) {
            $this->setName($data->subdir)->setDirectory(true);

            return;
        }

        $this->populate($data);
    }

    /**
     * A collection list of DataObjects contains a different data structure than the one returned for the
     * "Retrieve Object" operation. So we need to stock the values differently.
     * {@inheritDoc}
     */
    public function populate($info, $setObjects = true)
    {
        parent::populate($info, $setObjects);

        if (isset($info->bytes)) {
            $this->setContentLength($info->bytes);
        }
        if (isset($info->last_modified)) {
            $this->setLastModified($info->last_modified);
        }
        if (isset($info->content_type)) {
            $this->setContentType($info->content_type);
        }
        if (isset($info->hash)) {
            $this->setEtag($info->hash);
        }
    }

    /**
     * Takes a response and stocks common values from both the body and the headers.
     *
     * @param Response $response
     * @return $this
     */
    public function populateFromResponse(Response $response)
    {
        $this->content = $response->getBody();

        $headers = $response->getHeaders();

        return $this->setMetadata($headers, true)
            ->setContentType((string) $headers[HeaderConst::CONTENT_TYPE])
            ->setLastModified((string) $headers[HeaderConst::LAST_MODIFIED])
            ->setContentLength((string) $headers[HeaderConst::CONTENT_LENGTH])
            ->setEtag((string) $headers[HeaderConst::ETAG])
            // do not cast to a string to allow for null (i.e. no header)
            ->setManifest($headers[HeaderConst::X_OBJECT_MANIFEST]);
    }

    public function refresh()
    {
        $response = $this->getService()->getClient()
            ->get($this->getUrl())
            ->send();

        return $this->populateFromResponse($response);
    }

    /**
     * @param Container $container
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param $name string
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $directory bool
     * @return $this
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * @return bool
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @return bool Is this data object a pseudo-directory?
     */
    public function isDirectory()
    {
        return (bool) $this->directory;
    }

    /**
     * @param  mixed $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->etag = null;
        $this->contentType = null;
        $this->content = EntityBody::factory($content);

        return $this;
    }

    /**
     * @return EntityBody
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param  string $contentType
     * @return $this
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getContentType()
    {
        return $this->contentType ? : $this->content->getContentType();
    }

    /**
     * @param $contentLength mixed
     * @return $this
     */
    public function setContentLength($contentLength)
    {
        $this->contentLength = $contentLength;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContentLength()
    {
        return $this->contentLength !== null ? $this->contentLength : $this->content->getContentLength();
    }

    /**
     * @param $etag
     * @return $this
     */
    public function setEtag($etag)
    {
        $this->etag = $etag;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEtag()
    {
        return $this->etag ? : $this->content->getContentMd5();
    }
    
    /**
     * @param string $manifest Path (`container/object') to set as the value to X-Object-Manifest
     * @return $this
     */
    protected function setManifest($manifest)
    {
        $this->manifest = $manifest;

        return $this;
    }

    /**
     * @return null|string Path (`container/object') from X-Object-Manifest header or null if the header does not exist
     */
    public function getManifest()
    {
        // only make a request if manifest has not been set (is false)
        return $this->manifest !== false ? $this->manifest : $this->getManifestHeader();
    }

    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    public function getLastModified()
    {
        return $this->lastModified;
    }

    public function primaryKeyField()
    {
        return 'name';
    }

    public function getUrl($path = null, array $params = array())
    {
        if (!$this->name) {
            throw new Exceptions\NoNameError(Lang::translate('Object has no name'));
        }

        return $this->container->getUrl($this->name);
    }

    public function update($params = array())
    {
        $metadata = is_array($this->metadata) ? $this->metadata : $this->metadata->toArray();
        $metadata = self::stockHeaders($metadata);

        // merge specific properties with metadata
        $metadata += array(
            HeaderConst::CONTENT_TYPE      => $this->contentType,
            HeaderConst::LAST_MODIFIED     => $this->lastModified,
            HeaderConst::CONTENT_LENGTH    => $this->contentLength,
            HeaderConst::ETAG              => $this->etag,
            HeaderConst::X_OBJECT_MANIFEST => $this->manifest
        );

        return $this->container->uploadObject($this->name, $this->content, $metadata);
    }

    /**
     * @param string $destination Path (`container/object') of new object
     * @return \Guzzle\Http\Message\Response
     */
    public function copy($destination)
    {
        return $this->getService()
            ->getClient()
            ->createRequest('COPY', $this->getUrl(), array(
                'Destination' => (string) $destination
            ))
            ->send();
    }

    public function delete($params = array())
    {
        return $this->getService()->getClient()->delete($this->getUrl())->send();
    }
    
    /**
     * Create a symlink to another named object from this object. Requires this object to be empty.
     *
     * @param string $destination Path (`container/object') of other object to symlink this object to
     * @return \Guzzle\Http\Message\Response The response
     * @throws \OpenCloud\Common\Exceptions\NoNameError if a destination name is not provided
     * @throws \OpenCloud\ObjectStore\Exception\ObjectNotEmptyException if $this is not an empty object
     */
    public function createSymlinkTo($destination)
    {
        if (!$this->name) {
            throw new Exceptions\NoNameError(Lang::translate('Object has no name'));
        }

        if ($this->getContentLength()) {
            throw new ObjectNotEmptyException($this->getContainer()->getName() . '/' . $this->getName());
        }

        $response = $this->getService()
            ->getClient()
            ->createRequest('PUT', $this->getUrl(), array(
                HeaderConst::X_OBJECT_MANIFEST => (string) $destination
            ))
            ->send();

        if ($response->getStatusCode() == 201) {
            $this->setManifest($source);
        }

        return $response;
    }

    /**
     * Create a symlink to this object from another named object. Requires the other object to either not exist or be empty.
     *
     * @param string $source Path (`container/object') of other object to symlink this object from
     * @return DataObject The symlinked object
     * @throws \OpenCloud\Common\Exceptions\NoNameError if a source name is not provided
     * @throws \OpenCloud\ObjectStore\Exception\ObjectNotEmptyException  if object already exists and is not empty
     */
    public function createSymlinkFrom($source)
    {
        if (!strlen($source)) {
            throw new Exceptions\NoNameError(Lang::translate('Object has no name'));
        }

        // Use ltrim to remove leading slash from source
        list($containerName, $resourceName) = explode("/", ltrim($source, '/'), 2);
        $container = $this->getService()->getContainer($containerName);

        if ($container->objectExists($resourceName)) {
            $object = $container->getPartialObject($source);
            if ($object->getContentLength() > 0) {
                throw new ObjectNotEmptyException($source);
            }
        }

        return $container->uploadObject($resourceName, 'data', array(
            HeaderConst::X_OBJECT_MANIFEST => (string) $this->getUrl()
        ));
    }

    /**
     * Get a temporary URL for this object.
     *
     * @link http://docs.rackspace.com/files/api/v1/cf-devguide/content/TempURL-d1a4450.html
     *
     * @param int    $expires        Expiration time in seconds
     * @param string $method         What method can use this URL? (`GET' or `PUT')
     * @param bool   $forcePublicUrl If set to TRUE, a public URL will always be used. The default is to use whatever
     *                               URL type the user has set for the main service.
     *
     * @return string
     *
     * @throws \OpenCloud\Common\Exceptions\InvalidArgumentError
     * @throws \OpenCloud\Common\Exceptions\ObjectError
     *
     */
    public function getTemporaryUrl($expires, $method, $forcePublicUrl = false)
    {
        $method = strtoupper($method);
        $expiry = time() + (int) $expires;

        // check for proper method
        if ($method != 'GET' && $method != 'PUT') {
            throw new Exceptions\InvalidArgumentError(sprintf(
                'Bad method [%s] for TempUrl; only GET or PUT supported',
                $method
            ));
        }

        // @codeCoverageIgnoreStart
        if (!($secret = $this->getService()->getAccount()->getTempUrlSecret())) {
            throw new Exceptions\ObjectError('Cannot produce temporary URL without an account secret.');
        }
        // @codeCoverageIgnoreEnd

        $url = $this->getUrl();
        if ($forcePublicUrl === true) {
            $url->setHost($this->getService()->getEndpoint()->getPublicUrl()->getHost());
        }

        $urlPath = urldecode($url->getPath());
        $body = sprintf("%s\n%d\n%s", $method, $expiry, $urlPath);
        $hash = hash_hmac('sha1', $body, $secret);

        return sprintf('%s?temp_url_sig=%s&temp_url_expires=%d', $url, $hash, $expiry);
    }

    /**
     * Remove this object from the CDN.
     *
     * @param null $email
     * @return mixed
     */
    public function purge($email = null)
    {
        if (!$cdn = $this->getContainer()->getCdn()) {
            return false;
        }

        $url = clone $cdn->getUrl();
        $url->addPath($this->name);

        $headers = ($email !== null) ? array('X-Purge-Email' => $email) : array();

        return $this->getService()
            ->getClient()
            ->delete($url, $headers)
            ->send();
    }

    /**
     * @param string $type
     * @return bool|Url
     */
    public function getPublicUrl($type = UrlType::CDN)
    {
        $cdn = $this->container->getCdn();

        switch ($type) {
            case UrlType::CDN:
                $uri = $cdn->getCdnUri();
                break;
            case UrlType::SSL:
                $uri = $cdn->getCdnSslUri();
                break;
            case UrlType::STREAMING:
                $uri = $cdn->getCdnStreamingUri();
                break;
            case UrlType::IOS_STREAMING:
                $uri = $cdn->getIosStreamingUri();
                break;
        }

        return (isset($uri)) ? Url::factory($uri)->addPath($this->name) : false;
    }

    protected static function headerIsValidMetadata($header)
    {
        $pattern = sprintf('#^%s-%s-Meta-#i', self::GLOBAL_METADATA_PREFIX, self::METADATA_LABEL);

        return preg_match($pattern, $header);
    }
    
    /**
     * @return null|string
     */
    protected function getManifestHeader()
    {
        $response = $this->getService()
            ->getClient()
            ->head($this->getUrl())
            ->send();
            
        $manifest = $response->getHeader(HeaderConst::X_OBJECT_MANIFEST);
        
        $this->setManifest($manifest);
        
        return $manifest;
    }
}
