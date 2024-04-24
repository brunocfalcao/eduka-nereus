<?php

namespace Eduka\Nereus;

use Brunocfalcao\LaravelHelpers\Utils\DomainPatternIdentifier;
use Eduka\Cube\Models\Backend;
use Eduka\Cube\Models\Course;
use Eduka\Cube\Models\Domain;
use Illuminate\Support\Facades\Schema;

class Nereus
{
    public function __construct()
    {
        /**
         * Nereus is called via a facade, so the __construct() is not
         * always called on each Nereus method. Nothing here then.
         */
    }

    /**
     * Retrieves the current context of the url analysis.
     * Types:
     *  'context' key:
     * 'course' => We are not in any other context (fallback context).
     * 'chapter' => We are showing/listing course chapters.
     * 'episode' => We are showing an episode, or on an episode detail.
     * 'serving' => 'backend', 'frontend' or 'admin' (nova).
     *
     * Inside the 'model', we grab the context instance (chapter model, etc.).
     * Can be null also ['model' => null] if no model is instanciated.
     *
     * This allows specific code to run give the type of context we are, like
     * the social meta tags rendering, finding a model, etc.
     *
     * @return array
     */
    public function context()
    {
        $context = [
            'type' => 'course',
            'model' => null,
        ];

        if (self::course()) {
            $context['model'] = self::course();
        }

        return $context;
    }

    /**
     * Returns a translated locale, based on the course/backend context.
     * As example, for the canonical EN course-mastering-nova, there should be
     * a lang_path('en/course-mastering-nova.php') file. Then inside it
     * you should define the respective translations array.
     *
     * @param  string  $key  The locale key to return the value
     * @param  array  $params  key/values to translate using {xxx}
     * @param  string|null  $canonical  An optional locale name, default=canonical
     * @return string
     */
    public function trans(string $key, array $params = [], ?string $canonical = null)
    {
        $canonical ??= self::course()->canonical;
        $locale = app()->getlocale();

        if (file_exists(lang_path("{$locale}/{$canonical}.php"))) {
            return __("{$canonical}.{$key}", $params);
        }

        return "{$canonical}.{$key}";
    }

    /**
     * Returns the current course instance, for the respective domain.
     * Fallback HTTP error.
     *
     * @return Eduka\Cube\Models\Course
     */
    public function course()
    {
        return $this->matchCourse() ?:
            Course::firstWhere('domain', $this->domain());
    }

    /**
     * Returns the respective matched backend, if any.
     *
     * @return Eduka\Cube\Models\Backend
     */
    public function backend()
    {
        return $this->matchBackend() ?
            Backend::firstWhere('domain', $this->domain()) :
            null;
    }

    /**
     * Is the current request domain, part of a backend?
     *
     * @return bool
     */
    public function matchBackend()
    {
        // Verify if the table courses exist.
        if (! Schema::hasTable('backends')) {
            return false;
        }

        return Backend::where('domain', $this->domain())->exists();
    }

    /**
     * Tries to match an eduka course by the domain used by the visitor.
     *
     * @return Course
     */
    public function matchCourse()
    {
        // Verify if the table courses exist.
        if (! Schema::hasTable('courses')) {
            return null;
        }

        return Course::firstWhere('domain', $this->domain());
    }

    /*
     * request domain        domain           result
     * ------------------|----------------|---------
     * staging.roche.com    roche.com          true
     * roche.com            roche.com          true
     * roche.sky.com        roche.com          false
    */
    protected function domain()
    {
        $segments = DomainPatternIdentifier::parseUrl();

        $computedDomain = ($segments['subdomain'] ? $segments['subdomain'].'.' : '').
                          $segments['domain'].'.'.
                          $segments['top_level_domain'];

        return $computedDomain;
    }
}
