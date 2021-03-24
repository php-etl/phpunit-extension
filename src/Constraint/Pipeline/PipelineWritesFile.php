<?php declare(strict_types=1);

namespace Kiboko\Component\PHPUnitExtension\Constraint\Pipeline;

use Kiboko\Component\Pipeline\PipelineRunner;
use Kiboko\Contract\Pipeline\FlushableInterface;
use Kiboko\Contract\Pipeline\LoaderInterface;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\FileExists;

final class PipelineWritesFile extends Constraint
{
    public function __construct(
        private iterable $source,
        private string $expected,
    ) {}

    private function asIterator(iterable $iterable): \Iterator
    {
        if (is_array($iterable)) {
            return new \ArrayIterator($iterable);
        }
        if (!$iterable instanceof \Iterator && $iterable instanceof \Traversable) {
            return new \IteratorIterator($iterable);
        }
        if ($iterable instanceof \Iterator) {
            return $iterable;
        }

        throw new \InvalidArgumentException();
    }

    public function matches($other): bool
    {
        if (!$other instanceof LoaderInterface) {
            $this->fail($other, strtr('Expected an instance of %expected%, but got %actual%.', [
                '%expected%' => LoaderInterface::class,
                '%actual%' => get_debug_type($other),
            ]));
        }

        $runner = new PipelineRunner(null);
        if ($other instanceof FlushableInterface) {
            $iterator = new \AppendIterator();

            $iterator->append(
                $runner->run($this->asIterator($this->source), $other->load())
            );
            $iterator->append(
                $runner->run(
                    new \ArrayIterator([null]),
                    (function () use ($other): \Generator {
                        yield;
                        yield $other->flush();
                    })()
                )
            );
        } else {
            $iterator = $runner->run($this->asIterator($this->source), $other->load());
        }

        iterator_count($iterator);

        $constraint = new FileExists();
        $constraint->evaluate($this->expected);

        return true;
    }

    protected function failureDescription($other): string
    {
        return sprintf(
            '%s pipeline loads like %s',
            $this->exporter()->export($this->exporter()->toArray($other)),
            $this->exporter()->export($this->exporter()->toArray($this->expected)),
        );
    }

    public function toString(): string
    {
        return 'pipeline loads like';
    }
}
