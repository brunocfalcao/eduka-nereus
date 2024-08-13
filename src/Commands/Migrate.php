<?php

namespace Eduka\Nereus\Commands;

use Eduka\Abstracts\Classes\EdukaCommand;
use Eduka\Cube\Models\Course;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Console\Output\BufferedOutput;

class Migrate extends EdukaCommand
{
    protected $signature = 'eduka:migrate 
                            {--package= : The course package value, defined in eduka.php config}';

    protected $description = 'Runs a course migration, or all courses migrations defined in eduka config';

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

        $courses = $this->option('package') ?
                        config('eduka.courses.'.$this->option('package')) :
                        config('eduka.courses');

        foreach ($courses as $package => $course) {
            // Boot course service provider.
            $this->info('Booting '.$package.' service provider ...');
            app()->register($course['provider-class']);

            // Run php artisan migrate.
            $this->info('Running PHP artisan migrate ...');

            $output = new BufferedOutput;
            Artisan::call('migrate', [], $output);
            $this->info($output->fetch());

            //$result = Process::run('php artisan migrate');
            //$this->info($result->output());
        }

        $this->paragraph('All done!');

        return 0;
    }
}
