<?php

namespace Fabs\CouchDB2;


use Fabs\CouchDB2\Model\LuceneSearchQueryModel;

class LuceneSearchQueryBuilder
{

    const LUCENE_SPECIAL_CHARACTERS =
        ['\\', '+', '*', '?', '(', ')', '{', '}', '[', ']', '^', '-', '||', '&&', '!', '\"', '~', ":"];
    const REGEX_SPECIAL_CHARACTERS =
        ['\\', '+', '*', '?', '(', ')', '{', '}', '[', ']', '^', '-', '|', '.', '$'];


    /**
     * @param string $field
     * @param string $value
     * @param bool $exact
     * @param bool $required
     * @param string[] $allowed_special_characters
     * @return null|\string[]
     */
    public static function build($field, $value, $exact = false, $required = false, $allowed_special_characters = [])
    {
        $clean_query = self::getCleanQuery($value, $allowed_special_characters);
        $escaped_clean_query = self::getEscapedQuery($clean_query);

        $words = explode(' ', $escaped_clean_query);
        $count = count($words);
        for ($i = 0; $i < $count; $i++) {
            if (strlen(trim($words[$i])) === 0) {
                unset($words[$i]);
                continue;
            }

            $words[$i] = $field . ':' . $words[$i];

            if (!$exact) {
                $words[$i] = $words[$i] . '*';
            }

            if ($required) {
                $words[$i] = '+' . $words[$i];
            }
        }

        if (count($words) === 0) {
            return null;
        }

        return implode(' ', $words);
    }


    /**
     * @param string $value
     * @param string[] $allowed_special_characters
     * @return string
     * @author necipallef <necipallef@gmail.com>
     */
    private static function getCleanQuery($value, $allowed_special_characters)
    {
        $default_allowed_char_set = '\sa-zA-Z0-9ğüşıöçĞÜŞİÖÇ';
        $allowed_char_set = $default_allowed_char_set;
        foreach ($allowed_special_characters as $allowed_special_character) {
            if (strpos($allowed_char_set, $allowed_special_character, true) === true) {
                continue;
            }

            if (self::isRegexSpecialCharacter($allowed_special_character) === true) {
                $allowed_char_set .= '\\' . $allowed_special_character;
            } else {
                $allowed_char_set .= $allowed_special_character;
            }
        }

        $regex_string = sprintf('/[^%s]+/', $allowed_char_set);
        $clean_query = preg_replace($regex_string, ' ', $value);
        return $clean_query;
    }


    /**
     * @param string $character
     * @return bool
     * @author necipallef <necipallef@gmail.com>
     */
    private static function isRegexSpecialCharacter($character)
    {
        return in_array($character, self::REGEX_SPECIAL_CHARACTERS, true) === true;
    }


    /**
     * @param string $query
     * @return string
     * @author necipallef <necipallef@gmail.com>
     */
    private static function getEscapedQuery($query)
    {
        $escaped_query = $query;
        foreach (self::LUCENE_SPECIAL_CHARACTERS as $lucene_special_character) {
            if ($lucene_special_character === '||') {
                $escaped_query = preg_replace(
                    '/\|\|/',
                    '\||',
                    $escaped_query);
            } else {
                $escaped_query = preg_replace(
                    self::isRegexSpecialCharacter($lucene_special_character)
                        ? sprintf('/\%s/', $lucene_special_character)
                        : sprintf('/%s/', $lucene_special_character),
                    sprintf('\\%s', $lucene_special_character),
                    $escaped_query);
            }
        }

        return $escaped_query;
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
            $lucene_query_model->is_required,
            $lucene_query_model->allowed_special_characters);
    }
}