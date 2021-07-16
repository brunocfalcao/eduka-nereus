<?php

namespace Eduka\Nereus\Commands;

use Eduka\Abstracts\EdukaCommand;
use Eduka\Cube\Models\Course;
use Eduka\Cube\Models\User;

final class Install extends EdukaCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eduka:install {--with-test-data : Install with testing data}
                                          {--fast : No questions asked}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs Eduka LMS, the best learning framework in the world!';

    /**
     * Create a new command instance.
     *
     * @return void
     */
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

        $this->paragraph('-= Eduka installation =-', false);

        if (!$this->option('quiet')) {
            if (!$this->confirm('Did you install LARAVEL NOVA and LARAVEL HORIZON first?')) {
                return;
            }
        }

        // Clear framework cache.
        $this->call('optimize:clear');
        $this->call('view:clear');
        $this->call('key:generate');

        if (! $this->preChecks()) {
            return;
        }

        $this->deleteModelsDefaultFolder();

        $this->deleteStorageDirectories();

        $this->createStorageLink();

        $this->publish3rdPartyResources();

        $this->publishEdukaResources();

        $this->replaceJsonDataTypesToLongText();

        $this->migrateFresh();

        $this->createAdminUser();

        $this->paragraph('-= ATTENTION! Do not forget to publish your course package resources! =-', false, false);

        $this->paragraph('-= ALL GOOD! Go and create that awesome course! =-');

        return 0;
    }

    protected function deleteModelsDefaultFolder()
    {
        $this->paragraph('=> Deleting App/Models directory (if exist)...', false);

        if (is_dir(app_path('Models'))) {
            @$this->rrmdir(app_path('Models'));
        }

        @unlink(app_path('Nova/User.php'));
    }

    protected function deleteStorageDirectories()
    {
        $this->paragraph('=> Deleting storage public directories (if they exist)...', false);

        @$this->rrmdir(storage_path('app/public'));

        mkdir(storage_path('app/public'));
    }

    protected function createStorageLink()
    {
        $this->paragraph('=> Creating storage link...');

        // Delete storage if it exists.
        @rmdir(public_path('storage'));

        $this->call('storage:link');
    }

    protected function createAdminUser()
    {
        $this->paragraph('=> Creating eduka admin user...', false);

        $admin = User::create([
            'name' => env('EDUKA_ADMIN_NAME'),
            'email' => env('EDUKA_ADMIN_ADDRESS'),
            'email_verified_at' => now(),
            'password' => bcrypt(env('EDUKA_ADMIN_PASSWORD')),
        ]);

        $this->paragraph('=> Admin user created ('.env('EDUKA_ADMIN_ADDRESS').', '.env('EDUKA_ADMIN_PASSWORD').')');
    }

    protected function migrateFresh()
    {
        $this->paragraph('=> Creating Eduka database schema + seeding initial data + optional test data...', false);

        if ($this->option('with-test-data')) {
            $this->call('eduka:fresh-seed', [
                '--with-test-data' => true,
            ]);
        } else {
            $this->call('eduka:fresh-seed');
        }
    }

    protected function publishEdukaResources()
    {
        $this->paragraph('=> Publishing eduka packages assets...');

        /*
         * Eduka packages
         * brunocfalcao/eduka-nereus
         * brunocfalcao/eduka-analytics
         * brunocfalcao/eduka-nova
         **/
        $this->call('vendor:publish', [
            '--force' => true,
            '--provider' => 'Eduka\\Nereus\\EdukaNereusServiceProvider',
        ]);

        $this->call('vendor:publish', [
            '--force' => true,
            '--provider' => 'Eduka\\Analytics\\EdukaAnalyticsServiceProvider',
        ]);

        $this->call('vendor:publish', [
            '--force' => true,
            '--provider' => 'Eduka\\Nova\\EdukaNovaServiceProvider',
        ]);
    }

    protected function publish3rdPartyResources()
    {
        $this->paragraph('=> Publishing 3rd party package resources...');

        /*
         * 3rd party packages:
         * ebess/advanced-nova-media-library
         * spatie/laravel-medialibrary
         **/
        $this->call('vendor:publish', [
            '--force' => true,
            '--provider' => 'Ebess\\AdvancedNovaMediaLibrary\\AdvancedNovaMediaLibraryServiceProvider',
        ]);

        // Delete previous create_media_file migrations.
        foreach (glob(database_path('migrations/*create_media_table.php')) as $filename) {
            unlink($filename);
        }

        $this->call('vendor:publish', [
            '--force' => true,
            '--provider' => 'Spatie\\MediaLibrary\\MediaLibraryServiceProvider',
        ]);
    }

    protected function replaceJsonDataTypesToLongText()
    {
        $this->paragraph('=> Replacing json migration datatypes by longTexts (for maria db compatibility)...', false);

        // Delete previous create_media_file migrations.
        foreach (glob(database_path('migrations/*.php')) as $filename) {
            $file = file_get_contents($filename);

            $data = str_replace('->json(', '->longText(', $file);

            file_put_contents($filename, $data);
        }
    }

    protected function preChecks()
    {
        $this->paragraph('Running pre-checks...', false);

        /**
         * To initialize the schema initialization seeder, there should be
         * registered the following environment variables:
         * EDUKA_ADMIN_NAME
         * EDUKA_ADMIN_ADDRESS
         * EDUKA_ADMIN_PASSWORD
         * EDUKA_COURSE_NAME.
         *
         * This information is used to create the admin user in the database
         * and a course stub. This course stub cannot be deleted, should be
         * changed to the real course. The same for the admin user.
         **/

        /**
         * Quick ENV key/values validation.
         * key name => type
         * type can be:
         *   null (should exist, any value allowed)
         *   a value (equal to that value).
         */
        $shouldBeFilled = collect([
            'APP_NAME' => null,
            'EDUKA_ADMIN_NAME' => null,
            'EDUKA_ADMIN_ADDRESS' => null,
            'EDUKA_ADMIN_PASSWORD' => null,
            'MAIL_FROM_ADDRESS' => null,
            'MAIL_FROM_NAME' => null,
            'MAIL_MAILER' => 'postmark',
            'POSTMARK_TOKEN' => null,
            'QUEUE_CONNECTION' => 'redis',
            'CACHE_DRIVER' => 'redis',
            'REDIS_QUEUE' => null,
        ]);

        $result = $shouldBeFilled->every(function ($value, $key) {
            if (empty($value)) {
                return ! empty(env($key));
            }

            if (! empty($value)) {
                return env($key) == $value;
            }
        });

        if (! $result) {
            return $this->error("You are missing information your .env file. Please verify the following keys:
                env('APP_NAME')
                env('EDUKA_ADMIN_NAME')
                env('EDUKA_ADMIN_ADDRESS')
                env('EDUKA_ADMIN_PASSWORD')
                env('MAIL_FROM_ADDRESS')
                env('MAIL_FROM_NAME')
                env('POSTMARK_TOKEN')
                env('REDIS_QUEUE')
                env('MAIL_MAILER') = postmark
                env('QUEUE_CONNECTION') = redis
                env('CACHE_DRIVER') = redis");
        }

        if (env('APP_NAME') == 'Laravel') {
            return $this->error('Please rename your .ENV APP_NAME with your course name. Cannot be Laravel');
        }

        if (! class_exists('\App\Providers\NovaServiceProvider')) {
            return $this->error('Please install Nova before running Eduka');
        }

        return true;
    }

    private function rrmdir($dir)
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->rrmdir("$dir/$file") : unlink("$dir/$file");
        }

        return rmdir($dir);
    }
}
