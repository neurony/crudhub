<?php

namespace Zbiller\Crudhub\Contracts;

interface FlashHelperContract
{
    /**
     * @param string $message
     * @return void
     */
    public function success(string $message = 'Operation successful'): void;

    /**
     * @param string $message
     * @param \Throwable|null $exception
     * @return void
     * @throws \Throwable
     */
    public function error(string $message = 'Operation failed', ?\Throwable $exception = null): void;
}
