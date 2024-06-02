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

    public $backend;

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
        if (is_array(config('eduka.load_providers'))) {
            foreach (config('eduka.load_providers') as $provider) {
                app()->register($provider);
            }
        }

        /**
         * The common routes are routes that need to be loaded no matter
         * where we are. E.g.: jobs that would need to have routes
         * specified, then those routes need to be created here.
         */
        $this->loadCommonRoutes();

        /**
         * In case there is a route file with the same name as the
         * environment type, then we can load it.
         */
        $this->loadEnvironmentBaseRoutes();

        /**
         * No matter if we are in whatever context, we always load
         * our translation locale file. The translations are
         * used like trans('nereus::nereus.<xxx>') or in case
         * of a contextualized course, like trans('course::course.<xxx>').
         */
        $this->loadLocale();

        /**
         * The Eduka routes and views are always loaded since they
         * are mostly used on commands, jobs, image loading paths, etc.
         *
         * They are also loaded for the APP_URL, for instance for
         * payment webhooks.
         */
        $this->loadEdukaRoutes();
        $this->loadEdukaViews();

        /**
         * If we are in a console context, we don't need to try
         * to match an backend or a course.
         */
        if (app()->runningInConsole()) {
            parent::boot();

            return;
        }

        // Backend (student's backoffice) ?
        if (NereusFacade::matchBackend()) {
            $this->backend = NereusFacade::backend();

            /**
             * Load the backend backend routes. Nova, etc.
             */
            $this->loadBackendRoutes();

            /**
             * This allows us to have multiple backends for different
             * backends. This way we can just unify everything in
             * a single domain and then just have courses, and backends as
             * domain aliases.
             */
            app()->register($this->backend->provider_namespace);

            // Frontend?
        } elseif (NereusFacade::matchCourse()) {
            $this->course = NereusFacade::course();

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

            //Add the course logic into the filesystem 'eduka' disk.
            push_eduka_filesystem_disk($this->course);

            /**
             * We will then register the course provider. No need to verify
             * if this course service provider is already registered via the
             * load_providers config key, since laravel already does that.
             */
            $this->course->registerSelfProvider();
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

    protected function loadEdukaViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'eduka');
    }

    protected function loadEdukaRoutes()
    {
        $routesPath = __DIR__.'/../routes/eduka.php';
        $apiRoutesPath = __DIR__.'/../routes/api.php';

        Route::middleware([
            'web', RequestLog::class,
        ])
            ->group(function () use ($routesPath) {
                include $routesPath;
            });

        // Load the payments webhook on the api middleware.
        Route::middleware([
            'api', RequestLog::class,
        ])
            ->group(function () use ($apiRoutesPath) {
                include $apiRoutesPath;
            });
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

    protected function loadLocale()
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'nereus');
    }

    protected function loadEnvironmentBaseRoutes()
    {
        $envRoute = __DIR__.'/../routes/'.app()->environment().'.php';

        if ($envRoute) {
            Route::middleware([
                'web', RequestLog::class,
            ])
                ->group(function () use ($envRoute) {
                    include $envRoute;
                });
        }
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

        Route::middleware([
            'web', RequestLog::class,
        ])
            ->group(function () use ($routesPath) {
                include $routesPath;
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
