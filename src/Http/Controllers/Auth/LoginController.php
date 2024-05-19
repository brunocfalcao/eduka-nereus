<?php

namespace Eduka\Nereus\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Eduka\Cube\Models\Course;
use Eduka\Cube\Models\Student;
use Eduka\Nereus\Facades\Nereus;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // ***** Laravel methods override *****

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('backend::auth.login');
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }

    /**
     * Attempt to log the user into the application.
     *
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        /**
         * We can validate his/her credentials, but also if this student
         * should have access to this backend, meaning in case this account
         * exists, we need to verify if the student bought a course that
         * belongs to this respective backend.
         *
         * If it's a student admin, we don't even need to check if he is
         * attached to a course.
         */
        $student = Student::firstWhere('email', $request->input('email'));

        // No student? Done.
        if (! $student) {
            return false;
        }

        // Is this student an admin student?
        $course = Course::firstWhere('student_admin_id', $student->id);

        // Does his course he admins has this backend?
        if ($course && $course->backend->id == Nereus::backend()->id) {
            return $this->guard()->attempt(
                $this->credentials($request),
                $request->boolean('remember')
            );
        }

        // Student doesn't belong to a course from this backend?
        if (! $student->courses->where('backend_id', Nereus::backend()->id)->exists()) {
            return false;
        }

        // Execute a normal user attempt.
        return $this->guard()->attempt(
            $this->credentials($request),
            $request->boolean('remember')
        );
    }
}
