<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ProjectReportCommand extends Command
{
    protected $signature = 'project:report';
    protected $description = 'Analyze the whole project';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // ----- Models -----
        $this->info('======== Models ========');
        $modelFiles = File::allFiles(app_path('Models'));
        $modelNames = [];
        foreach ($modelFiles as $file) {
            if ($file->getExtension() === 'php') {
                $modelNames[] = $file->getRelativePathname();
            }
        }
        $this->line(implode("\n", $modelNames) ?: 'No models found.');

        // ----- Controllers -----
        $this->info('======== Controllers ========');
        $controllerFiles = File::allFiles(app_path('Http/Controllers'));
        $controllerNames = [];
        foreach ($controllerFiles as $file) {
            if ($file->getExtension() === 'php') {
                $controllerNames[] = $file->getRelativePathname();
            }
        }
        $this->line(implode("\n", $controllerNames) ?: 'No controllers found.');

        // ----- Migrations -----
        $this->info('======== Migrations ========');
        $migrationFiles = glob(database_path('migrations') . '/*.php') ?: [];
        $this->line(implode("\n", array_map('basename', $migrationFiles)) ?: 'No migrations found.');

        // ----- Routes -----
        $this->info('======== Routes ========');
        $this->call('route:list');

        // ----- Migration Status -----
        $this->info('======== Migration Status ========');
        $this->call('migrate:status');

        return 0;
    }
}
