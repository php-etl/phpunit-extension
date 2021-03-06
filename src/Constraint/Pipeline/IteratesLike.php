<?php declare(strict_types=1);

namespace Kiboko\Component\PHPUnitExtension\Constraint\Pipeline;

use PHPUnit\Framework\Constraint\Constraint;

final class IteratesLike extends Constraint
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

    public function matches($other): bool
    {
        $both = new \MultipleIterator(\MultipleIterator::MIT_NEED_ANY);

        $both->attachIterator($this->asIterator($this->expected));
        $both->attachIterator($this->asIterator($other));

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
            '%s iterates like %s',
            $this->exporter()->export($this->exporter()->toArray($other)),
            $this->exporter()->export($this->exporter()->toArray($this->expected)),
        );
    }

    public function toString(): string
    {
        return 'iterates like';
    }
}
