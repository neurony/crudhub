<?php

namespace Zbiller\Crudhub\Controllers\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PasswordResetController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * @param Request $request
     * @return InertiaResponse
     */
    public function create(Request $request): InertiaResponse
    {
        return Inertia::render('Auth/Password/Reset', [
            'email' => $request->get('email'),
            'token' => $request->route('token'),
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate($this->toValidate());

        $response = Password::broker('admins')
            ->reset($this->resetCredentials($request), function (Authenticatable $user, $password) {
                $this->resetPassword($user, $password);
            });

        if ($response == Password::PASSWORD_RESET) {
            return $this->sendResetResponse($request, $response);
        }

        return $this->sendResetFailedResponse($request, $response);
    }

    /**
     * @return array
     */
    protected function toValidate(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed'],
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function resetCredentials(Request $request): array
    {
        return $request->only([
            'email',
            'password',
            'password_confirmation',
            'token'
        ]);
    }

    /**
     * @param Authenticatable $user
     * @param string $password
     */
    protected function resetPassword(Authenticatable $user, string $password): void
    {
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        Event::dispatch(new PasswordReset($user));
        Auth::guard('admin')->login($user);
    }

    /**
     * @param Request $request
     * @param $response
     * @return JsonResponse|RedirectResponse
     */
    protected function sendResetResponse(Request $request, $response): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return new JsonResponse(['message' => Lang::get($response)]);
        }

        return Redirect::route('admin.dashboard');
    }

    /**
     * @param Request $request
     * @param $response
     * @return JsonResponse|RedirectResponse
     * @throws ValidationException
     */
    protected function sendResetFailedResponse(Request $request, $response): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'email' => [trans($response)],
            ]);
        }

        return Redirect::back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }
}
