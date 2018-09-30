<?php

namespace Fabs\CouchDB2\Tool;


use Fabs\CouchDB2\Couch;
use Fabs\CouchDB2\CouchConfig;
use Fabs\CouchDB2\Exception\CouchDBException;
use Fabs\CouchDB2\LuceneSearchQueryBuilder;
use Fabs\CouchDB2\Model\LuceneOptions;
use Fabs\CouchDB2\Model\LuceneSearchQueryModel;
use Fabs\CouchDB2\Query\DBQuery;
use Fabs\CouchDB2\Query\Queries\GetAllDocsDBQuery;
use Fabs\CouchDB2\Query\Queries\GetViewDBQuery;
use Fabs\CouchDB2\Query\Queries\LuceneDBQuery;
use Fabs\CouchDB2\Response\BulkDocsResponse;
use Fabs\CouchDB2\Response\LuceneResponse;
use Fabs\CouchDB2\Response\UUIDResponse;
use Fabs\CouchDB2\Tool\Constant\DesignDocuments;
use Fabs\CouchDB2\Tool\Constant\Views;
use Fabs\CouchDB2\Tool\Model\SearchResponseModel;
use Fabstract\Component\Assert\Assert;

abstract class DBHandlerBase
{
    /** @var Couch */
    private $couch;
    /** @var CouchDBExceptionHandler */
    private $exception_handler;

    /**
     * HandlerBase constructor.
     * @param CouchConfig $couch_config
     * @param CouchDBExceptionHandler $exception_handler
     */
    public function __construct($couch_config, $exception_handler = null)
    {
        Assert::isType($couch_config, CouchConfig::class, 'couch_config');
        if ($exception_handler !== null) {
            Assert::isImplements($exception_handler, CouchDBExceptionHandler::class, 'exception_handler');
        }

        $this->couch = new Couch($couch_config);
        $this->exception_handler = $exception_handler;
    }

    /**
     * @param CouchDBExceptionHandler $exception_handler
     */
    public function setExceptionHandler($exception_handler)
    {
        Assert::isImplements($exception_handler, CouchDBExceptionHandler::class, 'exception_handler');

        $this->exception_handler = $exception_handler;
    }

    /**
     * @return CouchDBExceptionHandler|null
     */
    public function getExceptionHandler()
    {
        return $this->exception_handler;
    }

    /**
     * @param int $page
     * @param int $per_page
     * @return EntityBase[]
     */
    public function getAll($page = 0, $per_page = 0)
    {
        $get_db_query = $this->getView(DesignDocuments::COMMON, Views::GET_ALL)
            ->setReduce(false)
            ->setIncludeDocs(true)
            ->setDescending(true);

        $this->setPagination($get_db_query, $page, $per_page);

        $rows = $get_db_query->execute()->getRows();

        $all = [];
        foreach ($rows as $row) {
            $all[] = $row->getDocWithType($this->getEntityClass());
        }

        return $all;
    }

    /**
     * @return int
     */
    public function getAllTotalCount()
    {
        $db_query = $this->getView(DesignDocuments::COMMON, Views::GET_ALL);
        return $this->getTotalCount($db_query);
    }

    /**
     * @param LuceneSearchQueryModel[] $lucene_query_list
     * @param LuceneOptions $lucene_options
     * @param string $design_doc_name
     * @param string $fulltext_name
     * @param int $page
     * @param int $per_page
     * @return SearchResponseModel
     */
    public function performLuceneFulltextSearch(
        $lucene_query_list,
        $lucene_options,
        $design_doc_name,
        $fulltext_name,
        $page,
        $per_page)
    {
        $lucene_db_query = $this->getLuceneQuery($design_doc_name, $fulltext_name)
            ->setIncludeDocs(true);
        if ($lucene_options !== null) {
            $sort_option = $lucene_options->buildSortOptions();
            if ($sort_option !== null) {
                $lucene_db_query->setSort($sort_option);
            }
        }

        foreach ($lucene_query_list as $lucene_query) {
            $search_query_value = LuceneSearchQueryBuilder::buildFromModel($lucene_query);
            $lucene_db_query->addQueryAND($search_query_value);
        }

        if (count($lucene_query_list) === 0) {
            $lucene_db_query->addQueryAND('*:*');
        }

        $this->setPagination($lucene_db_query, $page, $per_page);

        $lucene_response = $lucene_db_query->execute();

        $search_response_model = new SearchResponseModel();
        $search_response_model->total_count = $this->getTotalCountFromLuceneResponse($lucene_response);
        $search_response_model->entity_list = $this->getEntityListFromLuceneResponse($lucene_response);

        return $search_response_model;
    }

