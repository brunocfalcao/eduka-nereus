<?php

namespace Eduka\Nereus\Commands;

use Eduka\Abstracts\EdukaCommand;

final class QueueNames extends EdukaCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eduka:queue-names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets the list of the queue names that need to be running for Eduka LMS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('
                    _       _
             ___  _| | _ _ | |__ ___
            / ._>/ . || | || / /<_> |
            \___.\___|`___||_\_\<___|

        ');

        $this->paragraph('-= Eduka installation =-', false);
    }
}
