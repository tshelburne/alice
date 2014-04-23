<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\Fixtures\Parser\Methods;

use UnexpectedValueException;

use Nelmio\Alice\Fixtures\Parser\Methods\Yaml;

class YamlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Yaml
     **/
    private $parser;

    public function setUp()
    {
        $this->parser = new Yaml(array('value' => 'test'));
    }

    public function testCanParseWillReturnTrueForYamlExtensions()
    {
        $this->assertTrue($this->parser->canParse('test.yaml'));
        $this->assertTrue($this->parser->canParse('test.yml'));
    }

    public function testCanParseWillReturnTrueForYamlExtensionsWithPhpContext()
    {
        $this->assertTrue($this->parser->canParse('test.yaml.php'));
    }

    public function testCanParseWillReturnFalseForNonYamlExtensions()
    {
        $this->assertFalse($this->parser->canParse('test.xml'));
    }

    public function testParseWillExecuteWithASetContext()
    {
        $data = $this->parser->parse(__DIR__.'/../../../support/fixtures/parsers/yamltest.yml.php');

        $this->assertEquals('test', $data['contextual']);
    }

    public function testParseWillReturnAProperDataArray()
    {
        $data = $this->parser->parse(__DIR__.'/../../../support/fixtures/parsers/yamltest.yml.php');

        $this->assertEquals(array('contextual' => 'test', 'username' => '<username()>'), $data);
    }

    public function testIncludeFiles()
    {
        $data = $this->parser->parse(__DIR__.'/../../../support/fixtures/include.yml');

        $expectedData = array(
            'Nelmio\\Alice\\fixtures\\Product' =>
                array(
                    'product_base (template)' =>
                        array(
                            'status' => 'in_stock',
                            'site' => '<word()>',
                            'changed' => 'n',
                            'locked' => '<word()>',
                            'cancelled' => '<word()>',
                            'canBuy' => 'y',
                            'package' => 'n',
                            'price' => '<randomFloat()>',
                            'amount' => 1,
                            'markDeleted' => '<word()>',
                            'paid' => 'y',
                        ),
                    'product1' =>
                        array(
                            'amount' => 45,
                            'paid' => 'n',
                            'user' => '@user0',
                        ),
                    'product0' =>
                        array(
                            'changed' => 'y',
                            'user' => '@user1',
                        ),
                ),
            'Nelmio\\Alice\\fixtures\\Shop' =>
                array(
                    'shop2' =>
                        array(
                            'domain' => 'amazon.com',
                        ),
                    'shop1' =>
                        array(
                            'domain' => 'ebay.com',
                        ),
                ),
            'Nelmio\\Alice\\fixtures\\User' =>
                array(
                    'user_base (template)' =>
                        array(
                            'email' => '<email()>',
                        ),
                ),
        );
        $this->assertEquals($expectedData, $data);
    }

}
