<?php

namespace Zbiller\Crudhub\Controllers\Auth;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Zbiller\Crudhub\Facades\Flash;

class PasswordForgotController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * @return InertiaResponse
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('Auth/Password/Forgot');
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
            ->sendResetLink($request->only('email'));

        if ($response == Password::RESET_LINK_SENT) {
            return $this->sendForgotResponse($request, $response);
        }

        return $this->sendForgotFailedResponse($request, $response);
    }

    /**
     * @return array
     */
    protected function toValidate(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    /**
     * @param Request $request
     * @param $response
     * @return JsonResponse|RedirectResponse
     */
    protected function sendForgotResponse(Request $request, $response): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return new JsonResponse(['message' => Lang::get($response)]);
        }

        Flash::success(Lang::get($response));

        return Redirect::back()->with('status', Lang::get($response));
    }

    /**
     * @param Request $request
     * @param $response
     * @return RedirectResponse
     * @throws ValidationException
     */
    protected function sendForgotFailedResponse(Request $request, $response): RedirectResponse
    {
        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'email' => [trans($response)]
            ]);
        }

        return Redirect::back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }
}
