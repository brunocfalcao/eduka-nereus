<?php

namespace Eduka\Nereus\Commands;

use Eduka\Abstracts\Classes\EdukaCommand;
use Eduka\Cube\Models\Course;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Console\Output\BufferedOutput;

class Fresh extends EdukaCommand
{
    protected $signature = 'eduka:fresh';

    protected $description = 'Refreshes the eduka database, re-runs all courses migrations and publishes their respective assets';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('
                    _       _
             ___  _| | _ _ | |__ ___
            / ._>/ . || | || / /<_> |
            \___.\___|`___||_\_\<___|
        ');

        $courses = config('eduka.courses');

        // Deleting all file contents under the storage/app/public.
        $this->info('Deleting files under storage/app/public...');
        File::cleanDirectory(storage_path('app/public'));

        // Run php artisan migrate:fresh.
        $this->info('Running PHP artisan migrate:fresh for eduka database...');
        $result = Process::run('php artisan migrate:fresh --force');
        $this->info($result->output());

        foreach ($courses as $package => $course) {
            // Boot course service provider.
            $this->paragraph('Booting '.$package.' service provider ...');

            if (class_exists($course['provider-class'])) {
                app()->register($course['provider-class']);

                // Run php artisan migrate.
                $this->info('Running PHP artisan migrate (for seeders)...');

                $output = new BufferedOutput();
                Artisan::call('migrate', [], $output);
                $this->info($output->fetch());

                // Run php artisan vendor:publish for the respective service provider.
                $this->info('Publishing course assets for service provider '.$course['provider-class'].'...');
                $result = Process::run('php artisan vendor:publish --force --provider="'.$course['provider-class'].'"');
                $this->info($result->output());
            } else {
                $this->error('Service provider is not autoloaded. Skipping...');
            }
        }

        $this->paragraph('All done!');

        return 0;
    }
}
