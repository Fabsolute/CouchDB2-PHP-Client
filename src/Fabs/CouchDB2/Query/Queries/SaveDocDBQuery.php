<?php
/**
 * Created by PhpStorm.
 * User: fabsolutely
 * Date: 06/02/2017
 * Time: 06:51
 */

namespace Fabs\CouchDB2\Query\Queries;


use Fabs\CouchDB2\Model\CouchObject;
use Fabs\CouchDB2\Query\DBQuery;
use Fabs\CouchDB2\Query\QueryMethods;
use Fabs\CouchDB2\Query\QueryStatusCodes;
use Fabs\CouchDB2\Response\DocumentResponseElement;
use Fabs\Serialize\SerializableObject;

class SaveDocDBQuery extends DBQuery
{
    /** @var CouchObject */
    private $save_object_document = null;

    public function __construct($couch_object, $database_name, $doc)
    {
        $this->reset();
        $this->execution_method = 'save_doc';
        if ($doc instanceof CouchObject) {
            $this->save_object_document = $doc;
            $doc = $doc->jsonSerialize();
        } elseif (!is_array($doc)) {
            $doc = (array)$doc;
        }
        $this->query_data = $doc;

        if (isset($doc['_id']) && !empty($doc['_id']) && isset($doc['_rev']) && !empty($doc['_rev'])) {
            $this->query_url = $doc['_id'];
            $this->query_method = QueryMethods::PUT;
            $this->allowed_response_codes = [QueryStatusCodes::CREATED];
        } else {
            $this->query_method = QueryMethods::POST;
            $this->allowed_response_codes = [QueryStatusCodes::CREATED];
        }
        parent::__construct($couch_object, $database_name);
    }

    public function setIfMatch($value)
    {
        if (is_string($value)) {
            $this->query_headers['If-Match'] = $value;
        }
        return $this;
    }

    public function setDelayedCommitPolicy($value)
    {
        if (is_bool($value)) {
            $this->query_headers['X-Couch-Full-Commit'] = $value;
        }
        return $this;
    }

    public function setBatch($value)
    {
        return $this->setQueryParameters('batch', $value, 'string');
    }

    public function setNewEdits($value)
    {
        return $this->setQueryParameters('new_edits', $value, 'json_encode_bool');
    }

    /**
     * @return SerializableObject|DocumentResponseElement
     */
    public function execute()
    {
        $response = parent::execute();
        /** @var DocumentResponseElement $document_response */
        $document_response = DocumentResponseElement::deserialize($response->getRawData());

        if ($this->save_object_document != null) {
            $this->save_object_document->_id = $document_response->getID();
            $this->save_object_document->_rev = $document_response->getRev();
        }

        return $document_response;
    }
}