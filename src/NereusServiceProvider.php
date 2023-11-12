<?php

namespace Eduka\Nereus;

use Brunocfalcao\Tracer\Middleware\VisitTracing;
use Eduka\Abstracts\Classes\EdukaServiceProvider;
use Eduka\Cube\Models\Course;
use Eduka\Nereus\Commands\Fresh;
use Eduka\Nereus\Commands\Migrate;
use Eduka\Nereus\Commands\PublishAssets;
use Eduka\Nereus\Facades\Nereus as NereusFacade;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class NereusServiceProvider extends EdukaServiceProvider
{
    public const COURSE_SESSION_KEY = 'course';

    public const NONCE_KEY = 'nonce';

    public ?Course $course;

    public function boot()
    {
        // configuration attributes setting for the parent class to work.
        $this->dir = __DIR__;

        // Default "eduka-system" view namespace. Normally to showcase eduka.
        $this->loadViewNamespaces();

        $this->loadCommands();

        /**
         * In case we are running in console, sometimes we might want to
         * skip the course contextualization (like to run migrations).
         */
        if (config('eduka.skip_course_detection') === true &&
            config('eduka.skip_course_detection')) {
            parent::boot();

            return;
        }

        $domainMatched = false;

        /**
         * Nereus allows 2 site contexts:
         * - In frontend, the visitor is on an identified course domain.
         * - In backend, the visitor is on the her backend (to see videos).
         */
        $this->course = NereusFacade::course();

        if ($this->course) {
            // Load common routes already.
            $this->loadCommonRoutes();

            /**
             * Load the routes, analytics middleware, course service
             * provider, etc.
             */
            $this->loadFrontendRoutes();

            /**
             * We will then register the course provider. No need to verify
             * if this course service provider is already registered via the
             * load_providers config key, since laravel already does that.
             */
            $this->course->registerSelfProvider();

            $domainMatched = true;
        }

        // Verify if we are in the backend url (config eduka.backend.url).
        if (NereusFacade::matchBackend() && ! $domainMatched) {
            $this->loadBackendRoutes();

            // It's always the brunocfalcao/eduka-dev package.
            app()->register(\Eduka\Dev\DevServiceProvider::class);

            $domainMatched = true;
        }

        // Throw the HTTP 501 error. Limbo error.
        if (! $domainMatched && ! app()->runningInConsole()) {
            abort(501, 'No domain found to load a specific course or the admin backoffice');
        }

        parent::boot();

        RateLimiter::for('payment', function () {
            return Limit::perMinute(5); // @todo take from config
        });
    }

    public function register()
    {
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
            PublishAssets::class,
            Fresh::class,
        ]);
    }

    protected function loadCommonRoutes()
    {
        $routesPath = __DIR__.'/../routes/common.php';

        Route::middleware([
            'web',
            VisitTracing::class,
        ])
        ->group(function () use ($routesPath) {
            include $routesPath;
        });
    }

    protected function loadBackendRoutes()
    {
        $routesPath = __DIR__.'/../routes/backend.php';

        Route::middleware([
            'web',
            VisitTracing::class,
        ])
        ->group(function () use ($routesPath) {
            include $routesPath;
        });
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
