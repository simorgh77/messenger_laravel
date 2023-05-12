<?php

namespace App\Http\Controllers;

use App\Events\AvailableUser;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Pipeline;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LoginViewResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest;

class AuthController extends Controller
{


    public function logOut(Request $request,User $user)
    {
        $currentUser=Auth::user();

        $currentUser->forceFill([
            'is_online' => false,
        ])->save();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        event(new AvailableUser());
        return app(LogoutResponse::class);
}

    /**
     * Attempt to authenticate a new session.
     *
     * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
     * @return mixed
     */
    public function store(LoginRequest $request)
    {

      return $this->loginPipeline($request)->then(function ($request) {
          Auth::user()->forceFill([
            'is_online' => true,
        ])->save();
        event(new AvailableUser());
            return app(LoginResponse::class);
        });


    }

        public function loginPipeline(LoginRequest $request)
        {
            if (Fortify::$authenticateThroughCallback) {
                return (new Pipeline(app()))->send($request)->through(array_filter(
                    call_user_func(Fortify::$authenticateThroughCallback, $request)
                ));
            }

            if (is_array(config('fortify.pipelines.login'))) {
                return (new Pipeline(app()))->send($request)->through(array_filter(
                    config('fortify.pipelines.login')
                ));
            }

            return (new Pipeline(app()))->send($request)->through(array_filter([
                config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
                Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
                AttemptToAuthenticate::class,
                PrepareAuthenticatedSession::class,
            ]));
        }


}
