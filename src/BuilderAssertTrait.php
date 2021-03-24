<?php declare(strict_types=1);


namespace Kiboko\Component\PHPUnitExtension;


use Kiboko\Component\PHPUnitExtension\Constraint\Builder\BuilderProducesAnInstanceOf;
use Kiboko\Component\PHPUnitExtension\Constraint\Builder\BuilderProducesCodeThat;
use Kiboko\Component\PHPUnitExtension\Constraint\Builder\ExtractorIteratesAs;
use Kiboko\Component\PHPUnitExtension\Constraint\Builder\LoaderProducesFile;
use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\IteratesLike;
use Kiboko\Component\PHPUnitExtension\Constraint\Pipeline\PipelineLoadLike;
use PhpParser\Builder as DefaultBuilder;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\LogicalNot;

trait BuilderAssertTrait
{
    abstract public static function assertThat($value, Constraint $constraint, string $message = ''): void;

    protected function assertBuilderProducesAnInstanceOf(string $expected, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new BuilderProducesAnInstanceOf($expected), $message);
    }

    protected function assertBuilderNotProducesAnInstanceOf(string $expected, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new LogicalNot(new BuilderProducesAnInstanceOf($expected),), $message);
    }

    protected function assertExtractorIteratesAs(array $expected, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new ExtractorIteratesAs($expected), $message);
    }

    protected function assertLoaderProducesFile(string $expected, string $actual, DefaultBuilder $builder, array $input, string $message = '')
    {
        $this->assertThat($builder, new LoaderProducesFile($expected, $actual, $input), $message);
    }

    protected function assertBuilderProducesAnExtractorThatIteratesLike(iterable $expected, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new BuilderProducesCodeThat(new IteratesLike($expected)), $message);
    }

    protected function assertBuilderProducesAnExtractorThatDontIteratesLike(iterable $expected, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new LogicalNot(new BuilderProducesCodeThat(new IteratesLike($expected))), $message);
    }

    protected function assertBuilderProducesAPipelineLoadingLike(iterable $expected, iterable $actual, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new BuilderProducesCodeThat(new PipelineLoadLike($actual, $expected)), $message);
    }

    protected function assertBuilderProducesAPipelineNotLoadingLike(iterable $expected, iterable $actual, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new LogicalNot(new BuilderProducesCodeThat(new PipelineLoadLike($actual, $expected))), $message);
    }
}
