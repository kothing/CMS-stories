<?php

if (! function_exists('add_meta_box')) {
    /**
     * @deprecated since 5.7
     */
    function add_meta_box(
        string $id,
        string $title,
        callable $callback,
        ?string $screen = null,
        string $context = 'advanced',
        string $priority = 'default',
        $callbackArgs = null
    ): void {
        MetaBox::addMetaBox($id, $title, $callback, $screen, $context, $priority, $callbackArgs);
    }
}

if (! function_exists('get_meta_data')) {
    /**
     * @deprecated since 5.7
     */
    function get_meta_data($object, string $key, bool $single = false, array $select = ['meta_value'])
    {
        return MetaBox::getMetaData($object, $key, $single, $select);
    }
}

if (! function_exists('get_meta')) {
    /**
     * @deprecated since 5.7
     */
    function get_meta($object, string $key, array $select = ['meta_value'])
    {
        return MetaBox::getMeta($object, $key, $select);
    }
}

if (! function_exists('save_meta_data')) {
    /**
     * @deprecated since 5.7
     */
    function save_meta_data($object, string $key, string $value, array $options = null): bool
    {
        return MetaBox::saveMetaBoxData($object, $key, $value, $options);
    }
}

if (! function_exists('delete_meta_data')) {
    /**
     * @deprecated since 5.7
     */
    function delete_meta_data($object, string $key)
    {
        return MetaBox::deleteMetaData($object, $key);
    }
}
