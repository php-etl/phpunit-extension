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

        $node = $other->getNode();

        if (!$node instanceof Node\Expr) {
            $this->fail($other, sprintf('The builder should produce an instance of %s, got %s', Node\Expr::class, get_debug_type($node)));
        }

        try {
            $filename = $this->createFile();

            file_put_contents($filename, $printer->prettyPrintFile([
                new Node\Stmt\Return_($node)
            ]));

            $instance = include $filename;
        } catch (\Error $exception) {
            $this->fail($printer->prettyPrintExpr($node), $exception->getMessage());
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
