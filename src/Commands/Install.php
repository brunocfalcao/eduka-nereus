<?php

namespace Eduka\Nereus\Commands;

use Eduka\Abstracts\EdukaCommand;
use Eduka\Cube\Models\Course;
use Eduka\Cube\Models\User;
use Illuminate\Support\Str;

final class Install extends EdukaCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eduka:install';

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

        $this->preChecks();

        $this->deleteStorageDirectories();

        $this->publish3rdPartyResources();

        $this->publishEdukaResources();

        $this->replaceJsonDataTypesToLongText();

        $this->migrateFresh();

        $this->createStorageLink();

        $this->createAdminUser();

        $this->paragraph('WARNING: Please ensure you have the ENV variable REDIS_QUEUE configured!', false);

        $this->paragraph('-= ALL GOOD! Go and create that awesome course! =-', false, false);

        return 0;
    }

    protected function deleteStorageDirectories()
    {
        $this->paragraph('Delete storage public directories (if they exist)...');

        $this->rrmdir(storage_path('app/public'));

        mkdir(storage_path('app/public'));

        $this->paragraph('Storage public directories deleted okay!', false);
    }

    protected function createStorageLink()
    {
        $this->paragraph('Creating storage link...');

        // Delete storage if it exists.
        @rmdir(public_path('storage'));
        $this->call('storage:link');

        $this->paragraph('Storate linkage okay!', false);
    }

    protected function createAdminUser()
    {
        $this->paragraph('Creating admin user...', false);

        $password = app()->environment() != 'production' ? 'password' : (string) Str::random(10);

        $admin = User::create([
            'name' => env('MAIL_FROM_NAME'),
            'email' => env('MAIL_FROM_ADDRESS'),
            'email_verified_at' => now(),
            'password' => bcrypt($password),
        ]);

        $this->paragraph('Admin user creation okay (email: '.env('MAIL_FROM_ADDRESS').' password: '.$password.')!', false);
    }

    protected function migrateFresh()
    {
        $this->paragraph('Freshing database...');

        $this->call('migrate:fresh');

        $this->paragraph('Database migration ran okay!', false);
    }

    protected function publishEdukaResources()
    {
        $this->paragraph('Publishing eduka packages resources...');

        /*
         * Eduka packages
         * brunocfalcao/eduka-cube
         * brunocfalcao/eduka-nereus
         * brunocfalcao/eduka-maquillage
         * brunocfalcao/eduka-nova
         **/
        $this->call('vendor:publish', [
            '--force' => true,
            '--provider' => 'Eduka\\Cube\\EdukaCubeServiceProvider',
        ]);

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
            '--provider' => 'Eduka\\Maquillage\\EdukaMaquillageServiceProvider',
        ]);

        $this->call('vendor:publish', [
            '--force' => true,
            '--provider' => 'Eduka\\Nova\\EdukaNovaServiceProvider',
        ]);

        $this->paragraph('Eduka packages resources okay!', false);
    }

    protected function publish3rdPartyResources()
    {
        $this->paragraph('Publishing 3rd party package resources...');

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

        $this->paragraph('3rd party packages resources okay!', false);
    }

    protected function replaceJsonDataTypesToLongText()
    {
        // Delete previous create_media_file migrations.
        foreach (glob(database_path('migrations/*.php')) as $filename) {
            $file = file_get_contents($filename);

            $data = str_replace('->json(', '->longText(', $file);

            file_put_contents($filename, $data);
        }
    }

    protected function preChecks()
    {
        $this->paragraph('Running pre-checks...');

        // Logo for emails (logo.jpg) image in the public/images folder?
        if (! file_exists(public_path('images/logo.jpg'))) {
            return $this->error('Please upload your logo for emails image, 1200x600, JPG, in /public/images/logo.jpg');
        }

        if (env('APP_NAME') == 'Laravel') {
            return $this->error('Please rename your .ENV APP_NAME with your course name');
        }

        if (env('MAIL_MAILER') != 'postmark') {
            return $this->error('Please check your .ENV MAIL_MAILER that should be equal to postmark');
        }

        if (is_null(env('MAIL_FAKE'))) {
            return $this->error('Please check your .ENV MAIL_FAKE that should be equal 1 or 0');
        }

        if (is_null(env('POSTMARK_TOKEN'))) {
            return $this->error('Please check your .ENV POSTMARK_TOKEN that cannot be null');
        }

        if (is_null(env('MAIL_FROM_ADDRESS'))) {
            return $this->error('Please check your .ENV MAIL_FROM_ADDRESS that cannot be null');
        }

        if (is_null(env('MAIL_FROM_NAME'))) {
            return $this->error('Please check your .ENV MAIL_FROM_NAME that cannot be null');
        }

        if (is_null(env('QUEUE_CONNECTION'))) {
            return $this->error('Please check your .ENV QUEUE_CONNECTION that should be redis');
        }

        $this->paragraph('Pre-checks okay!', false, false);
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
