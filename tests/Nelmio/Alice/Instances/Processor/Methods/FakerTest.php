<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\Instances\Processor\Methods;

use Nelmio\Alice\Instances\Processor\Processable;
use Nelmio\Alice\support\extensions\CustomProvider;

class FakerTest extends \PHPUnit_Framework_TestCase
{
    public function buildFaker()
    {
        return new Faker(array());
    }

    public function process($value, $variables = array())
    {
        return $this->buildFaker()->process(new Processable($value), $variables);
    }

    public function testCanProcessStrings()
    {
        $this->assertTrue($this->buildFaker()->canProcess(new Processable('string')));
    }

    public function testWillNotAffectNonFakerStrings()
    {
        $res = $this->process('just a string');

        $this->assertEquals('just a string', $res);
    }

    public function testProcessesFakerData()
    {
        $res = $this->process('<firstName()>');

        $this->assertNotEquals('<firstName()>', $res);
        $this->assertNotEmpty($res);
    }

    public function testProcessesFakerDataMultiple()
    {
        $res = $this->process('<firstName()> <lastName()>');

        $this->assertNotEquals('<firstName()> <lastName()>', $res);
        $this->assertRegExp('{^[\w\']+ [\w\']+$}i', $res);
    }

    public function testProcessesFakerDataWithArgs()
    {
        $res = $this->process('<dateTimeBetween("yesterday", "tomorrow")>');

        $this->assertInstanceOf('DateTime', $res);
        $this->assertGreaterThanOrEqual(strtotime("yesterday"), $res->getTimestamp());
        $this->assertLessThanOrEqual(strtotime("tomorrow"), $res->getTimestamp());
    }

    public function testProcessesFakerDataWithPhpArgs()
    {
        $res = $this->process('<dateTimeBetween("yest"."erday", strrev("omot")."rrow")>');

        $this->assertInstanceOf('DateTime', $res);
        $this->assertGreaterThanOrEqual(strtotime("yesterday"), $res->getTimestamp());
        $this->assertLessThanOrEqual(strtotime("tomorrow"), $res->getTimestamp());
    }

    public function testProcessesVariables()
    {
        $res = $this->process('<dateTimeBetween($start, "-9days")>', array('start' => '-20days'));

        $this->assertInstanceOf('DateTime', $res);
        $this->assertGreaterThanOrEqual(strtotime("-20days"), $res->getTimestamp());
        $this->assertLessThanOrEqual(strtotime("-9days"), $res->getTimestamp());
    }

    public function testProcessesFakerDataWithLocale()
    {
        $res = $this->process('<fr_FR:siren()>');

        $this->assertRegExp('{^\d{3} \d{3} \d{3}$}', $res);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unknown formatter "siren"
     */
    public function testProcessesFakerDataUsesDefaultLocale()
    {
        $res = $this->process('<siren()>');
    }

    public function testCanAddCustomProviders()
    {
        $faker = new Faker(array( new CustomProvider ));
        $res = $faker->process(new Processable('<fooGenerator()>'), array());

        $this->assertEquals('foo', $res);
    }

}
