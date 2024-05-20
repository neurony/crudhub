<?php

namespace Zbiller\Crudhub\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;
use Zbiller\Crudhub\Contracts\FlashHelperContract;

class FlashHelper implements FlashHelperContract
{
    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $message
     * @return void
     */
    public function success(string $message = 'Operation successful'): void
    {
        $this->request->session()->flash('success', $message);
    }

    /**
     * @param string $message
     * @param Throwable|null $exception
     * @return void
     * @throws Throwable
     */
    public function error(string $message = 'Operation failed', ?Throwable $exception = null): void
    {
        if ($exception instanceof Throwable && $this->shouldThrowErrors()) {
            throw $exception;
        }

        $this->request->session()->flash('error', $message);

        if ($exception instanceof Throwable && $this->shouldLogErrors()) {
            Log::error($exception);
        }
    }

    /**
     * @return bool
     */
    protected function shouldThrowErrors(): bool
    {
        return (bool)config('crudhub.flash.throw_errors', false);
    }

    /**
     * @return bool
     */
    protected function shouldLogErrors(): bool
    {
        return (bool)config('crudhub.flash.log_errors', false);
    }
}
