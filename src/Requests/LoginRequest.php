<?php

namespace Zbiller\Crudhub\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Zbiller\Crudhub\Traits\ThrottlesRequests;

class LoginRequest extends FormRequest
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
        return [
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
                'string',
            ],
        ];
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
}
