<?php declare(strict_types=1);

namespace Kiboko\Component\PHPUnitExtension;

use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\IteratesLike;
use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\PipelineExtractsLike;
use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\PipelineLoadsLike;
use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\PipelineTransformsLike;
use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\PipelineWritesFile;
use Kiboko\Contract\Pipeline\ExtractorInterface;
use Kiboko\Contract\Pipeline\LoaderInterface;
use Kiboko\Contract\Pipeline\TransformerInterface;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\LogicalNot;

trait PipelineAssertTrait
{
    abstract public static function assertThat($value, Constraint $constraint, string $message = ''): void;

    protected function assertDoesIterateLike(iterable $expected, iterable $actual, $message = '')
    {
        $this->assertThat($actual, new IteratesLike($expected), $message);
    }

    protected function assertDoesNotIterateLike(iterable $expected, iterable $actual, $message = '')
    {
        $this->assertThat($actual, new LogicalNot(
            new IteratesLike($expected),
        ), $message);
    }

    protected function assertPipelineExtractsLike(iterable $expected, ExtractorInterface $actual, $message = '')
    {
        $this->assertThat($actual, new PipelineExtractsLike($expected), $message);
    }

    protected function assertPipelineNotExtractsLike(iterable $expected, ExtractorInterface $actual, $message = '')
    {
        $this->assertThat($actual, new LogicalNot(
            new PipelineExtractsLike($expected),
        ), $message);
    }

    protected function assertPipelineTransformsLike(iterable $expected, iterable $source, TransformerInterface $actual, $message = '')
    {
        $this->assertThat($actual, new PipelineTransformsLike($source, $expected), $message);
    }

    protected function assertPipelineNotTransformsLike(iterable $expected, iterable $source, TransformerInterface $actual, $message = '')
    {
        $this->assertThat($actual, new LogicalNot(
            new PipelineTransformsLike($source, $expected),
        ), $message);
    }

    protected function assertPipelineLoadsLike(iterable $expected, iterable $source, LoaderInterface $actual, $message = '')
    {
        $this->assertThat($actual, new PipelineLoadsLike($source, $expected), $message);
    }

    protected function assertPipelineNotLoadsLike(iterable $expected, iterable $source, LoaderInterface $actual, $message = '')
    {
        $this->assertThat($actual, new LogicalNot(
            new PipelineLoadsLike($source, $expected)
        ), $message);
    }

    protected function assertPipelineWritesFile(string $expected, iterable $source, LoaderInterface $actual, $message = '')
    {
        $this->assertThat($actual, new PipelineWritesFile($source, $expected), $message);
    }

    protected function assertPipelineNotWritesFile(string $expected, iterable $source, LoaderInterface $actual, $message = '')
    {
        $this->assertThat($actual, new LogicalNot(
            new PipelineWritesFile($source, $expected)
        ), $message);
    }
}
