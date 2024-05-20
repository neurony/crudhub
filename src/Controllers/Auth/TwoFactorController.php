<?php

namespace Zbiller\Crudhub\Controllers\Auth;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;
use Zbiller\Crudhub\Contracts\AdminModelContract;
use Zbiller\Crudhub\Contracts\AuthenticatableTwoFactor;
use Zbiller\Crudhub\Exceptions\TwoFactorException;
use Zbiller\Crudhub\Facades\Flash;
use Zbiller\Crudhub\Requests\TwoFactorRequest;

class TwoFactorController extends AuthenticatedSessionController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * @param TwoFactorRequest $request
     * @return RedirectResponse|InertiaResponse
     */
    public function show(TwoFactorRequest $request): RedirectResponse|InertiaResponse
    {
        try {
            $admin = $this->getAuthenticatingUser($request);

            return Inertia::render('Auth/TwoFactor/Code', [
                'email' => $admin->{$admin->getTwoFactorEmailField()},
                'remember' => $request->session()->get('remember', false),
            ]);
        } catch (Throwable $e) {
            $request->session()->invalidate();

            Flash::error('Session expired', $e);

            return Redirect::route('admin.login.create');
        }
    }

    /**
     * @param TwoFactorRequest $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function request(TwoFactorRequest $request): RedirectResponse
    {
        $request->guardAgainstRateLimited();

        try {
            if (!Auth::guard('admin')->validate($request->attemptData())) {
                throw new AuthenticationException;
            }

            $admin = $this->getAuthenticatingUser($request);
            $admin->requestTwoFactorCode();

            Flash::success('2FA code requested');

            return Redirect::route('admin.two_factor.show')->with([
                'email' => $request->get('email'),
                'remember' => $request->get('remember'),
            ]);
        } catch (AuthenticationException | ModelNotFoundException $e) {
            $request->increaseRateLimit();

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        } catch (Throwable $e) {
            $request->session()->invalidate();

            Flash::error('Session expired', $e);

            return Redirect::route('admin.login.create');
        }
    }

    /**
     * @param TwoFactorRequest $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function check(TwoFactorRequest $request): RedirectResponse
    {
        try {
            $request->guardAgainstRateLimited();

            if (!$request->get('code')) {
                throw ValidationException::withMessages([
                    'code' => Lang::get('validation.required', [
                        'attribute' => 'code'
                    ]),
                ]);
            }

            $admin = $this->getAuthenticatingUser($request);
            $admin->validateTwoFactorCode($request->get('code'));

            Auth::guard('admin')->login($admin, (bool)$request->get('remember', false));

            $request->clearRateLimit();
            $request->session()->regenerate();

            return $this->sendLoginResponse($request);
        } catch (TwoFactorException | ValidationException $e) {
            $request->increaseRateLimit();

            $request->session()->flash('email', $request->get('email'));
            $request->session()->flash('remember', $request->get('remember'));

            throw ValidationException::withMessages([
                'code' => $e->getMessage(),
            ]);
        } catch (Throwable $e) {
            $request->session()->invalidate();

            Flash::error('Session expired', $e);

            return Redirect::route('admin.login.create');
        }
    }

    /**
     * @param TwoFactorRequest $request
     * @return AuthenticatableTwoFactor
     * @throws ModelNotFoundException|Exception
     */
    protected function getAuthenticatingUser(TwoFactorRequest $request): AuthenticatableTwoFactor
    {
        $user = App::make(AdminModelContract::class)
            ->where($request->findData())
            ->firstOrFail();

        if (!($user instanceof AuthenticatableTwoFactor)) {
            throw new Exception('User must implement "AuthenticatableTwoFactor"');
        }

        return $user;
    }
}
