<?php

namespace Botble\Base\Commands;

use Botble\Backup\Supports\MySql\MySqlDump;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Process\Process;

#[AsCommand('cms:db:export', 'Export database to SQL file.')]
class ExportDatabaseCommand extends Command
{
    public function handle(): int
    {
        $config = config('database.connections.mysql', []);

        if (! $config) {
            return false;
        }

        $sqlPath = base_path('database.sql');

        $sql = 'mysqldump --user="' . $config['username'] . '" --password="' . $config['password'] . '"';

        $sql .= ' --host=' . $config['host'] . ' --port=' . $config['port'] . ' ' . $config['database'] . ' > ' . $sqlPath;

        try {
            Process::fromShellCommandline($sql)->mustRun();
        } catch (Exception) {
            try {
                system($sql);
            } catch (Exception) {
                $this->processMySqlDumpPHP($sqlPath, $config);
            }
        }

        if (! File::exists($sqlPath) || File::size($sqlPath) < 1024) {
            $this->processMySqlDumpPHP($sqlPath, $config);
        }

        $this->components->info('Exported database to SQL file successfully!');

        return self::SUCCESS;
    }

    protected function processMySqlDumpPHP(string $path, array $config): bool
    {
        (new MySqlDump(
            'mysql:host=' . $config['host'] . ';dbname=' . $config['database'],
            $config['username'],
            $config['password']
        ))->start($path);

        return true;
    }
}
