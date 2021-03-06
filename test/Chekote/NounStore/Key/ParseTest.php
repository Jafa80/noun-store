<?php namespace Chekote\NounStore\Key;

use Chekote\Phake\Phake;
use InvalidArgumentException;

/**
 * @covers \Chekote\NounStore\Key::parse()
 */
class ParseTest extends KeyTest
{
    public function setUp()
    {
        parent::setUp();

        /* @noinspection PhpUndefinedMethodInspection */
        Phake::when($this->key)->parse(Phake::anyParameters())->thenCallParent();
    }

    /**
     * Provides examples of valid key and index pairs with expected parse results.
     *
     * @return array
     */
    public function successScenarioDataProvider()
    {
        return [
        //   key           index   parsedKey,  parsedIndex
            ['Thing',       null, 'Thing',            null], // no nth in key or index param
            ['1st Thing',   null, 'Thing',               0], // 1st in key with no index param
            ['1st Thing',      0, 'Thing',               0], // nth in key with matching index param
            ['2nd Thing',   null, 'Thing',               1], // 2nd in key with no index param
            ['3rd Thing',   null, 'Thing',               2], // 3rd in key with no index param
            ['4th Thing',   null, 'Thing',               3], // 3th in key with no index param
            ['478th Thing', null, 'Thing',             477], // high nth in key with no index param
            ['Thing',          0, 'Thing',               0], // no nth in key with 0 index param
            ['Thing',         49, 'Thing',              49], // no nth in key with high index param
        ];
    }

    /**
     * Tests that calling Key::parse with valid key and index combinations works correctly.
     *
     * @dataProvider successScenarioDataProvider
     * @param string $key         the key to parse
     * @param int    $index       the index to pass along with the key
     * @param string $parsedKey   the expected resulting parsed key
     * @param int    $parsedIndex the expected resulting parsed index
     */
    public function testSuccessScenario($key, $index, $parsedKey, $parsedIndex)
    {
        $this->assertEquals([$parsedKey, $parsedIndex], $this->key->parse($key, $index));
    }

    /**
     * Provides examples of mismatched key & index pairs.
     *
     * @return array
     */
    public function mismatchedKeyAndIndexDataProvider()
    {
        return [
            ['1st Thing', 1],
            ['1st Thing', 2],
            ['4th Person', 0],
            ['4th Person', 4],
            ['4th Person', 10],
        ];
    }

    /**
     * Tests that calling Key::parse with mismatched key and index param throws an exception.
     *
     * @dataProvider mismatchedKeyAndIndexDataProvider
     * @param string $key   the key to parse
     * @param string $index the mismatched index to pass along with the key
     */
    public function testParseKeyThrowsExceptionIfKeyAndIndexMismatch($key, $index)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "$index was provided for index param when key '$key' contains an nth value, but they do not match"
        );

        $this->key->parse($key, $index);
    }
}
