<?php declare(strict_types=1);

namespace Kiboko\Component\PHPUnitExtension\Assert;

use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\PipelineTransformsLike;
use Kiboko\Contract\Pipeline\TransformerInterface;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\Constraint\LogicalNot;

trait TransformerAssertTrait
{
    abstract public static function assertThat($value, Constraint $constraint, string $message = ''): void;

    protected function assertTransformerTransformsLike(iterable $expected, iterable $input, TransformerInterface $transformer, string $message = '')
    {
        $this->assertThat($transformer, new PipelineTransformsLike($expected, $input, fn ($item) => new IsEqual($item)), $message);
    }

    protected function assertTransformerDoesNotTransformLike(iterable $expected, iterable $input, TransformerInterface $transformer, string $message = '')
    {
        $this->assertThat($transformer, new LogicalNot(
            new PipelineTransformsLike($expected, $input, fn ($item) => new IsEqual($item))
        ), $message);
    }

    protected function assertTransformerTransformsExactly(iterable $expected, iterable $input, TransformerInterface $transformer, string $message = '')
    {
        $this->assertThat($transformer, new PipelineTransformsLike($expected, $input, fn ($item) => new IsIdentical($item)), $message);
    }

    protected function assertTransformerDoesNotTransformExactly(iterable $expected, iterable $input, TransformerInterface $transformer, string $message = '')
    {
        $this->assertThat($transformer, new LogicalNot(
            new PipelineTransformsLike($expected, $input, fn ($item) => new IsIdentical($item))
        ), $message);
    }
}
