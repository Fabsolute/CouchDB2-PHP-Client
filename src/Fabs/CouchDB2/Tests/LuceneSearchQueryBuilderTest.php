<?php

namespace Fabs\CouchDB2\Tests;

use Fabs\CouchDB2\LuceneSearchQueryBuilder;
use Fabs\CouchDB2\Model\LuceneSearchQueryModel;

class LuceneSearchQueryBuilderTest extends TestBase
{

    public function testBuildRequired()
    {
        $model = (new LuceneSearchQueryModel('gender', 'male', true, true));
        $search_query = LuceneSearchQueryBuilder::buildFromModel($model);
        self::assertEquals('+gender:male', $search_query);
    }


    public function testBuildNotRequired()
    {
        $model = (new LuceneSearchQueryModel('gender', 'male', true, false));
        $search_query = LuceneSearchQueryBuilder::buildFromModel($model);
        self::assertEquals('gender:male', $search_query);
    }


    public function testBuildExact()
    {
        $model = (new LuceneSearchQueryModel('gender', 'male', true, false));
        $search_query = LuceneSearchQueryBuilder::buildFromModel($model);
        self::assertEquals('gender:male', $search_query);
    }


    public function testBuildNotExact()
    {
        $model = (new LuceneSearchQueryModel('gender', 'male', false, false));
        $search_query = LuceneSearchQueryBuilder::buildFromModel($model);
        self::assertEquals('gender:male*', $search_query);
    }


    public function testBuildExactRequired()
    {
        $model = (new LuceneSearchQueryModel('gender', 'male', true, true));
        $search_query = LuceneSearchQueryBuilder::buildFromModel($model);
        self::assertEquals('+gender:male', $search_query);
    }


    public function testBuildRequiredNotExact()
    {
        $model = (new LuceneSearchQueryModel('gender', 'male', false, true));
        $search_query = LuceneSearchQueryBuilder::buildFromModel($model);
        self::assertEquals('+gender:male*', $search_query);
    }


    public function testBuildDotAllowed()
    {
        $model = (new LuceneSearchQueryModel('ip', '127.0.0.1', false, true))
            ->addAllowedSpecialCharacter('.');
        $search_query = LuceneSearchQueryBuilder::buildFromModel($model);
        self::assertEquals('+ip:127.0.0.1*', $search_query);
    }


    public function testBuildDotNotAllowed()
    {
        $model = (new LuceneSearchQueryModel('ip', '127.0.0.1', false, true));
        $search_query = LuceneSearchQueryBuilder::buildFromModel($model);
        self::assertEquals('+ip:127* +ip:0* +ip:0* +ip:1*', $search_query);
    }


    public function testBuildEscapeParenthesis()
    {
        $model = (new LuceneSearchQueryModel('name', 'AK47 (BattleScarred)', false, true))
            ->addAllowedSpecialCharacter('(')
            ->addAllowedSpecialCharacter(')');
        $search_query = LuceneSearchQueryBuilder::buildFromModel($model);
        self::assertEquals('+name:AK47* +name:\(BattleScarred\)*', $search_query);
    }
}