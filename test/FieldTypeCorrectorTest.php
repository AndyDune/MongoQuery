<?php
/**
 *
 * PHP version 7.0 and 7.1
 *
 * @package andydune/mongo-query
 * @link  https://github.com/AndyDune/MongoQuery for the canonical source repository
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrey Ryzhov  <info@rznw.ru>
 * @copyright 2018 Andrey Ryzhov
 *
 */


namespace AndyDune\MongoQueryTest;
use AndyDune\DateTime\DateTime;
use AndyDune\MongoQuery\FieldTypeCorrector;
use AndyDune\MongoQuery\Query;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;

class FieldTypeCorrectorTest extends TestCase
{
    public function testFieldMethod()
    {
        $corrector = new FieldTypeCorrector(['number' => 'i']);

        $result = $corrector->correct('number', '123');
        $this->assertTrue(123 === $result);

        $result = $corrector->correct('number_not_corected', '123');
        $this->assertFalse(123 === $result);

        $result = $corrector->correct('number', 'frog');
        $this->assertTrue(0 === $result);


        $result = $corrector->correct('number', null);
        $this->assertTrue(0 === $result);

        $result = $corrector->correct('number', true);
        $this->assertTrue(1 === $result);

        $result = $corrector->correct('number', false);
        $this->assertTrue(0 === $result);

        $result = $corrector->correct('number', []);
        $this->assertTrue(0 === $result);

        $result = $corrector->correct('number', '');
        $this->assertTrue(0 === $result);


        // don touche next fields
        $result = $corrector->correct('string', 'frog');
        $this->assertTrue('frog' === $result);

        $corrector = new FieldTypeCorrector(['date' => 'datetime']);
        $timeNow = time();
        $dt = new UTCDateTime($timeNow * 1000);
        $this->assertEquals($timeNow, $dt->toDateTime()->getTimestamp());
        $dtR = $corrector->correct('date', $timeNow);
        $this->assertEquals($dt->toDateTime(), $dtR->toDateTime());


        $dt = new UTCDateTime(($timeNow - 60) * 1000);
        $dtR = $corrector->correct('date', '-1 minute');
        $this->assertEquals($dt->toDateTime(), $dtR->toDateTime());

        $dt = new UTCDateTime(($timeNow - 61) * 1000);
        $dtR = $corrector->correct('date', new DateTime($timeNow - 61));
        $this->assertEquals($dt->toDateTime(), $dtR->toDateTime());

        $dt = new UTCDateTime(($timeNow - 62) * 1000);
        $dtR = $corrector->correct('date', date('Y-m-d H:i:s', $timeNow - 62));
        $this->assertEquals($dt->toDateTime(), $dtR->toDateTime());


        $dt = new UTCDateTime(($timeNow - 63) * 1000);
        $dtR = $corrector->correct('date', $dt);
        $this->assertEquals($dt->toDateTime(), $dtR->toDateTime());


    }
}