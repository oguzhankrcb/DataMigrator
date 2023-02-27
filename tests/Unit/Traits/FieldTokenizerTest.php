<?php

namespace Oguzhankrcb\DataMigrator\Tests\Unit\Traits;

use Oguzhankrcb\DataMigrator\Tests\TestCase;
use Oguzhankrcb\DataMigrator\Traits\FieldTokenizer;

class FieldTokenizerTest extends TestCase
{
    use FieldTokenizer;

    /**
     * @test
     *
     * @see \Oguzhankrcb\DataMigrator\Traits\FieldTokenizer::tokenizeField()
     */
    public function it_gets_fields_for_empty_string(): void
    {
        $result = $this->tokenizeField('');
        $this->assertEquals([], $result);
    }

    /**
     * @test
     *
     * @see \Oguzhankrcb\DataMigrator\Traits\FieldTokenizer::tokenizeField()
     */
    public function it_gets_fields_for_single_word(): void
    {
        $result = $this->tokenizeField('foo');
        $this->assertEquals(['foo'], $result);
    }

    /**
     * @test
     *
     * @see \Oguzhankrcb\DataMigrator\Traits\FieldTokenizer::tokenizeField()
     */
    public function it_gets_fields_for_words_with_spaces(): void
    {
        $result = $this->tokenizeField('foo bar baz');
        $this->assertEquals(['foo', ' ', 'bar', ' ', 'baz'], $result);
    }

    /**
     * @test
     *
     * @see \Oguzhankrcb\DataMigrator\Traits\FieldTokenizer::tokenizeField()
     */
    public function it_gets_fields_for_words_with_brackets(): void
    {
        $result = $this->tokenizeField('[foo] [bar]');
        $this->assertEquals(['foo', ' ', 'bar'], $result);
    }

    /**
     * @test
     *
     * @see \Oguzhankrcb\DataMigrator\Traits\FieldTokenizer::tokenizeField()
     */
    public function it_gets_fields_for_words_with_nested_brackets(): void
    {
        $result = $this->tokenizeField('[foo [bar] baz]');
        $this->assertEquals(['foo', ' ', 'bar', ' ', 'baz'], $result);
    }

    /**
     * @test
     *
     * @see \Oguzhankrcb\DataMigrator\Traits\FieldTokenizer::tokenizeField()
     */
    public function it_gets_fields_for_words_with_mixed_brackets(): void
    {
        $result = $this->tokenizeField('[foo] bar [baz]');
        $this->assertEquals(['foo', ' ', 'bar', ' ', 'baz'], $result);
    }

    /**
     * @test
     *
     * @see \Oguzhankrcb\DataMigrator\Traits\FieldTokenizer::tokenizeField()
     */
    public function it_gets_fields_for_words_with_under_score(): void
    {
        $result = $this->tokenizeField('[foo]_ [bar]');
        $this->assertEquals(['foo', '_', ' ', 'bar'], $result);
    }
}
