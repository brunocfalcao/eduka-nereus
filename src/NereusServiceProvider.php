<?php

namespace Eduka\Nereus;

use Eduka\Abstracts\Classes\EdukaServiceProvider;
use Eduka\Analytics\Middleware\TrackVisit;
use Eduka\Nereus\Commands\Migrate;
use Eduka\Nereus\Facades\Nereus as NereusFacade;
use Illuminate\Support\Facades\Route;

class NereusServiceProvider extends EdukaServiceProvider
{
    public $course;

    public function boot()
    {
        // configuration attributes setting for the parent class to work.
        $this->dir = __DIR__;

        // Default "eduka-system" view namespace. Normally to showcase eduka.
        $this->loadViewNamespaces();

        $this->loadCommands();

        /**
         * Nereus allows 2 site contexts:
         * - In frontend, the visitor is on an identified course domain.
         * - In backend, the visitor is on the her backend (to see videos).
         */
        if (! $this->app->runningInConsole()) {
            $this->course = NereusFacade::matchCourse();

            if ($this->course) {
                /**
                 * Load the routes, analytics middleware, course service
                 * provider, etc.
                 */
                $this->loadFrontendRoutes();
                $this->registerCourseServiceProvider();
            }

            // Throw the HTTP 501 error. Limbo error.
            //abort(501, 'Look! You are in the limbo!');
        }

        parent::boot();
    }

    public function register()
    {
        /**
         * Bind facades.
         */
        $this->app->bind('eduka-nereus', function () {
            return new Nereus();
        });

        $this->registerAdditionalProviders();
    }

    protected function loadViewNamespaces()
    {
        $this->customViewNamespace(__DIR__.'/../resources/views', 'eduka-system');
    }

    protected function loadCommands()
    {
        $this->commands([
            Migrate::class,
        ]);
    }

    protected function loadFrontendRoutes()
    {
        if ($this->course->isPrelaunched()) {
            $routesPath = __DIR__.'/../routes/prelaunched.php';
        }

        if ($this->course->isLaunched()) {
            $routesPath = __DIR__.'/../routes/launched.php';
        }

        Route::middleware([
            'web',
            TrackVisit::class,
        ])
        ->group(function () use ($routesPath) {
            include $routesPath;
        });
    }

    protected function registerCourseServiceProvider()
    {
        $this->course->registerSelfProvider();
    }

    protected function registerAdditionalProviders()
    {
        $providers = config('eduka.system.load_providers');

        foreach ($providers as $provider) {
            app()->register($provider);
        }
    }
}
