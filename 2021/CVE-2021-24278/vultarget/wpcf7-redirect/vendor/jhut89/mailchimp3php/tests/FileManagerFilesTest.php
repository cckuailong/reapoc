<?php

namespace MailchimpTests;

use MailchimpAPI\Resources\FileManagerFiles;

class FileManagerFilesTest extends MailChimpTestCase
{
    public function testFileManagerFilesCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            FileManagerFiles::URL_COMPONENT,
            $this->mailchimp->fileManagerFiles(),
            "The File Manager Files collection endpoint should be constructed correctly"
        );
    }

    public function testFileManagerFilesInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            FileManagerFiles::URL_COMPONENT . 1,
            $this->mailchimp->fileManagerFiles(1),
            "The File Manager Files instance endpoint should be constructed correctly"
        );
    }
}