    /**
     * @param string $id
     * @param bool $ignore_exception
     * @return EntityBase|null
     */
    public function getById($id, $ignore_exception = false)
    {
        try {
            /** @var EntityBase $response */
            $response = $this->getDBQuery()
                ->getDoc($id)
                ->execute()
                ->toObject($this->getEntityClass());

            return $response;
        } catch (CouchDBException $exception) {
            if ($ignore_exception === false) {
                $this->onCouchDBException($exception);
            }

            return null;
        }
    }

    /**
     * @param string[] $id_list
     * @return EntityBase[]|null
     */
    public function getByIdList($id_list)
    {
        $id_list = LINQ::from($id_list)
            ->where(function ($id) {
                if ($id === null) {
                    return false;
                }
                return true;
            })
            ->toArray();
        if (count($id_list) === 0) {
            return [];
        }

        try {
            $rows = $this->getDBQuery()
                ->getAllDocs()
                ->setIncludeDocs(true)
                ->setKeys($id_list)
                ->execute()
                ->getRows();

            $response_list = [];
            foreach ($rows as $row) {
                $response_list[$row->getID()] = $row->getDocWithType($this->getEntityClass());
            }

            return $response_list;
        } catch (CouchDBException $exception) {
            $this->onCouchDBException($exception);
            return null;
        }
    }

    /**
     * @param EntityBase $entity
     * @return EntityBase|null
     */
    public function save($entity)
    {
        if ($entity->isChanged() === false) {
            return $entity;
        }

        try {
            $this->getDBQuery()->saveDoc($entity)->execute();
            return $entity;
        } catch (CouchDBException $exception) {
            $this->onCouchDBException($exception);
            return null;
        }
    }

    /**
     * @param EntityBase[] $entities
     * @return EntityBase[]
     */
    public function saveAll($entities)
    {
        /** @var EntityBase[] $changed_entities */
        $changed_entities = [];
        foreach ($entities as $entity) {
            if ($entity->isChanged()) {
                $changed_entities[] = $entity;
            }
        }

        try {
            if (count($changed_entities) > 0) {
                /** @var BulkDocsResponse $bulk_docs_response */
                $bulk_docs_response = $this->getDBQuery()
                    ->bulkDocs()
                    ->addDocs($changed_entities)
                    ->execute();
                $failed_doc_ids = [];
                foreach ($bulk_docs_response->getDocs() as $doc_response) {
                    if ($doc_response->getOK() !== true) {
                        $failed_doc_ids[] = $doc_response->getID();
                    }
                }
                if (count($failed_doc_ids) !== 0) {
                    $this->onSaveAllFailed(
                        $this->getDatabaseName(),
                        $failed_doc_ids);
                    return null;
                }
            }

            return $entities;
        } catch (CouchDBException $exception) {
            $this->onCouchDBException($exception);
            return null;
        }
    }

    /**
     * @param int $count
     * @return string[]
     */
    public function generateDocId($count = 1)
    {
        /** @var UUIDResponse $uuid_response */
        $uuid_response = $this->getCouch()
            ->getUUIDs($count)
            ->execute();

        return $uuid_response->getUUIDs();
    }

