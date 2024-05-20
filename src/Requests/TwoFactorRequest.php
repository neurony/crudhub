<?php

namespace Zbiller\Crudhub\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Zbiller\Crudhub\Traits\ThrottlesRequests;

class TwoFactorRequest extends FormRequest
{
    use ThrottlesRequests;

    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * @return array
     */
    public function attemptData(): array
    {
        $attempt = $this->only('email', 'password');

        if (config('crudhub.login.allow_active_only', true)) {
            $attempt += ['active' => true];
        }

        return $attempt;
    }

    /**
     * @return array
     */
    public function findData(): array
    {
        $find = [
            'email' => $this->get('email', $this->session()->get('email'))
        ];

        if (config('crudhub.login.allow_active_only', true)) {
            $find += ['active' => true];
        }

        return $find;
    }
}
