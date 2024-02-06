<?php

namespace Eduka\Nereus;

use Brunocfalcao\Cerebrus\ConcernsSessionPersistence;
use Brunocfalcao\LaravelHelpers\Utils\DomainPatternIdentifier;
use Eduka\Cube\Models\Course;
use Eduka\Cube\Models\Domain;
use Eduka\Cube\Models\Organization;
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
     * Returns the respective matched organization, if any.
     *
     * @return Eduka\Cube\Models\Organization
     */
    public function organization()
    {
        $this->withPrefix('eduka:nereus:organization')
            ->invalidateIf(function () {
                return app()->runningInConsole();
            })
            ->persist(function () {
                $organization = $this->matchOrganization();
                if ($organization) {
                    return $organization->id;
                }
            });

        $organizationId = $this->obtain();

        if ($organizationId) {
            return Organization::firstWhere('id', $organizationId);
        }
    }

    /**
     * Tries to match one of the possible organization backend domains.
     * We iterate through the organization domains until we match one of
     * those.
     *
     * @return bool
     */
    public function matchBackend()
    {
        return Organization::where('domain', $this->domain())->exists();
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
    public function matchCourseDomain()
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
     * Verify if the current request domain matches a possible domain
     * in the organizations scopes. The match is done in the "name", meaning:.
     *
     * request domain      course domain    result
     * staging.roche.com   roche.com        true
     * roche.com           roche.com        true
     * roche.sky.com       roche.com        false
     *
     * @return string|null The matched domain.
     */
    public function matchOrganizationDomain()
    {
        $segments = DomainPatternIdentifier::parseUrl();

        $computedDomain = ($segments['subdomain'] ? $segments['subdomain'].'.' : '').
                          $segments['domain'].'.'.
                          $segments['top_level_domain'];

        return Organization::firstWhere(
            'domain',
            $computedDomain
        );
    }

    public function matchOrganization()
    {
        // Verify if the table courses exist.
        if (! Schema::hasTable('organizations')) {
            return null;
        }

        // Verify if the current url can be a possible domain course.
        return $this->matchOrganizationDomain();
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
        return $this->matchCourseDomain();
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
