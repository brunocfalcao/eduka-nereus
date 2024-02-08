<?php

namespace Eduka\Nereus;

use Eduka\Abstracts\Classes\EdukaServiceProvider;
use Eduka\Cube\Models\Course;
use Eduka\Nereus\Commands\Fresh;
use Eduka\Nereus\Commands\Migrate;
use Eduka\Nereus\Facades\Nereus as NereusFacade;
use Eduka\Nereus\Middleware\RequestLog;
use Illuminate\Support\Facades\Route;

class NereusServiceProvider extends EdukaServiceProvider
{
    public $course;

    public $organization;

    public function boot()
    {
        // configuration attributes setting for the parent class to work.
        $this->dir = __DIR__;

        $this->loadCommands();

        /**
         * Load forced providers that are configured via the
         * eduka.load_providers. They will always load no matter what
         * happens next.
         */
        foreach (config('eduka.load_providers') as $provider) {
            app()->register($provider);
        }

        /**
         * If we are in a console context, we don't need to try
         * to match an organization or a course.
         */
        if (app()->runningInConsole()) {
            parent::boot();

            return;
        }

        // Backend?
        if (NereusFacade::organization()) {
            $this->organization = NereusFacade::organization();

            /**
             * Load the organization backend routes. Nova, etc.
             */
            $this->loadBackendRoutes();

            /**
             * This allows us to have multiple backends for different
             * organizations. This way we can just unify everything in
             * a single domain and then just have courses, and backends as
             * domain aliases.
             */
            app()->register($this->organization->provider_namespace);

            // Frontend?
        } elseif (NereusFacade::course()) {
            $this->course = NereusFacade::course();

            // Load common routes already.
            $this->loadCommonRoutes();

            /**
             * Load the routes, analytics middleware, course service
             * provider, etc.
             */
            $this->loadFrontendRoutes();

            /**
             * Bootstrap Eduka UI provider (only in the case of a
             * course, or a backend that is identified).
             */
            $this->registerUIProvider();

            /**
             * We will then register the course provider. No need to verify
             * if this course service provider is already registered via the
             * load_providers config key, since laravel already does that.
             */
            $this->course->registerSelfProvider();
        } else {
            abort(501, 'Domain not part of the eduka organizations or courses');
        }

        parent::boot();
    }

    public function register()
    {
        $this->app->bind('eduka-nereus', function () {
            return new Nereus();
        });

        $this->registerAdditionalProviders();
    }

    protected function registerUIProvider()
    {
        app()->register(\Eduka\UI\UIServiceProvider::class);
    }

    protected function loadCommands()
    {
        $this->commands([
            Migrate::class,
            Fresh::class,
        ]);
    }

    protected function loadCommonRoutes()
    {
        $routesPath = __DIR__.'/../routes/common.php';

        Route::middleware([
            'web', RequestLog::class,
        ])
            ->group(function () use ($routesPath) {
                include $routesPath;
            });
    }

    protected function loadBackendRoutes()
    {
        $routesPath = __DIR__.'/../routes/backend.php';
        $apiRoutesPath = __DIR__.'/../routes/api.php';

        Route::middleware([
            'web', RequestLog::class,
        ])
            ->group(function () use ($routesPath) {
                include $routesPath;
            });

        // Load the payments webhook without on the api middleware.
        Route::middleware([
            'api', RequestLog::class,
        ])
            ->group(function () use ($apiRoutesPath) {
                include $apiRoutesPath;
            });
    }

    protected function loadFrontendRoutes()
    {
        switch ($this->course->state()) {
            case 'prelaunched':
                $routesPath = __DIR__.'/../routes/prelaunched.php';
                break;

            case 'launched':
                $routesPath = __DIR__.'/../routes/launched.php';
                break;
        }

        Route::middleware([
            'web', RequestLog::class,
        ])
            ->group(function () use ($routesPath) {
                include $routesPath;
            });
    }

    protected function registerAdditionalProviders()
    {
        $providers = config('eduka.load_providers');

        if ($providers) {
            foreach ($providers as $provider) {
                app()->register($provider);
            }
        }
    }
}
