<?php declare(strict_types=1);

namespace Kiboko\Component\PHPUnitExtension\Assert;

use Kiboko\Component\PHPUnitExtension\Constraint\Builder\BuilderProducesCodeThat;
use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\PipelineExtractsLike;
use PhpParser\Builder as DefaultBuilder;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\Constraint\LogicalNot;

trait ExtractorBuilderAssertTrait
{
    abstract public static function assertThat($value, Constraint $constraint, string $message = ''): void;

    protected function assertBuildsExtractorExtractsLike(iterable $expected, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new BuilderProducesCodeThat(
            new PipelineExtractsLike($expected, fn ($item) => new IsEqual($item))
        ), $message);
    }

    protected function assertBuildsExtractorDoesNotExtractLike(iterable $expected, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new LogicalNot(
            new BuilderProducesCodeThat(
                new PipelineExtractsLike($expected, fn ($item) => new IsEqual($item))
            ),
        ), $message);
    }

    protected function assertBuildsExtractorExtractsExactly(iterable $expected, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new BuilderProducesCodeThat(
            new PipelineExtractsLike($expected, fn ($item) => new IsIdentical($item))
        ), $message);
    }

    protected function assertBuildsExtractorDoesNotExtractExactly(iterable $expected, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new LogicalNot(
            new BuilderProducesCodeThat(
                new PipelineExtractsLike($expected, fn ($item) => new IsIdentical($item))
            ),
        ), $message);
    }
}
