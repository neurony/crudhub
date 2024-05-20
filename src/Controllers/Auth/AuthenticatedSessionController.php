<?php

namespace Zbiller\Crudhub\Controllers\Auth;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Zbiller\Crudhub\Requests\LoginRequest;

class AuthenticatedSessionController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * @param Request $request
     * @return InertiaResponse
     */
    public function create(Request $request): InertiaResponse
    {
        return Inertia::render('Auth/Login', [
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function store(): RedirectResponse
    {
        /** @var LoginRequest $request */
        $request = App::make(config('crudhub.bindings.form_requests.login_form_request', LoginRequest::class));

        if (config('crudhub.login.two_factor_enabled')) {
            return App::call(TwoFactorController::class . '@request', [$request]);
        }

        $request->guardAgainstRateLimited();

        if (Auth::guard('admin')->attempt($request->attemptData(),  $request->boolean('remember'))) {
            $request->clearRateLimit();
            $request->session()->regenerate();

            return $this->sendLoginResponse($request);
        }

        $request->increaseRateLimit();

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::route(
            config('crudhub.login.redirect_after_logout', 'admin.login.create')
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    protected function sendLoginResponse(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
        }

        return Redirect::intended(
            route(config('crudhub.login.redirect_after_login', 'admin.dashboard'))
        );
    }
}
