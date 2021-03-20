<?php

namespace Eduka\Nereus;

use Eduka\Abstracts\EdukaServiceProvider;
use Eduka\Analytics\Middleware\IpTracing;
use Eduka\Analytics\Middleware\VisitorTracing;
use Eduka\Analytics\Middleware\VisitTracing;
use Eduka\Cube\Models\Course;
use Eduka\Nereus\Commands\Install;
use Illuminate\Support\Facades\Route;

class EdukaNereusServiceProvider extends EdukaServiceProvider
{
    public function boot()
    {
        $this->loadEdukaViews(__DIR__.'/../resources/views');
        $this->registerCommands();
        $this->publishResources();
        $this->loadSystemViews();
        $this->loadRoutes();
    }

    public function register()
    {
        //
    }

    protected function loadSystemViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views/system', 'eduka-nereus');
    }

    protected function loadRoutes()
    {
        $this->loadTestRoutes();

        // Do we have a course that is not deleted?
        if (Course::exists()) {
            /*
             * The 'is launched?' decision is based on the course.launched_at
             * column.
             **/
            $routesPath = Course::active()->is_launched ?
            __DIR__.'/../routes/post-launch.php' :
            __DIR__.'/../routes/pre-launch.php';

            Route::middleware(['web',
                   IpTracing::class,
                   VisitorTracing::class,
                   VisitTracing::class,
                   GoalsTracing::class, ])
                 ->group(function () use ($routesPath) {
                    include $routesPath;
                 });
        } else {
            /**
             * No course loaded at all. So we load a default route
             * to show an Eduka splash screen.
             **/
            $routesPath = __DIR__.'/../routes/default.php';

            Route::middleware(['web'])
                 ->group(function () use ($routesPath) {
                    include $routesPath;
                 });
        }

        /**
         * Load system routes. For e.g. postmark inbound emails.
         **/
        $systemRoutesPath = __DIR__.'/../routes/system.php';

        Route::middleware([IpTracing::class])
             ->group(function () use ($systemRoutesPath) {
                include $systemRoutesPath;
             });
    }

    protected function publishResources()
    {
        $this->publishes([
            __DIR__.'/../resources/overrides/' => base_path('/'),
        ]);
    }

    protected function registerCommands(): void
    {
        $this->app->bind('command.eduka:install', Install::class);

        $this->commands([
            'command.eduka:install',
        ]);
    }

    protected function loadTestRoutes()
    {
        /*
         * The test routes are only loaded in case we are not in a
         * production environment. You are free to load your own
         * test routes on your course service provider.
         **/
        if (app()->environment() != 'production') {
            $this->loadRoutesFrom(__DIR__.'/../routes/tests.php');
        }
    }
}
