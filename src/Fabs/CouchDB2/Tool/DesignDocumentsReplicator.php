<?php

namespace Fabs\CouchDB2\Tool;

use Fabs\CouchDB2\Couch;
use Fabs\CouchDB2\CouchConfig;
use Fabs\CouchDB2\Model\DesignDocument;
use Fabs\CouchDB2\Model\FullText;
use Fabs\CouchDB2\Model\ReplicatorDocument;
use Fabs\CouchDB2\Model\View;
use Fabs\CouchDB2\Query\DBQuery;

class DesignDocumentsReplicator
{

    /** @var Couch */
    private $couch = null;


    /**
     * @param Couch $couch
     * @return DesignDocumentsReplicator
     * @author necipallef <necipallef@gmail.com>
     */
    public function setCouch($couch)
    {
        $this->couch = $couch;
        return $this;
    }


    /**
     * @param string $design_documents_path
     * @return array
     * @author necipallef <necipallef@gmail.com>
     */
    public function createFromDirectory($design_documents_path)
    {
        $db_names = $this->couch->getAllDatabases()
            ->execute()
            ->getRawData();

        $count = 0;
        foreach (self::folderList($design_documents_path) as $db_path) {
            $db_name = str_replace($design_documents_path . '/', '', $db_path);
            if (!in_array($db_name, $db_names)) {
                $this->couch->createDatabase($db_name)
                    ->execute();
            }
            /** @var DesignDocument[] $design_document_list */
            $design_document_list = [];
            $design_document_rows = $this->couch->selectDatabase($db_name)
                ->getAllDocs()
                ->setStartKey('_design')
                ->setEndKey('_design{')
                ->setIncludeDocs(true)
                ->execute()
                ->getRows();
            foreach ($design_document_rows as $row) {
                /** @var DesignDocument $design_document_from_db */
                $design_document_from_db = $row->getDocWithType(DesignDocument::class);
                $design_document_list[$design_document_from_db->_id] = $design_document_from_db;
            }
            $design_document_updated_list = [];
            foreach (self::folderList($db_path) as $design_document_path) {
                $design_document_name = str_replace($db_path . '/', '', $design_document_path);
                $design_document_id = '_design/' . $design_document_name;
                if (array_key_exists($design_document_id, $design_document_list)) {
                    $design_document = $design_document_list[$design_document_id];
                } else {
                    $design_document = new DesignDocument();
                    $design_document->_id = $design_document_id;
                }

                foreach (self::folderList($design_document_path) as $design_document_element_path) {
                    $design_document_element_name = str_replace($design_document_path . '/', '', $design_document_element_path);
                    $design_document_element_files = glob($design_document_element_path . '/*.js');
                    switch ($design_document_element_name) {
                        case 'views':
                            foreach ($design_document_element_files as $view_file) {
                                $view_name = str_replace('.js', '',
                                    str_replace($design_document_element_path . '/', '', $view_file));

                                $view_entity = new View();
                                $view_entity->map = self::getDesignDocumentContent($view_file);
                                $reduce_file = $design_document_element_path . '/reduce/' . $view_name . '.js';
                                if (file_exists($reduce_file)) {
                                    $view_entity->reduce = self::getDesignDocumentContent($reduce_file);
                                }
                                $design_document->views[$view_name] = $view_entity;
                            }
                            break;
                        case 'updates':
                            foreach ($design_document_element_files as $update_file) {
                                $update_name = str_replace('.js', '',
                                    str_replace($design_document_element_path . '/', '', $update_file));
                                $design_document->updates[$update_name] = self::getDesignDocumentContent($update_file);
                            }
                            break;
                        case 'lists':
                            // todo
                            break;
                        case 'fulltext':
                            foreach ($design_document_element_files as $fulltext_file) {
                                $fulltext_name = str_replace('.js', '',
                                    str_replace($design_document_element_path . '/', '', $fulltext_file));

                                $fulltext_entity = new FullText();
                                $fulltext_entity->index = self::getDesignDocumentContent($fulltext_file);
                                $design_document->fulltext[$fulltext_name] = $fulltext_entity;
                            }
                            break;
                        case 'filters':
                            foreach ($design_document_element_files as $filter_file) {
                                $filter_name = str_replace(
                                    '.js',
                                    '',
                                    str_replace($design_document_element_path . '/', '', $filter_file)
                                );
                                $design_document->filters[$filter_name] = self::getDesignDocumentContent($filter_file);
                            }
                            break;
                        default:
                            break;
                    }
                }
                if ($design_document->isChanged()) {
                    $design_document_updated_list[] = $design_document;
                }
            }
            if (count($design_document_updated_list) > 0) {
                $this->couch->selectDatabase($db_name)
                    ->bulkDocs()
                    ->addDocs($design_document_updated_list)
                    ->execute();
            }

            $count += count($design_document_updated_list);
        }

        return ['count' => $count];
    }


