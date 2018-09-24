<?php
/**
 * Created by PhpStorm.
 * User: fabsolutely
 * Date: 05/02/2017
 * Time: 19:39
 */

namespace Fabs\CouchDB2\Query\Queries;


use Fabs\CouchDB2\Constant\QueryMethods;
use Fabs\CouchDB2\Constant\QueryStatusCodes;
use Fabs\CouchDB2\Model\CouchObject;
use Fabs\CouchDB2\Query\DBQuery;
use Fabs\CouchDB2\Response\BulkDocsResponse;
use Fabs\Serialize\SerializableObject;

class BulkDocsDBQuery extends DBQuery
{
    /** @var CouchObject[] */
    protected $doc_list = [];

    public function __construct($couch_object, $database_name)
    {
        $this->reset();
        parent::__construct($couch_object, $database_name);
        $this->execution_method = 'bulk_docs';
        $this->query_url = '_bulk_docs';
        $this->query_data = ['docs' => []];
        $this->query_method = QueryMethods::POST;
        $this->allowed_response_codes = [QueryStatusCodes::CREATED];
        return $this;
    }

    public function addDocs($docs)
    {
        foreach ($docs as $doc) {
            $this->addDoc($doc);
        }
        return $this;
    }

    public function addDoc($doc)
    {
        if ($doc instanceof CouchObject) {
            $this->doc_list[] = $doc;
            $this->query_data['docs'][] = $doc->jsonSerialize();
        } else if (!is_array($doc)) {
            $this->doc_list[] = null;
            $this->query_data['docs'][] = (array)$doc;
        } else {
            $this->doc_list[] = null;
            $this->query_data['docs'][] = $doc;
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

    public function setNewEdits($value)
    {
        if (is_bool($value)) {
            $this->query_data['new_edits'] = $value;
        }
        return $this;
    }

    /**
     * @return SerializableObject|BulkDocsResponse
     */
    public function execute()
    {
        $response = parent::execute();
        /** @var BulkDocsResponse $bulk_response */
        $bulk_response = BulkDocsResponse::deserialize($response->getRawData());
        $counter = 0;
        foreach ($bulk_response->getDocs() as $document) {
            if ($this->doc_list[$counter] != null) {
                $this->doc_list[$counter]->_id = $document->getID();
                $this->doc_list[$counter]->_rev = $document->getRev();
            }
            $counter++;
        }

        return $bulk_response;
    }
}