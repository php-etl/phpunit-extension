<?php


namespace Kiboko\Component\PHPUnitExtension\Constraint\Builder;


use PhpParser\Builder;
use PhpParser\Node;
use PhpParser\PrettyPrinter;
use PHPUnit\Framework\Constraint\UnaryOperator;
use PHPUnit\Framework\InvalidArgumentException;

final class BuilderProducesCodeThat extends UnaryOperator
{
    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param Builder $other value or object to evaluate
     * @return bool
     * @throws \Exception
     */
    protected function matches($other): bool
    {
        if (!$other instanceof Builder) {
            throw InvalidArgumentException::create(
                1,
                Builder::class,
            );
        }

        $printer = new PrettyPrinter\Standard();

        try {
            $filename = $this->createFile();

            file_put_contents($filename, $printer->prettyPrintFile([
                new Node\Stmt\Return_($other->getNode())
            ]));

            $instance = include $filename;
        } catch (\Error $exception) {
            $this->fail($printer->prettyPrintExpr($other->getNode()), $exception->getMessage());
        }

        return $this->constraint()->evaluate($instance, '', true);
    }

    private function createFile(): string
    {
        return 'vfs://' . hash('sha512', random_bytes(512)) .'.php';
    }

    public function operator(): string
    {
        return 'compiles';
    }

    public function precedence(): int
    {
        return 10;
    }
}
