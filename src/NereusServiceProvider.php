<?php

namespace Eduka\Nereus;

use Eduka\Abstracts\Classes\EdukaServiceProvider;
use Eduka\Cube\Models\Course;
use Eduka\Nereus\Commands\Fresh;
use Eduka\Nereus\Commands\Migrate;
use Eduka\Nereus\Commands\Update;
use Eduka\Nereus\Facades\Nereus as NereusFacade;
use Illuminate\Support\Facades\Route;

class NereusServiceProvider extends EdukaServiceProvider
{
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
        if (config('eduka.skip_course_detection') === true) {
            parent::boot();

            return;
        }

        $domainMatched = false;

        // Verify if we are in the backend url (config eduka.backend.url).
        if (NereusFacade::matchBackend()) {
            $this->loadBackendRoutes();

            // It's always the brunocfalcao/eduka-dev package.
            app()->register(\Eduka\Dev\DevServiceProvider::class);

            $domainMatched = true;
        }

        /**
         * Nereus allows 2 site contexts:
         * - In frontend, the visitor is on an identified course domain.
         * - In backend, the visitor is on the her backend (to see videos).
         */
        if (! $domainMatched) {
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

                $domainMatched = true;
            }
        }

        // Throw the HTTP 501 error. Limbo error.
        if (! $domainMatched && ! app()->runningInConsole()) {
            abort(501, 'No domain found to load a specific course or the admin backoffice');
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

    protected function loadViewNamespaces()
    {
        $this->customViewNamespace(__DIR__.'/../resources/views', 'eduka-system');
    }

    protected function loadCommands()
    {
        $this->commands([
            Update::class,
            Migrate::class,
            Fresh::class,
        ]);
    }

    protected function loadCommonRoutes()
    {
        $routesPath = __DIR__.'/../routes/common.php';

        Route::middleware([
            'web',
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
            'web',
        ])
            ->group(function () use ($routesPath) {
                include $routesPath;
            });

        // Load the payments webhook without on the api middleware.
        Route::middleware([
            'api',
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
