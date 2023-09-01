<?php

namespace Eduka\Nereus;

use Brunocfalcao\Cerebrus\Cerebrus;
use Eduka\Abstracts\Classes\EdukaServiceProvider;
use Eduka\Analytics\Middleware\TrackVisit;
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
            $this->course = NereusFacade::matchCourse() ??
                            NereusFacade::matchCourseByLoadedProviders();

            if ($this->course) {
                /**
                 * Load the routes, analytics middleware, course service
                 * provider, etc.
                 */
                $this->loadFrontendRoutes();
                $this->registerCourseServiceProvider();

                // new Cerebrus Session
                // eduka-course prefix
                (new Cerebrus())->set(
                    self::COURSE_SESSION_KEY,
                    $this->course,
                );
            }

            // Verify if we are in the backend url (config eduka.backend.url).
            if (NereusFacade::matchBackend()) {
                dd('in backend');
            }

            // Throw the HTTP 501 error. Limbo error.
            // abort(501, "No domain found to load a specific course or the admin backoffice");
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
        /**
         * We will then register the course provider. No need to verify
         * if this course service provider is already registered via the
         * load_providers config key, since laravel already does that.
         */
        $this->course->registerSelfProvider();
    }

    protected function registerAdditionalProviders()
    {
        $providers = config('eduka.system.load_providers');

        if ($providers) {
            foreach ($providers as $provider) {
                app()->register($provider);
            }
        }
    }
}
