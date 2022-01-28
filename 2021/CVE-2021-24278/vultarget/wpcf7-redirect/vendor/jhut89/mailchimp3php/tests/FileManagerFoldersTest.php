<?php

namespace MailchimpTests;

use MailchimpAPI\Resources\FileManagerFolders;

class FileManagerFoldersTest extends MailChimpTestCase
{
    public function testFileManagerFoldersCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            FileManagerFolders::URL_COMPONENT,
            $this->mailchimp->fileManagerFolders(),
            "The File Manager Folders collection endpoint should be constructed correctly"
        );
    }

    public function testFileManagerFoldersInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            FileManagerFolders::URL_COMPONENT . 1,
            $this->mailchimp->fileManagerFolders(1),
            "The File Manager Folders instance endpoint should be constructed correctly"
        );
    }
}
