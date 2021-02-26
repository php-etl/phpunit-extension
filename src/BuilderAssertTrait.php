<?php declare(strict_types=1);


namespace Kiboko\Component\PHPUnitExtension;


use Kiboko\Component\PHPUnitExtension\Constraint\Builder\BuilderHasLogger;
use Kiboko\Component\PHPUnitExtension\Constraint\Builder\BuilderProducesAnInstanceOf;
use Kiboko\Component\PHPUnitExtension\Constraint\Builder\ExtractorIteratesAs;
use Kiboko\Component\PHPUnitExtension\Constraint\Builder\LoaderProducesFile;
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

    protected function assertBuilderHasLogger(string $expected, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new BuilderHasLogger($expected), $message);
    }

    protected function assertExtractorIteratesAs(array $expected, DefaultBuilder $builder, string $message = '')
    {
        $this->assertThat($builder, new ExtractorIteratesAs($expected), $message);
    }

    protected function assertLoaderProducesFile(string $expected, string $actual, DefaultBuilder $builder, array $input, string $message = '')
    {
        $this->assertThat($builder, new LoaderProducesFile($expected, $actual, $input), $message);
    }
}
