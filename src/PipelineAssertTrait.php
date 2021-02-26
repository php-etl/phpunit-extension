<?php declare(strict_types=1);

namespace Kiboko\Component\PHPUnitExtension;

use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\IteratesLike;
use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\PipelineLoadLike;
use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\PipelineTransformLike;
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
        $this->assertThat($actual, new LogicalNot(new IteratesLike($expected)), $message);
    }

    protected function assertPipelineDoesTransformLike(iterable $source, iterable $expected, TransformerInterface $actual, $message = '')
    {
        $this->assertThat($actual, new PipelineTransformLike($source, $expected), $message);
    }

    protected function assertPipelineDoesNotTransformLike(iterable $source, iterable $expected, TransformerInterface $actual, $message = '')
    {
        $this->assertThat($actual, new LogicalNot(new PipelineTransformLike($source, $expected)), $message);
    }

    protected function assertPipelineDoesLoadLike(iterable $source, iterable $expected, LoaderInterface $actual, $message = '')
    {
        $this->assertThat($actual, new PipelineLoadLike($source, $expected), $message);
    }

    protected function assertPipelineDoesNotLoadLike(iterable $source, iterable $expected, LoaderInterface $actual, $message = '')
    {
        $this->assertThat($actual, new LogicalNot(new PipelineLoadLike($source, $expected)), $message);
    }
}
