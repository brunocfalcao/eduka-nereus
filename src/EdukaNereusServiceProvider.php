<?php

namespace Eduka\Nereus;

use Eduka\Abstracts\EdukaServiceProvider;
use Eduka\Analytics\Middleware\GoalsTracing;
use Eduka\Analytics\Middleware\IpTracing;
use Eduka\Analytics\Middleware\VisitTracing;
use Eduka\Cube\Models\Course;
use Eduka\Nereus\Commands\Install;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use ImLiam\BladeHelper\Facades\BladeHelper;

class EdukaNereusServiceProvider extends EdukaServiceProvider
{
    public function boot()
    {
        $this->customViewNamespace(__DIR__.'/../resources/views', 'eduka');

        $this->loadRoutes();
        $this->registerCommands();
        $this->publishResources();
        $this->registerBladeDirectives();
        $this->registerBladeComponents();
    }

    public function register()
    {
        //
    }

    protected function registerBladeComponents()
    {
        // Register blade components namespace.
        Blade::componentNamespace('Eduka\\Nereus\\Views\\Components', 'eduka');
    }

    protected function loadRoutes()
    {
        if (course()) {
            $this->loadTestRoutes();
            $this->loadCourseRoutes();
            $this->loadSystemRoutes();
        }
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
            if (file_exists(__DIR__.'/../routes/tests.php')) {
                $this->loadRoutesFrom(__DIR__.'/../routes/tests.php');
            }
        }
    }

    protected function loadCourseRoutes()
    {
        $routesPath = __DIR__.'/../routes/course.php';

        Route::middleware(['web',
               IpTracing::class,
               VisitTracing::class,
               GoalsTracing::class, ])
             ->group(function () use ($routesPath) {
                 include $routesPath;
             });
    }

    protected function loadSystemRoutes()
    {
        $systemRoutesPath = __DIR__.'/../routes/system.php';

        Route::middleware([IpTracing::class])
         ->group(function () use ($systemRoutesPath) {
             include $systemRoutesPath;
         });
    }

    protected function registerBladeDirectives()
    {
        /*
         * @image_placeholder_url($width, $height = null, $text = null, $backgroundColor = null)
         */
        BladeHelper::directive('image_placeholder_url', function ($width, $height = null, $text = null, $background = null) {
            return "https://via.placeholder.com/{$width}x{$height}/{$background}?text={$text}";
        });

        /*
         * @htmlentities('Bruno Falcão')
         */
        BladeHelper::directive('htmlentities', function ($value) {
            return htmlentities($value);
        });

        /*
         * @routename('comments.save')
         */
        Blade::if('routename', function ($name) {
            return Route::getCurrentRoute()->getAction()['as'] == $name;
        });
    }
}