    /**
     * @param int $count
     * @return null|string[]
     */
    public function generateUniqueId($count = 1)
    {
        try {
            return (new CouchCustomUUIDsQuery($this->getCouch(), $count))
                ->execute()
                ->getUUIDs();
        } catch (CouchDBException $exception) {
            $this->onCouchDBException($exception);
            return null;
        }
    }

    /**
     * @param EntityBase $entity
     * @return bool|null
     */
    public function deleteDoc($entity)
    {
        try {
            /** @var EntityBase $response */
            $this->getDBQuery()
                ->deleteDoc($entity)
                ->execute();
            return true;
        } catch (CouchDBException $exception) {
            $this->onCouchDBException($exception);
            return null;
        }
    }

    /**
     * @param string $design_document_name
     * @param string $view_name
     * @return GetViewDBQuery
     */
    protected function getView($design_document_name, $view_name)
    {
        return $this->getDBQuery()->getView($design_document_name, $view_name);
    }

    /**
     * @param string $design_doc_name
     * @param string $query_name
     * @return LuceneDBQuery
     */
    protected function getLuceneQuery($design_doc_name, $query_name)
    {
        return new LuceneDBQuery(
            $this->getCouch(),
            $this->getDatabaseName(),
            $design_doc_name,
            $query_name
        );
    }

    /**
     * @param string $design_document_name
     * @param string $update_handler_name
     * @return CustomGetUpdateHandlerDBQuery
     */
    protected function getUpdateHandler($design_document_name, $update_handler_name)
    {
        return new CustomGetUpdateHandlerDBQuery(
            $this->getCouch(),
            $this->getDatabaseName(),
            $design_document_name,
            $update_handler_name
        );
    }

    /**
     * @param GetAllDocsDBQuery|GetViewDBQuery|LuceneDBQuery $query
     * @param int $page
     * @param int $per_page
     */
    protected function setPagination($query, $page = 0, $per_page = 0)
    {
        if ($per_page > 0 && $page >= 0) {
            $skip_count = $page * $per_page;
            $query->setLimit($per_page)->setSkip($skip_count);
        }
    }

    /**
     * @param GetViewDBQuery $db_query
     * @return int
     */
    protected function getTotalCount($db_query)
    {
        $view_response = $db_query->setReduce(true)
            ->execute()
            ->getOne();
        if ($view_response === null) {
            return 0;
        }

        return $view_response->getStatsValue()->count;
    }

    /**
     * @param CouchDBException $exception
     */
    protected function onCouchDBException($exception)
    {
        if ($this->exception_handler !== null) {
            $this->exception_handler->handle($exception);
        }
    }

    /**
     * @return string
     */
    protected abstract function getDatabaseName();

    /**
     * @return string
     */
    protected abstract function getEntityClass();

    /**
     * @return Couch
     */
    private function getCouch()
    {
        return $this->couch;
    }

    /**
     * @return DBQuery
     */
    private function getDBQuery()
    {
        return $this->getCouch()
            ->selectDatabase($this->getDatabaseName());
    }

    /**
     * @param LuceneResponse $lucene_response
     * @return EntityBase[]
     */
    protected function getEntityListFromLuceneResponse($lucene_response)
    {
        $rows = $lucene_response->getRows();

        $entities = [];
        foreach ($rows as $row) {
            $entities[] = $row->getDocWithType($this->getEntityClass());
        }

        return $entities;
    }

    /**
     * @param LuceneResponse $lucene_response
     * @return int
     */
    protected function getTotalCountFromLuceneResponse($lucene_response)
    {
        return $lucene_response->getTotalRows();
    }

    /**
     * @param string $db_name
     * @param string[] $failed_doc_ids
     */
    private function onSaveAllFailed($db_name, $failed_doc_ids)
    {
        if ($this->exception_handler !== null) {
            $this->exception_handler->handleSaveAllFailed($db_name, $failed_doc_ids);
        }
    }
}
