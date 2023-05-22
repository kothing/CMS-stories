<?php

namespace Botble\Setting\Supports;

use Illuminate\Filesystem\Filesystem;

class JsonSettingStore extends SettingStore
{
    protected Filesystem $files;

    protected ?string $path = null;

    public function __construct(Filesystem $files, ?string $path = null)
    {
        $this->files = $files;
        $this->setPath($path ?: storage_path('settings.json'));
    }

    /**
     * Set the path for the JSON file.
     */
    public function setPath(string $path)
    {
        // If the file does not already exist, we will attempt to create it.
        if (! $this->files->exists($path)) {
            $result = $this->files->put($path, '{}');
            if ($result === false) {
                info('Could not write to ' . $path);
            }
        }

        if (! $this->files->isWritable($path)) {
            info($path . ' is not writable.');
        }

        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    protected function read(): array
    {
        $contents = $this->files->get($this->path);

        $data = json_decode($contents, true);

        if ($data === null) {
            info('Invalid JSON in ' . $this->path);

            return [];
        }

        return $data;
    }

    protected function write(array $data): void
    {
        $contents = '{}';
        if ($data) {
            $contents = json_encode($data);
        }

        $this->files->put($this->path, $contents);
    }
}
