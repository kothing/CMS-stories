<?php

namespace Botble\Table;

use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

class TableBuilder
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function create(string $tableClass): TableAbstract
    {
        if (! class_exists($tableClass)) {
            throw new InvalidArgumentException(
                'Table class with name ' . $tableClass . ' does not exist.'
            );
        }

        return $this->container->make($tableClass);
    }
}
