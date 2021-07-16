<?php declare(strict_types=1);

namespace Kiboko\Component\PHPUnitExtension\Assert;

use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\PipelineExtractsLike;
use Kiboko\Contract\Pipeline\ExtractorInterface;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\Constraint\LogicalNot;

trait ExtractorAssertTrait
{
    abstract public static function assertThat($value, Constraint $constraint, string $message = ''): void;

    protected function assertExtractorExtractsLike(iterable $expected, ExtractorInterface $extractor, string $message = '')
    {
        $this->assertThat($extractor, new PipelineExtractsLike($expected, fn ($item) => new IsEqual($item)), $message);
    }

    protected function assertExtractorDoesNotExtractLike(iterable $expected, ExtractorInterface $extractor, string $message = '')
    {
        $this->assertThat($extractor, new LogicalNot(
            new PipelineExtractsLike($expected, fn ($item) => new IsEqual($item))
        ), $message);
    }

    protected function assertExtractorExtractsExactly(iterable $expected, ExtractorInterface $extractor, string $message = '')
    {
        $this->assertThat($extractor, new PipelineExtractsLike($expected, fn ($item) => new IsIdentical($item)), $message);
    }

    protected function assertExtractorDoesNotExtractExactly(iterable $expected, ExtractorInterface $extractor, string $message = '')
    {
        $this->assertThat($extractor, new LogicalNot(
            new PipelineExtractsLike($expected, fn ($item) => new IsIdentical($item))
        ), $message);
    }
}
