<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 30/06/2017
 * Time: 12:21
 */

namespace Fabs\CouchDB2\Query\Queries;


// refactor support for OR
// refactor support for nested AND ORs
use Fabs\CouchDB2\Constant\QueryMethods;
use Fabs\CouchDB2\Constant\QueryStatusCodes;
use Fabs\CouchDB2\Couch;
use Fabs\CouchDB2\Query\DBQuery;
use Fabs\CouchDB2\Response\LuceneResponse;

class LuceneDBQuery extends DBQuery
{
    /** @var string[] */
    private $and_queries = [];

    /**
     * LuceneDBQuery constructor.
     * @param Couch $couch_object
     * @param string $database_name
     * @param string $design_doc_name
     * @param string $fulltext_name
     */
    public function __construct($couch_object, $database_name, $design_doc_name, $fulltext_name)
    {
        $this->reset();
        $this->execution_method = 'get_lucene_view';
        $this->query_method = QueryMethods::GET;
        $this->allowed_response_codes = [QueryStatusCodes::SUCCESS];
        $this->query_url = sprintf('_design/%s/%s', $design_doc_name, $fulltext_name);
        parent::__construct($couch_object, $database_name);
    }

    /**
     * @param string $value
     * @return LuceneDBQuery
     */
    public function setIncludeDocs($value)
    {
        $this->setQueryParameters('include_docs', $value, 'json_encode_boolean');
        return $this;
    }

    /**
     * @param string $value
     * @return LuceneDBQuery $this
     */
    public function setLimit($value)
    {
        $this->setQueryParameters('limit', $value, 'int');
        return $this;
    }

    /**
     * @param string $value
     * @return LuceneDBQuery
     */
    public function setSkip($value)
    {
        $this->setQueryParameters('skip', $value, 'int');
        return $this;
    }

    /**
     * @param string $value
     * @return LuceneDBQuery
     */
    public function setStale($value)
    {
        $this->setQueryParameters('stale', $value, 'string');
        return $this;
    }

    /**
     * @param string $value
     * @return LuceneDBQuery
     */
    public function addQueryAND($value)
    {
        $this->and_queries[] = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setSort($value)
    {
        $this->setQueryParameters('sort', $value);
        return $this;
    }

    /**
     * @return LuceneResponse
     */
    public function execute()
    {
        if (count($this->and_queries) > 0) {
            $this->setQueryParameters('q', implode(' AND ', $this->and_queries));
        }

        $response = parent::execute();
        return LuceneResponse::deserialize($response->getRawData());
    }

    public function getQueryUrl()
    {
        $url = parent::getQueryUrl();
        return sprintf('%s/%s', '_fti/local', $url);
    }
}