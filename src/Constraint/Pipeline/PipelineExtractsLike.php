<?php declare(strict_types=1);

namespace Kiboko\Component\PHPUnitExtension\Constraint\Pipeline;

use Kiboko\Component\Pipeline\PipelineRunner;
use Kiboko\Contract\Pipeline\ExtractorInterface;
use Kiboko\Contract\Pipeline\NullRejection;
use Kiboko\Contract\Pipeline\NullState;
use PHPUnit\Framework\Constraint\Constraint;

final class PipelineExtractsLike extends Constraint
{
    /** @var callable */
    private $itemConstraintFactory;

    public function __construct(
        private iterable $expected,
        callable $itemConstraintFactory
    ) {
        $this->itemConstraintFactory = $itemConstraintFactory;
    }

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

    private function passThroughCoroutine(): \Generator
    {
        $line = yield;
        while ($line = yield $line);
    }

    public function matches($other): bool
    {
        $both = new \MultipleIterator(\MultipleIterator::MIT_NEED_ANY);

        $both->attachIterator($this->asIterator($this->expected));

        if (!$other instanceof ExtractorInterface) {
            $this->fail($other, strtr('Expected an instance of %expected%, but got %actual%.', [
                '%expected%' => ExtractorInterface::class,
                '%actual%' => get_debug_type($other),
            ]));
        }

        $runner = new PipelineRunner(null);
        $extract = $other->extract();
        if (is_array($extract)) {
            $iterator = $runner->run(
                new \ArrayIterator($extract),
                $this->passThroughCoroutine(),
                new NullRejection(),
                new NullState(),
            );
        } elseif ($extract instanceof \Iterator) {
            $iterator = $runner->run(
                $extract,
                $this->passThroughCoroutine(),
                new NullRejection(),
                new NullState(),
            );
        } elseif ($extract instanceof \Traversable) {
            $iterator = $runner->run(
                new \IteratorIterator($extract),
                $this->passThroughCoroutine(),
                new NullRejection(),
                new NullState(),
            );
        } else {
            throw new \RuntimeException('Invalid data source, expecting array or Traversable.');
        }
        $both->attachIterator($iterator);

        $index = 0;
        foreach ($both as [$expectedItem, $actualItem]) {
            ++$index;
            $constraint = ($this->itemConstraintFactory)($expectedItem);
            $constraint->evaluate($actualItem, sprintf("Values of Iteration #%d", $index)) !== true;
        }

        return true;
    }

    protected function failureDescription($other): string
    {
        return sprintf(
            '%s pipeline extracts like %s',
            $this->exporter()->export($this->exporter()->toArray($other)),
            $this->exporter()->export($this->exporter()->toArray($this->expected)),
        );
    }

    public function toString(): string
    {
        return 'pipeline extracts like';
    }
}
