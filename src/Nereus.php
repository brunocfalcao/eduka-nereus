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
        return $this->matchBackend() ?:
            Backend::firstWhere('domain', $this->domain());
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
