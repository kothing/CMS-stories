<?php

namespace Botble\Media\Storage\BunnyCDN;

class Util
{
    /**
     * Splits a path into a file and a directory
     *
     * @param $path
     * @return array
     */
    public static function splitPathIntoDirectoryAndFile($path): array
    {
        $path = self::endsWith($path, '/') ? substr($path, 0, -1) : $path;
        $sub = explode('/', $path);
        $file = array_pop($sub);
        $directory = implode('/', $sub);

        return [
            'file' => $file,
            'dir' => $directory,
        ];
    }

    /**
     * @param $path
     * @param bool $isDirectory
     * @return string|string[]
     */
    public static function normalizePath($path, bool $isDirectory = false): array|string
    {
        $path = str_replace('\\', '/', $path);

        if ($isDirectory && ! self::endsWith($path, '/')) {
            $path .= '/';
        }

        // Remove double slashes
        while (str_contains($path, '//')) {
            $path = str_replace('//', '/', $path);
        }

        // Remove the starting slash
        if (str_starts_with($path, '/')) {
            $path = substr($path, 1);
        }

        return $path;
    }

    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function startsWith($haystack, $needle): bool
    {
        return str_starts_with($haystack, $needle);
    }

    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function endsWith($haystack, $needle): bool
    {
        $length = strlen($needle);
        if ($length === 0) {
            return true;
        }

        return substr($haystack, -$length) === $needle;
    }
}
