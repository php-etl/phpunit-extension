<?php declare(strict_types=1);

namespace Kiboko\Component\PHPUnitExtension;

use Kiboko\Component\PHPUnitExtension\Constraint\Builder\BuilderProducesCodeThat;
use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\IteratesLike;
use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\PipelineExtractsLike;
use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\PipelineLoadsLike;
use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\PipelineTransformsLike;
use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\PipelineWritesFile;
use PhpParser\Builder as DefaultBuilder;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\Constraint\LogicalNot;

trait BuilderAssertTrait
{
    abstract public static function assertThat($value, Constraint $constraint, string $message = ''): void;

    protected function assertBuilderProducesInstanceOf(string $expected, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new BuilderProducesCodeThat(
            new IsInstanceOf($expected)
        ), $message);
    }

    protected function assertBuilderProducesNotInstanceOf(string $expected, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new LogicalNot(
            new BuilderProducesCodeThat(
                new IsInstanceOf($expected)
            ),
        ), $message);
    }

    protected function assertBuilderProducesExtractorIteratesAs(array $expected, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new BuilderProducesCodeThat(
            new PipelineExtractsLike($expected)
        ), $message);
    }

    protected function assertBuilderProducesExtractorNotIteratesAs(array $expected, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new LogicalNot(
            new BuilderProducesCodeThat(
                new PipelineExtractsLike($expected)
            ),
        ), $message);
    }

    protected function assertBuilderProducesPipelineExtractingLike(iterable $expected, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new BuilderProducesCodeThat(
            new IteratesLike($expected)
        ), $message);
    }

    protected function assertBuilderProducesPipelineNotExtractingLike(iterable $expected, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new LogicalNot(
            new BuilderProducesCodeThat(
                new IteratesLike($expected)
            ),
        ), $message);
    }

    protected function assertBuilderProducesPipelineTransformingLike(iterable $expected, iterable $actual, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new BuilderProducesCodeThat(
            new PipelineTransformsLike($actual, $expected)
        ), $message);
    }

    protected function assertBuilderProducesPipelineNotTransformingLike(iterable $expected, iterable $actual, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new LogicalNot(
            new BuilderProducesCodeThat(
                new PipelineTransformsLike($actual, $expected)
            ),
        ), $message);
    }

    protected function assertBuilderProducesPipelineLoadingLike(iterable $expected, iterable $actual, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new BuilderProducesCodeThat(
            new PipelineLoadsLike($actual, $expected)
        ), $message);
    }

    protected function assertBuilderProducesPipelineNotLoadingLike(iterable $expected, iterable $actual, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new LogicalNot(
            new BuilderProducesCodeThat(
                new PipelineLoadsLike($actual, $expected)
            ),
        ), $message);
    }

    protected function assertBuilderProducesLoaderWritingFile(string $expected, array $source, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new BuilderProducesCodeThat(
            new PipelineWritesFile($source, $expected),
        ), $message);
    }

    protected function assertBuilderProducesLoaderNotWritingFile(string $expected, array $source, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new LogicalNot(
            new BuilderProducesCodeThat(
                new PipelineWritesFile($source, $expected),
            ),
        ), $message);
    }
}
