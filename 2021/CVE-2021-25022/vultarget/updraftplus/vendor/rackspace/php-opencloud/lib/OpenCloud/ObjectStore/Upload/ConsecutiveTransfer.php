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

namespace OpenCloud\ObjectStore\Upload;

use Guzzle\Http\EntityBody;
use Guzzle\Http\ReadLimitEntityBody;
use OpenCloud\Common\Constants\Size;

/**
 * A transfer type which executes consecutively - i.e. it will upload an entire EntityBody and then move on to the next
 * in a linear fashion. There is no concurrency here.
 *
 * @codeCoverageIgnore
 */
class ConsecutiveTransfer extends AbstractTransfer
{
    public function transfer()
    {
        while (!$this->entityBody->isConsumed()) {
            if ($this->entityBody->getContentLength() && $this->entityBody->isSeekable()) {
                // Stream directly from the data
                $body = new ReadLimitEntityBody($this->entityBody, $this->partSize, $this->entityBody->ftell());
            } else {
                // If not-seekable, read the data into a new, seekable "buffer"
                $body = EntityBody::factory();
                $output = true;
                while ($body->getContentLength() < $this->partSize && $output !== false) {
                    // Write maximum of 10KB at a time
                    $length = min(10 * Size::KB, $this->partSize - $body->getContentLength());
                    $output = $body->write($this->entityBody->read($length));
                }
            }

            if ($body->getContentLength() == 0) {
                break;
            }

            $request = TransferPart::createRequest(
                $body,
                $this->transferState->count() + 1,
                $this->client,
                $this->options
            );

            $response = $request->send();

            $this->transferState->addPart(TransferPart::fromResponse($response));
        }
    }
}
