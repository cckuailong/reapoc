<?php
namespace Braintree;

use InvalidArgumentException;

/**
 * Upload documents to Braintree in exchange for a DocumentUpload object.
 *
 * An example of creating a document upload with all available fields:
 *      $result = Braintree\DocumentUpload::create([
 *          "kind" => Braintree\DocumentUpload::EVIDENCE_DOCUMENT,
 *          "file" => $pngFile
 *      ]);
 *
 * For more information on DocumentUploads, see https://developers.braintreepayments.com/reference/request/document_upload/create
 */
class DocumentUpload extends Base
{
    /* DocumentUpload Kind */
    const EVIDENCE_DOCUMENT = "evidence_document";

    protected function _initialize($documentUploadAttribs)
    {
        $this->_attributes = $documentUploadAttribs;
    }

    /**
     * Creates a DocumentUpload object
     * @param kind The kind of document
     * @param file The open file to upload
     * @throws InvalidArgumentException if the params are not expected
     */
    public static function create($params)
    {
        return Configuration::gateway()->documentUpload()->create($params);
    }

    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);
        return $instance;
    }
}
class_alias('Braintree\DocumentUpload', 'Braintree_DocumentUpload');
