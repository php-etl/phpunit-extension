<?php

declare(strict_types=1);

namespace Kiboko\Component\PHPUnitExtension\Constraint\Pipeline;

use Kiboko\Contract\Pipeline\FlushableInterface;
use Kiboko\Contract\Pipeline\NullRejection;
use Kiboko\Contract\Pipeline\NullState;
use Kiboko\Contract\Pipeline\PipelineRunnerInterface;
use Kiboko\Contract\Pipeline\TransformerInterface;
use PHPUnit\Framework\Constraint\Constraint;

/** @template Type */
final class PipelineTransformsLike extends Constraint
{
    /** @var callable */
    private $itemConstraintFactory;

    /**
     * @param list<Type> $source
     * @param list<Type> $expected
     */
    public function __construct(
        private iterable $source,
        private iterable $expected,
        callable $itemConstraintFactory,
        private PipelineRunnerInterface $runner,
    ) {
        $this->itemConstraintFactory = $itemConstraintFactory;
    }

    /**
     * @param list<Type> $iterable
     * @return \Iterator<Type>
     */
    private function asIterator(iterable $iterable): \Iterator
    {
        if (is_array($iterable)) {
            return new \ArrayIterator($iterable);
        }
        if ($iterable instanceof \Iterator) {
            return $iterable;
        }
        return new \IteratorIterator($iterable);
    }

    public function matches($other): bool
    {
        $both = new \MultipleIterator(\MultipleIterator::MIT_NEED_ANY);

        $both->attachIterator($this->asIterator($this->expected));

        if (!$other instanceof TransformerInterface) {
            $this->fail($other, strtr('Expected an instance of %expected%, but got %actual%.', [
                '%expected%' => TransformerInterface::class,
                '%actual%' => get_debug_type($other),
            ]));
        }

        if ($other instanceof FlushableInterface) {
            $iterator = new \AppendIterator();

            $iterator->append(
                $this->runner->run(
                    $this->asIterator($this->source),
                    $other->transform(),
                    new NullRejection(),
                    new NullState(),
                )
            );
            $iterator->append(
                $this->runner->run(
                    new \ArrayIterator([[]]),
                    (function () use ($other): \Generator {
                        yield;
                        yield $other->flush();
                    })(),
                    new NullRejection(),
                    new NullState(),
                )
            );
        } else {
            $iterator = $this->runner->run(
                $this->asIterator($this->source),
                $other->transform(),
                new NullRejection(),
                new NullState(),
            );
        }
        $both->attachIterator($iterator);

        $index = 0;
        foreach ($both as [$expectedItem, $actualItem]) {
            ++$index;
            $constraint = ($this->itemConstraintFactory)($expectedItem);
            $constraint->evaluate($actualItem, sprintf("Values of Iteration #%d", $index)) !== true;
        }

        return !$iterator->valid();
    }

    protected function failureDescription($other): string
    {
        return sprintf(
            '%s pipeline transforms like %s',
            $this->exporter()->export($this->exporter()->toArray($other)),
            $this->exporter()->export($this->exporter()->toArray($this->expected)),
        );
    }

    public function toString(): string
    {
        return 'pipeline transforms like';
    }
}