    /**
     * @param string $design_documents_path
     * @return array
     * @author necipallef <necipallef@gmail.com>
     */
    public function createDirectoriesFromDB($design_documents_path)
    {
        $db_names = $this->couch->getAllDatabases()
            ->execute()
            ->getRawData();

        $count = 0;
        foreach ($db_names as $db_name) {
            $db_path = $design_documents_path . '/' . $db_name;
            $this->createDir($db_path);

            $rows = $this->couch->selectDatabase($db_name)
                ->getAllDocs()
                ->setIncludeDocs(true)
                ->setStartKey('_design')
                ->setEndKey('_design{')
                ->execute()
                ->getRows();

            /** @var DesignDocument[] $design_document_list */
            $design_document_list = [];
            foreach ($rows as $row) {
                $design_document_list[] = $row->getDocWithType(DesignDocument::class);
            }

            foreach ($design_document_list as $design_document) {
                $design_document_name = str_replace('_design/', '', $design_document->_id);
                $design_document_path = $db_path . '/' . $design_document_name;
                $this->createDir($design_document_path);

                // Views
                if (count($design_document->views) > 0) {
                    $views_path = $design_document_path . '/views';
                    $this->createDir($views_path);
                    foreach ($design_document->views as $view_name => $view) {
                        $view_file_name = $view_name . '.js';
                        $view_file_path = $views_path . '/' . $view_file_name;
                        file_put_contents($view_file_path, $view->map);
                        $count++;

                        // Reduce
                        if ($view->reduce !== null) {
                            $reduce_path = $views_path . '/reduce';
                            $this->createDir($reduce_path);

                            $reduce_file_name = $view_name . '.js';
                            $reduce_file_path = $reduce_path . '/' . $reduce_file_name;
                            file_put_contents($reduce_file_path, $view->reduce);
                            $count++;
                        }
                    }
                }

                // Fulltexts
                if (count($design_document->fulltext) > 0) {
                    $fulltexts_path = $design_document_path . '/fulltext';
                    $this->createDir($fulltexts_path);

                    foreach ($design_document->fulltext as $fulltext_name => $fulltext_entity) {
                        $fulltext_file_name = $fulltext_name . '.js';
                        $fulltext_file_path = $fulltexts_path . '/' . $fulltext_file_name;
                        file_put_contents($fulltext_file_path, $fulltext_entity->index);
                        $count++;
                    }
                }


                // Updates
                if (count($design_document->updates) > 0) {
                    $updates_path = $design_document_path . '/updates';
                    $this->createDir($updates_path);

                    foreach ($design_document->updates as $update_name => $update) {
                        $update_file_name = $update_name . '.js';
                        $update_file_path = $updates_path . '/' . $update_file_name;
                        file_put_contents($update_file_path, $update);
                        $count++;
                    }
                }

                // todo Filters

                // todo Validations

                // todo Lists
            }
        }

        return ['count' => $count];
    }


    /**
     * @param CouchConfig $source_couch_config
     * @param CouchConfig $target_couch_config
     * @param bool $is_continuous
     * @param bool $create_target
     * @param bool $include_views
     * @return array
     * @author necipallef <necipallef@gmail.com>
     */
    public function createReplicatorsAll(
        $source_couch_config,
        $target_couch_config,
        $is_continuous = false,
        $create_target = false,
        $include_views = true)
    {
        $source_couch = new Couch($source_couch_config);
        $target_couch = new Couch($target_couch_config);

        $db_names = $source_couch->getAllDatabases()
            ->execute()
            ->getRawData();

        $count = 0;
        $db_query = $target_couch->selectDatabase('_replicator')->bulkDocs();
        foreach ($db_names as $db_name) {
            if (strpos($db_name, '_') === 0) {
                continue;
            }

            $replicator_document_entity = new ReplicatorDocument();
            $replicator_document_entity->_id = $db_name . '_replicator';

            $source = sprintf('http://%s:%s@%s:%s/%s',
                $source_couch_config->username,
                $source_couch_config->password,
                $source_couch_config->server,
                $source_couch_config->port,
                $db_name);
            $replicator_document_entity->source = $source;

            if ($include_views === false) {
                $target = sprintf('http://%s:%s/%s',
                    $target_couch_config->server,
                    $target_couch_config->port,
                    $db_name);
            } else {
                $target = sprintf('http://%s:%s@%s:%s/%s',
                    $target_couch_config->username,
                    $target_couch_config->password,
                    $target_couch_config->server,
                    $target_couch_config->port,
                    $db_name);
            }
            $replicator_document_entity->target = $target;

            $replicator_document_entity->continuous = $is_continuous;
            $replicator_document_entity->create_target = $create_target;

            $db_query->addDoc($replicator_document_entity);
            $count++;
        }

        $db_query->execute();

        return ['count' => $count];
    }

