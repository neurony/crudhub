<?php

use Illuminate\Support\Facades\Route;
use Zbiller\Crudhub\Controllers\AdminController;
use Zbiller\Crudhub\Controllers\Auth\AuthenticatedSessionController;
use Zbiller\Crudhub\Controllers\Auth\PasswordForgotController;
use Zbiller\Crudhub\Controllers\Auth\PasswordResetController;
use Zbiller\Crudhub\Controllers\Auth\TwoFactorController;
use Zbiller\Crudhub\Controllers\DashboardController;
use Zbiller\Crudhub\Controllers\PermissionController;
use Zbiller\Crudhub\Controllers\UploadController;
use Zbiller\Crudhub\Controllers\UserController;

$prefix = config('crudhub.admin.prefix', 'admin');

$controllers = [
    'dashboard' => '\\' . config('crudhub.bindings.controllers.dashboard_controller', DashboardController::class),
    'login' => '\\' . config('crudhub.bindings.controllers.login_controller', AuthenticatedSessionController::class),
    'two_factor' => '\\' . config('crudhub.bindings.controllers.two_factor_controller', TwoFactorController::class),
    'password_forgot' => '\\' . config('crudhub.bindings.controllers.password_forgot_controller', PasswordForgotController::class),
    'password_reset' => '\\' . config('crudhub.bindings.controllers.password_reset_controller', PasswordResetController::class),
    'upload' => '\\' . config('crudhub.bindings.controllers.upload_controller', UploadController::class),
    'admin' => '\\' . config('crudhub.bindings.controllers.admin_controller', AdminController::class),
    'user' => '\\' . config('crudhub.bindings.controllers.user_controller', UserController::class),
    'permission' => '\\' . config('crudhub.bindings.controllers.permission_controller', PermissionController::class),
];

Route::prefix($prefix)->middleware([
    'web',
    'crudhub.inertia.handle_requests',
])->group(function () use ($controllers) {
    /**
     * Dashboard routes
     */
    Route::middleware([
        'auth:admin'
    ])->group(function () use ($controllers) {
        Route::get('', [$controllers['dashboard'], 'index'])->name('admin.dashboard');
    });

    /**
     * Login routes
     */
    Route::middleware([
        'guest:admin'
    ])->group(function () use ($controllers) {
        Route::get('login', [$controllers['login'], 'create'])->name('admin.login.create');
        Route::post('login', [$controllers['login'], 'store'])->name('admin.login.store');
    });

    /**
     * Two-factor routes
     */
    Route::prefix('two-factor')->middleware([
        'guest:admin'
    ])->group(function () use ($controllers) {
        Route::get('', [$controllers['two_factor'], 'show'])->name('admin.two_factor.show');
        Route::post('request', [$controllers['two_factor'], 'request'])->name('admin.two_factor.request');
        Route::post('check', [$controllers['two_factor'], 'check'])->name('admin.two_factor.check');
    });

    /**
     * Password routes
     */
    Route::prefix('password')->middleware([
        'guest:admin'
    ])->group(function () use ($controllers) {
        Route::get('forgot', [$controllers['password_forgot'], 'create'])->name('admin.password_forgot.create');
        Route::post('forgot', [$controllers['password_forgot'], 'store'])->name('admin.password_forgot.store');

        Route::get('reset/{token}', [$controllers['password_reset'], 'create'])->name('admin.password_reset.create');
        Route::post('reset', [$controllers['password_reset'], 'store'])->name('admin.password_reset.store');
    });

    /**
     * Logout routes
     */
    Route::middleware([
        'auth:admin'
    ])->group(function () use ($controllers) {
        Route::post('logout', [$controllers['login'], 'destroy'])->name('admin.login.destroy');
    });

    /**
     * Upload routes
     */
    Route::prefix('upload')->middleware([
        'auth:admin',
    ])->group(function () use ($controllers) {
        Route::post('', [$controllers['upload'], 'store'])->name('admin.uploads.store');
    });

    /**
     * Admin routes
     */
    Route::prefix('admins')->middleware([
        'auth:admin',
    ])->group(function () use ($controllers) {
        Route::get('', [$controllers['admin'], 'index'])->name('admin.admins.index')->middleware('permission:admins-list');
        Route::get('create', [$controllers['admin'], 'create'])->name('admin.admins.create')->middleware('permission:admins-add');
        Route::post('', [$controllers['admin'], 'store'])->name('admin.admins.store')->middleware('permission:admins-add');
        Route::get('{admin}/edit', [$controllers['admin'], 'edit'])->name('admin.admins.edit');
        Route::put('{admin}', [$controllers['admin'], 'update'])->name('admin.admins.update');
        Route::delete('{admin}', [$controllers['admin'], 'destroy'])->name('admin.admins.destroy')->middleware('permission:admins-delete');
        Route::patch('{id}', [$controllers['admin'], 'partialUpdate'])->name('admin.admins.partial_update')->middleware('permission:admins-edit');
        Route::post('bulk-destroy', [$controllers['admin'], 'bulkDestroy'])->name('admin.admins.bulk_destroy')->middleware('permission:admins-delete');
    });

    /**
     * User routes
     */
    Route::prefix('users')->middleware([
        'auth:admin',
    ])->group(function () use ($controllers) {
        Route::get('', [$controllers['user'], 'index'])->name('admin.users.index')->middleware('permission:users-list');
        Route::get('create', [$controllers['user'], 'create'])->name('admin.users.create')->middleware('permission:users-add');
        Route::post('', [$controllers['user'], 'store'])->name('admin.users.store')->middleware('permission:users-add');
        Route::get('{user}/edit', [$controllers['user'], 'edit'])->name('admin.users.edit')->middleware('permission:users-edit');
        Route::put('{user}', [$controllers['user'], 'update'])->name('admin.users.update')->middleware('permission:users-edit');
        Route::delete('{user}', [$controllers['user'], 'destroy'])->name('admin.users.destroy')->middleware('permission:users-delete');
        Route::patch('{id}', [$controllers['user'], 'partialUpdate'])->name('admin.users.partial_update')->middleware('permission:users-edit');
        Route::post('bulk-destroy', [$controllers['user'], 'bulkDestroy'])->name('admin.users.bulk_destroy')->middleware('permission:users-delete');
    });

    /**
     * Permission routes
     */
    Route::prefix('permissions')->middleware([
        'auth:admin',
    ])->group(function () use ($controllers) {
        Route::get('', [$controllers['permission'], 'index'])->name('admin.permissions.index')->middleware('permission:permissions-list');
        Route::post('', [$controllers['permission'], 'update'])->name('admin.permissions.update')->middleware('permission:permissions-edit');
    });
});
