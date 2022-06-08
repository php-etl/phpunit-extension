<?php declare(strict_types=1);

namespace Kiboko\Component\PHPUnitExtension\Mock;

use Akeneo\Pim\ApiClient\FileSystem\LocalFileSystem;
use PhpParser\Builder;
use PhpParser\Node;

final class FileSystemBuilder implements Builder
{
    public function getNode(): Node\Expr
    {
        return new Node\Expr\New_(
            class: new Node\Name\FullyQualified(LocalFileSystem::class)
        );
    }
}
