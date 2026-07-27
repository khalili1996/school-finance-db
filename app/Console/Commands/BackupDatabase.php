<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'تهیه پشتیبان از دیتابیس';

    public function handle()
    {
        $filename = 'backup-' . now()->format('Y-m-d-His') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        // تنظیمات دیتابیس از config
        $db = config('database.connections.mysql');
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            $db['username'],
            $db['password'],
            $db['host'],
            $db['database'],
            $path
        );

        $process = Process::fromShellCommandline($command);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('بکاپ‌گیری ناموفق بود.');
            return 1;
        }

        $this->info('بکاپ با موفقیت در مسیر زیر ذخیره شد: ' . $path);
        return 0;
    }
}
