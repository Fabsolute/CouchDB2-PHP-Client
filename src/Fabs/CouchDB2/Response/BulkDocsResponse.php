<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 20/03/2017
 * Time: 10:12
 */

namespace Fabs\CouchDB2\Response;


class BulkDocsResponse extends BaseResponse
{
    protected $docs = [];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return DocumentResponseElement
     */
    public function getOne()
    {
        if (count($this->docs) > 0) {
            return $this->docs[0];
        }
        return null;
    }

    /**
     * @return DocumentResponseElement[]
     */
    public function getDocs()
    {
        return $this->docs;
    }

    public function deserializeFromArray($data)
    {
        parent::deserializeFromArray($data);
        foreach ($data as $key => $value) {
            $this->docs[] = DocumentResponseElement::deserialize($value);
        }
    }
}