<?php

namespace Botble\Analytics\GA4\Traits;

trait RowOperationTrait
{
    public ?bool $keepEmptyRows = null;

    public ?int $limit = null;

    public ?int $offset = null;

    public function keepEmptyRows(bool $keepEmptyRows = false): self
    {
        $this->keepEmptyRows = $keepEmptyRows;

        return $this;
    }

    public function limit(int $limit = null): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function offset(int $offset = null): self
    {
        $this->offset = $offset;

        return $this;
    }
}
