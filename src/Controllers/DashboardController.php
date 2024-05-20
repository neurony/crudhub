<?php

namespace Zbiller\Crudhub\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class DashboardController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        return inertia('Dashboard');
    }
}
