<?php

namespace Eduka\Nereus\Commands;

use Eduka\Abstracts\Classes\EdukaCommand;
use Eduka\Cube\Models\Course;
use Illuminate\Support\Facades\Artisan;

class Migrate extends EdukaCommand
{
    protected $signature = 'eduka:migrate { canonical? : The course canonical, if not runs for all courses migrations }';

    protected $description = 'Runs a course (or all courses) migrations';

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

        $this->paragraph('-= Run course migrations (artisan migrate) =-', false);

        if ($this->argument('canonical')) {
            // Will have a collection of course instances to run.
            $courses = collect();
            $courses->push(Course::firstWhere('canonical', $this->argument('canonical')));
        } else {
            $courses = Course::all();
        }

        $courses->each(function ($course) {
            $this->paragraph('Registering "'.$course->name.'" service provider...', false);
            $course->registerSelfProvider();
            $this->paragraph('Running "php artisan migrate ...');
            Artisan::call('migrate');
            $this->info(Artisan::output());
        });

        return 0;
    }
}
