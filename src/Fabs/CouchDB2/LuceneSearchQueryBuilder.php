<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 30/06/2017
 * Time: 15:02
 */

namespace Fabs\CouchDB2;


use Fabs\CouchDB2\Model\LuceneSearchQueryModel;

class LuceneSearchQueryBuilder
{
    /**
     * @param string $field
     * @param string $value
     * @param bool $exact
     * @param bool $required
     * @return null|string[]
     */
    public static function build($field, $value, $exact = false, $required = false)
    {
        $clean_query = preg_replace('/[^\sa-zA-Z0-9ğüşıöçĞÜŞİÖÇ]+/', ' ', $value);

        $words = explode(' ', $clean_query);
        $count = count($words);
        for ($i = 0; $i < $count; $i++)
        {
            if (strlen(trim($words[$i])) === 0)
            {
                unset($words[$i]);
                continue;
            }

            $words[$i] = $field . ':' . $words[$i];

            if (!$exact)
            {
                $words[$i] = $words[$i] . '*';
            }

            if ($required)
            {
                $words[$i] = '+' . $words[$i];
            }
        }

        if (count($words) === 0)
        {
            return null;
        }

        return implode(' ', $words);
    }

    /**
     * @param LuceneSearchQueryModel $lucene_query_model
     * @return null|string
     */
    public static function buildFromModel($lucene_query_model)
    {
        return self::build($lucene_query_model->field,
            $lucene_query_model->value,
            $lucene_query_model->is_exact,
            $lucene_query_model->is_required);
    }
}