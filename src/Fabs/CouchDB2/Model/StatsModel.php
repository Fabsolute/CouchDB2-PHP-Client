<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 21/07/2017
 * Time: 15:19
 */

namespace Fabs\CouchDB2\Model;


use Fabs\Serialize\SerializableObject;

class StatsModel extends SerializableObject
{
    /** @var float|int */
    public $sum = 0;
    /** @var float|int */
    public $count = 0;
    /** @var float|int */
    public $min = 0;
    /** @var float|int */
    public $max = 0;
    /** @var float|int */
    public $sumsqr = 0;
}