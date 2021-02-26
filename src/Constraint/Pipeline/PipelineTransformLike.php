<?php declare(strict_types=1);

namespace Kiboko\Component\PHPUnitExtension\Constraint\Pipeline;

use Kiboko\Component\Pipeline\PipelineRunner;
use Kiboko\Contract\Pipeline\TransformerInterface;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsIdentical;

class PipelineTransformLike extends Constraint
{
    public function __construct(private iterable $source, private iterable $expected)
    {}

    private function asIterator(iterable $iterable): \Iterator
    {
        if (is_array($iterable)) {
            return new \ArrayIterator($iterable);
        }
        if (!$iterable instanceof \Iterator && $iterable instanceof \Traversable) {
            return new \IteratorIterator($iterable);
        }
        return $iterable;
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

        $runner = new PipelineRunner(null);
        $both->attachIterator($runner->run($this->asIterator($this->source), $other->transform()));

        $index = 0;
        foreach ($both as [$expectedItem, $actualItem]) {
            ++$index;
            $constraint = new IsIdentical($expectedItem);
            $constraint->evaluate($actualItem, sprintf("Values of Iteration #%d", $index)) !== true;
        }

        return true;
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