    /**
     * @param string $db_name
     * @param CouchConfig $source_couch_config
     * @param CouchConfig $target_couch_config
     * @param bool $is_continuous
     * @param bool $create_target
     * @param bool $include_views
     * @return array
     * @author necipallef <necipallef@gmail.com>
     */
    public function createReplicator(
        $db_name,
        $source_couch_config,
        $target_couch_config,
        $is_continuous = false,
        $create_target = false,
        $include_views = true)
    {
        if (strpos($db_name, '_') === 0) {
            return ['aborting, db_name cannot start with underscore'];
        }

        $target_couch = new Couch($target_couch_config);

        $replicator_document_entity = new ReplicatorDocument();
        $replicator_document_entity->_id = $db_name . '_replicator';

        $source = sprintf('http://%s:%s@%s:%s/%s',
            $source_couch_config->username,
            $source_couch_config->password,
            $source_couch_config->server,
            $source_couch_config->port,
            $db_name);
        $replicator_document_entity->source = $source;

        if ($include_views === false) {
            $target = sprintf('http://%s:%s/%s',
                $target_couch_config->server,
                $target_couch_config->port,
                $db_name);
        } else {
            $target = sprintf('http://%s:%s@%s:%s/%s',
                $target_couch_config->username,
                $target_couch_config->password,
                $target_couch_config->server,
                $target_couch_config->port,
                $db_name);
        }
        $replicator_document_entity->target = $target;

        $replicator_document_entity->continuous = $is_continuous;
        $replicator_document_entity->create_target = $create_target;

        $result = $target_couch->selectDatabase('_replicator')
                ->bulkDocs()
                ->addDoc($replicator_document_entity)
                ->execute() !== null;

        return [$db_name . ' result' => $result];
    }

    /**
     * @return array
     * @author ahmetturk <ahmetturk93@gmail.com>
     */
    public function executeAllDesignDocuments()
    {
        $db_names = $this->couch->getAllDatabases()
            ->execute()
            ->getRawData();

        $count = 0;
        foreach ($db_names as $db_name) {

            $rows = $this->couch->selectDatabase($db_name)
                ->getAllDocs()
                ->setIncludeDocs(true)
                ->setStartKey('_design')
                ->setEndKey('_design{')
                ->execute()
                ->getRows();

            /** @var DesignDocument[] $design_document_list */
            $design_document_list = [];
            foreach ($rows as $row) {
                $design_document_list[] = $row->getDocWithType(DesignDocument::class);
            }

            foreach ($design_document_list as $design_document) {
                $design_document_name = str_replace('_design/', '', $design_document->_id);

                if (count($design_document->views) > 0) {
                    foreach ($design_document->views as $view_name => $view) {
                        $this->executeView($db_name, $design_document_name, $view_name);
                        $count++;
                        break;
                    }
                }
            }
        }

        return ['count' => $count];
    }

    /**
     * @param string $database_name
     * @param string $design_document_name
     * @param string $view_name
     * @return array
     * @author ahmetturk <ahmetturk93@gmail.com>
     */
    public function executeView($database_name, $design_document_name, $view_name)
    {
        $this->couch->selectDatabase($database_name)
            ->getView($design_document_name, $view_name)
            ->setStale('update_after')
            ->setLimit(1)
            ->execute();

        return ['result' => 'success'];
    }

    /**
     * @param string $file
     * @return string
     */
    public function getDesignDocumentContent($file)
    {
        return str_replace('couch_fun', '', file_get_contents($file));
    }

    /**
     * @param string $path
     * @return string[]
     */
    public function folderList($path)
    {
        return array_filter(glob($path . '/*'), 'is_dir');
    }


    /**
     * @param string $path
     * @return bool
     * @author necipallef <necipallef@gmail.com>
     */
    private function createDir($path)
    {
        if (file_exists($path) === false) {
            return mkdir($path);
        }

        return true;
    }
}