<?php

namespace Eduka\Nereus\Commands;

use Eduka\Abstracts\Classes\EdukaCommand;
use Eduka\Cube\Models\Course;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Console\Output\BufferedOutput;

class Update extends EdukaCommand
{
    protected $signature = 'eduka:update';

    protected $description = 'Runs several eduka update commands to refresh the codebase directly on the server';

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

        $commands = [
            'composer update',
            'php artisan eduka:fresh',
            'php artisan optimize:clear',
            'php artisan queue:restart'
        ];

        foreach ($commands as $command) {
            $this->paragraph('Running "' . $command . '"');
            $result = Process::run($command);
            $this->info($result->output());
            $this->paragraph('--- Done ---');
        }

        return 0;
    }
}
