<?php

namespace Eduka\Nereus\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class CourseInstall extends Command
{
    protected $signature = 'eduka:course-install
                            {provider : The course service provider full qualified namespace + class name}
                            {seeder : The seeder full qualified namespace + class name}';

    protected $description = 'Will install the composer package, register the provider, and run the seeder using db:seed';

    public function handle()
    {
        // Register course service provider, to load the migration script.
        app()->register($this->argument('provider'));

        // Run artisan migrate.
        $result = Process::run('php artisan db:seed --class="'.$this->argument('seeder').'"');
        $this->info($result->output());
    }

    protected function executeCommand($command, $path)
    {
        $process = Process::fromShellCommandline($command, $path)->setTimeout(null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }

        $process->run(function ($type, $line) {
            $this->output->write($line);
        });
    }
}
