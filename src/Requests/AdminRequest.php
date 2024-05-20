<?php

namespace Zbiller\Crudhub\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;
use Spatie\Permission\Contracts\Role as RoleModelContract;
use Zbiller\Crudhub\Contracts\AdminModelContract;

class AdminRequest extends FormRequest
{
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
            'name' => [
                'required',
            ],
            'email' => [
                'required',
                'email',
                Rule::unique(App::make(AdminModelContract::class)->getTable(), 'email')
                    ->ignore($this->route('admin')?->getKey() ?: null),
            ],
            'password' => [
                'confirmed',
                $this->isMethod('post') ? 'required' : null,
            ],
            'role' => [
                'required',
                Rule::exists(App::make(RoleModelContract::class)->getTable(), 'id'),
            ],
        ];
    }

    /**
     * @return $this
     */
    public function merged(): self
    {
        return $this->mergePassword()->mergeActive();
    }

    /**
     * @return $this
     */
    protected function mergePassword(): self
    {
        if ($this->filled('password')) {
            return $this->merge([
                'password' => bcrypt($this->input('password')),
                'password_confirmation' => null,
            ]);
        } else {
            return $this->create($this->url(), $this->method(), $this->except([
                'password', 'password_confirmation'
            ]));
        }
    }

    /**
     * @return $this
     */
    protected function mergeActive(): self
    {
        if (!$this->filled('active')) {
            $this->merge([
                'active' => false,
            ]);
        }

        return $this;
    }
}
