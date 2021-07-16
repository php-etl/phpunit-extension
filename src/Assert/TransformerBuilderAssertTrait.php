<?php declare(strict_types=1);

namespace Kiboko\Component\PHPUnitExtension\Assert;

use Kiboko\Component\PHPUnitExtension\Constraint\Builder\BuilderProducesCodeThat;
use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\PipelineTransformsLike;
use PhpParser\Builder as DefaultBuilder;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\Constraint\LogicalNot;

trait TransformerBuilderAssertTrait
{
    abstract public static function assertThat($value, Constraint $constraint, string $message = ''): void;

    protected function assertBuildsTransformerTransformsLike(iterable $expected, iterable $input, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new BuilderProducesCodeThat(
            new PipelineTransformsLike($expected, $input, fn ($item) => new IsEqual($item))
        ), $message);
    }

    protected function assertBuildsTransformerDoesNotTransformLike(iterable $expected, iterable $input, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new LogicalNot(
            new BuilderProducesCodeThat(
                new PipelineTransformsLike($expected, $input, fn ($item) => new IsEqual($item))
            ),
        ), $message);
    }

    protected function assertBuildsTransformerTransformsExactly(iterable $expected, iterable $input, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new BuilderProducesCodeThat(
            new PipelineTransformsLike($expected, $input, fn ($item) => new IsIdentical($item))
        ), $message);
    }

    protected function assertBuildsTransformerDoesNotTransformExactly(iterable $expected, iterable $input, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new LogicalNot(
            new BuilderProducesCodeThat(
                new PipelineTransformsLike($expected, $input, fn ($item) => new IsIdentical($item))
            ),
        ), $message);
    }
}
