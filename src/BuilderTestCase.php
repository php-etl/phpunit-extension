<?php declare(strict_types=1);

namespace Kiboko\Component\PHPUnitExtension;

use Kiboko\Contract\Pipeline\PipelineRunnerInterface;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

abstract class BuilderTestCase extends TestCase
{
    private ?vfsStreamDirectory $fs = null;

    protected function setUp(): void
    {
        $this->fs = vfsStream::setup();
    }

    protected function tearDown(): void
    {
        $this->fs = null;
    }

    protected function getBuilderCompilePath(): string
    {
        return $this->fs->url();
    }

    public function pipelineRunner(): PipelineRunnerInterface
    {
        return new PipelineRunner();
    }
}
