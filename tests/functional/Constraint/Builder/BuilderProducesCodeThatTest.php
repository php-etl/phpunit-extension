<?php

namespace functional\Kiboko\Component\PHPUnitExtension\Constraint\Builder;

use Kiboko\Component\PHPUnitExtension\Constraint\Builder\BuilderProducesCodeThat;
use PHPUnit\Framework\Constraint\IsFalse;
use PHPUnit\Framework\Constraint\IsTrue;
use PHPUnit\Framework\TestCase;
use Vfs\FileSystem;

class BuilderProducesCodeThatTest extends TestCase
{
    private ?FileSystem $fs = null;

    protected function setUp(): void
    {
        $this->fs = FileSystem::factory('vfs://');
        $this->fs->mount();
    }

    protected function tearDown(): void
    {
        $this->fs->unmount();
        $this->fs = null;
    }

    public function testEvaluateReturnTrue(): void
    {
        $constraint = new BuilderProducesCodeThat(new IsTrue());

        $this->assertTrue($constraint->evaluate(new BuilderStub(), returnResult: true));
    }

    public function testEvaluateReturnFalse(): void
    {
        $constraint = new BuilderProducesCodeThat(new IsFalse());

        $this->assertFalse($constraint->evaluate(new BuilderStub(), returnResult: true));
    }

    public function testCountIsOne(): void
    {
        $constraint = new BuilderProducesCodeThat(new IsTrue());

        $this->assertCount(1, $constraint);
    }
}
