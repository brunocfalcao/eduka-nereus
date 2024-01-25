<?php

namespace Eduka\Nereus;

use Brunocfalcao\Cerebrus\ConcernsSessionPersistence;
use Brunocfalcao\LaravelHelpers\Utils\DomainPatternIdentifier;
use Eduka\Cube\Models\Course;
use Eduka\Cube\Models\Domain;
use Illuminate\Support\Facades\Schema;

class Nereus
{
    use ConcernsSessionPersistence;

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
     * Returns the current course instance, for the respetive domain.
     * Fallback HTTP error.
     *
     * @return Eduka\Cube\Models\Course
     */
    public function course()
    {
        $this->withPrefix('eduka:nereus:course')
            ->invalidateIf(function () {
                /**
                 * Invalidation can occur in the following scenarios:
                 * EDUKA_ALWAYS_INVALIDATE_COURSES=true or
                 * Visitor getHost() != Current course session domain.
                 */
                return $this->matchCourse() == null ||
                       config('eduka.always_invalidate_courses') === true;
            })
            ->persist(function () {
                $course = $this->matchCourse();
                if ($course) {
                    return $course->id;
                }
            });

        $courseId = $this->obtain();

        if ($courseId) {
            return Course::firstWhere('id', $courseId);
        }
    }

    /**
     * Tries to match the backend url with the visited url. This is the
     * complementary to the matchCourse, meaning, if we are not in a course
     * landing page, then we need to be in a backend context.
     *
     *
     * @return bool
     */
    public function matchBackend()
    {
        return $this->domain() == config('eduka.backend.url');
    }

    /**
     * Verify if the current request domain matches a possible domain
     * in the courses scopes. The match is done in the "name", meaning:.
     *
     * request domain      course domain    result
     * staging.roche.com   roche.com        true
     * roche.com           roche.com        true
     * roche.sky.com       roche.com        false
     *
     * @return string|null The matched domain.
     */
    public function matchDomain()
    {
        $segments = DomainPatternIdentifier::parseUrl();

        $computedDomain = ($segments['subdomain'] ? $segments['subdomain'].'.' : '').
                          $segments['domain'].'.'.
                          $segments['top_level_domain'];

        return Course::firstWhere(
            'domain',
            $computedDomain
        );
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

        // Verify if the current url can be a possible domain course.
        return $this->matchDomain();
    }

    /**
     * Returns the current domain that is contextualized from session.
     * Invalidates session if the visit domain url, is different from the
     * contextualized domain.
     *
     * @return string session request for the domain host.
     */
    protected function domain()
    {
        return $this->withPrefix('eduka:nereus:domain')
            ->persist(function () {
                return request()->getHost();
            })
            ->obtain();
    }
}
