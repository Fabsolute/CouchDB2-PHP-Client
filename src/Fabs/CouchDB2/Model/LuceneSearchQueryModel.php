<?php

namespace Fabs\CouchDB2\Model;


class LuceneSearchQueryModel
{
    /** @var string */
    public $field = null;
    /** @var string */
    public $value = null;
    /** @var string */
    public $is_exact = false;
    /** @var string */
    public $is_required = false;
    /** @var string[] */
    public $allowed_special_characters = [];

    /**
     * LuceneSearchQueryModel constructor.
     * @param string $field
     * @param string $value
     * @param bool $is_exact
     * @param bool $is_required
     */
    function __construct($field, $value, $is_exact = true, $is_required = true)
    {
        $this->field = $field;
        $this->value = $this->convertQueryValueToTurkishLowercase($value);
        $this->is_exact = $is_exact;
        $this->is_required = $is_required;
    }


    /**
     * @param string[] $new_allowed_special_characters
     * @return LuceneSearchQueryModel
     * @author necipallef <necipallef@gmail.com>
     */
    public function addAllowedSpecialCharacters($new_allowed_special_characters)
    {
        foreach ($new_allowed_special_characters as $allowed_special_character) {
            $this->addAllowedSpecialCharacter($allowed_special_character);
        }

        return $this;
    }


    /**
     * @param string $allowed_special_character
     * @return LuceneSearchQueryModel
     * @author necipallef <necipallef@gmail.com>
     */
    public function addAllowedSpecialCharacter($allowed_special_character)
    {
        if (in_array($allowed_special_character, $this->allowed_special_characters, true) === false) {
            $this->allowed_special_characters[] = $allowed_special_character;
        }

        return $this;
    }

    /**
     * @param string $value
     * @return string
     */
    private function convertQueryValueToTurkishLowercase($value)
    {
        $cleaned_char_list = [];
        $char_lookup = ['I' => 'ı', 'İ' => 'i', 'Ş' => 'ş', 'Ö' => 'ö', 'Ğ' => 'ğ', 'Ü' => 'ü'];
        $tmp_arr = preg_split('~~u', $value, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($tmp_arr as $chr) {
            if (array_key_exists($chr, $char_lookup) === true) {
                $cleaned_char_list[] = $char_lookup[$chr];
            } else {
                $cleaned_char_list[] = $chr;
            }
        }
        return strtolower(implode($cleaned_char_list));
    }
}
