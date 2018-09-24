<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 30/06/2017
 * Time: 12:18
 */

namespace Fabs\CouchDB2\Model;


class LuceneOptions
{
    /** @var  LuceneSortOption[] */
    private $sort_options = [];

    /**
     * @param LuceneSortOption $sort_option
     * @return $this
     */
    public function addSortOption($sort_option)
    {
        $this->sort_options[] = $sort_option;
        return $this;
    }

    /**
     * @return null|string
     */
    public function buildSortOptions()
    {
        if (count($this->sort_options) === 0)
        {
            return null;
        }

        $sort_by_array = [];
        foreach ($this->sort_options as $sort_option)
        {
            $sort_by = $sort_option->sort_by;
            if ($sort_option->ascending)
            {
                $sort_by = '/' . $sort_by;
            } else
            {
                $sort_by = '\\' . $sort_by;
            }

            $sort_by .= '<' . $sort_option->type . '>';
            $sort_by_array[] = $sort_by;
        }

        return implode(',', $sort_by_array);
    }
}