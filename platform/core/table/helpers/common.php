<?php

use Illuminate\Database\Eloquent\Model;

if (! function_exists('table_checkbox')) {
    /**
     * @deprecated
     */
    function table_checkbox(int $id): string
    {
        return view('core/table::partials.checkbox', compact('id'))->render();
    }
}

if (! function_exists('table_actions')) {
    function table_actions(?string $edit, ?string $delete, Model $item, ?string $extra = null): string
    {
        return view('core/table::partials.actions', compact('edit', 'delete', 'item', 'extra'))->render();
    }
}
