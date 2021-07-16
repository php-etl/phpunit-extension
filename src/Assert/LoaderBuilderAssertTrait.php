<?php declare(strict_types=1);

namespace Kiboko\Component\PHPUnitExtension\Assert;

use Kiboko\Component\PHPUnitExtension\Constraint\Builder\BuilderProducesCodeThat;
use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\PipelineLoadsLike;
use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\PipelineWritesFile;
use PhpParser\Builder as DefaultBuilder;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\Constraint\LogicalNot;

trait LoaderBuilderAssertTrait
{
    abstract public static function assertThat($value, Constraint $constraint, string $message = ''): void;

    protected function assertBuildsLoaderLoadsLike(iterable $expected, iterable $input, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new BuilderProducesCodeThat(
            new PipelineLoadsLike($expected, $input, fn ($item) => new IsEqual($item))
        ), $message);
    }

    protected function assertBuildsLoaderDoesNotLoadLike(iterable $expected, iterable $input, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new LogicalNot(
            new BuilderProducesCodeThat(
                new PipelineLoadsLike($expected, $input, fn ($item) => new IsEqual($item))
            ),
        ), $message);
    }

    protected function assertBuildsLoaderLoadsExactly(iterable $expected, iterable $input, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new BuilderProducesCodeThat(
            new PipelineLoadsLike($expected, $input, fn ($item) => new IsIdentical($item))
        ), $message);
    }

    protected function assertBuildsLoaderDoesNotLoadExactly(iterable $expected, iterable $input, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new LogicalNot(
            new BuilderProducesCodeThat(
                new PipelineLoadsLike($expected, $input, fn ($item) => new IsIdentical($item))
            ),
        ), $message);
    }

    protected function assertBuildsLoaderProducesFile(string $expected, iterable $input, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new BuilderProducesCodeThat(
            new PipelineWritesFile($input, $expected),
        ), $message);
    }

    protected function assertBuildsLoaderDoesNotProduceFile(string $expected, iterable $input, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new LogicalNot(
            new BuilderProducesCodeThat(
                new PipelineWritesFile($input, $expected),
            ),
        ), $message);
    }
}
