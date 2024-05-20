<?php

use Illuminate\Http\Request;
use Zbiller\Crudhub\Contracts\FlashHelperContract;

if (!function_exists('flash')) {
    /**
     * @return FlashHelperContract
     */
    function flash(): FlashHelperContract
    {
        return app(FlashHelperContract::class);
    }
}

if (!function_exists('has_query_filled')) {
    /**
     * @param Request $request
     * @param array $ignore
     * @return bool
     */
    function has_query_filled(Request $request, array $ignore = []): bool
    {
        foreach ($request->query() as $key => $val) {
            if (in_array($key, $ignore)) {
                continue;
            }

            if (!empty($val)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('class_uses_trait')) {
    /**
     * @param object|string $class
     * @param string $trait
     * @return bool
     */
    function class_uses_trait(object|string $class, string $trait): bool
    {
        return in_array($trait, class_uses_recursive($class));
    }
}
