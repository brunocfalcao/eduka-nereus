<?php

namespace Eduka\Nereus;

use Brunocfalcao\Cerebrus\ConcernsSessionPersistence;
use Eduka\Cube\Models\Course;
use Eduka\Cube\Models\Domain;

class Nereus
{
    use ConcernsSessionPersistence;

    public function __construct()
    {
        $this->withPrefix('eduka:nereus:domain')
            ->invalidateIf(function () {
                /**
                 * Invalidate if the domain host session is different from the
                 * visitor domain host.
                 */
                return request()->getHost() != $this->obtain();
            });

        $this->withPrefix('eduka:nereus:course')
             ->invalidateIf(function () {
                 /**
                  * Invalidate if the domain host session is different from the
                  * visitor domain host.
                  */
                 return request()->getHost() != $this->obtain();
             });
    }

    /**
     * Returns the current course instance, for the respetive domain.
     * Fallback HTTP error.
     *
     * @return Eduka\Cube\Models\Course
     */
    public function course()
    {
        $this->domainMatched();

        $this->withPrefix('eduka:nereus:course')
             ->persist(function () {
                 return $this->course;
             })
             ->obtain();
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
     * in the courses scopes. The match is done in the "suffix", meaning:.
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
        $fdn = $this->domain();

        $chars = strlen($fdn);

        return Domain::whereRaw("right(suffix, {$chars}) = ?", [$fdn])
                              ->first();
    }

    /**
     * Tries to match an eduka course by the domain used by the visitor.
     *
     * @return \Eduka\Cube\Models\Course
     */
    public function matchCourse()
    {
        $domain = $this->matchDomain();

        if (! $domain) {
            return null;
        }

        if (blank($domain->course)) {
            abort('501', 'Domain is registered, but no course is related with it');
        }

        return $domain->course;
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
