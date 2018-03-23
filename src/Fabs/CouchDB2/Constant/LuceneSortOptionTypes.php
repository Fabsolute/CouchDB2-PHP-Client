<?php

namespace Fabs\CouchDB2\Constant;
class LuceneSortOptionTypes
{
    const STRING = 'string';
    const DOUBLE = 'double';
    const FLOAT = 'float';
    const LONG = 'long';
    const DATE = 'date';
    const INT = 'int';

    const ALL =
        [
            LuceneSortOptionTypes::STRING,
            LuceneSortOptionTypes::DOUBLE,
            LuceneSortOptionTypes::FLOAT,
            LuceneSortOptionTypes::LONG,
            LuceneSortOptionTypes::DATE,
            LuceneSortOptionTypes::INT
        ];
}