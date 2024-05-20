<?php

/*
| ------------------------------------------------------------------------------------------------------------------
| Class Bindings
| ------------------------------------------------------------------------------------------------------------------
|
| FQNs of the classes used by the Crudhub platform internally to achieve different functionalities.
| Each of these classes represents a concrete implementation that is bound to the Laravel IoC container.
|
| If you need to extend or modify a functionality, you can swap the implementation below with your own class.
| Swapping the implementation, requires some steps, like extending the core class, or implementing an interface.
|
*/
return [

    /*
    | --------------------------------------------------------------------------------------------------------------
    | Model Class Bindings
    | --------------------------------------------------------------------------------------------------------------
    */
    'models' => [

        /*
        |
        | Concrete implementation for the "user model".
        | To extend or replace this functionality, change the value below with your full "user model" FQN.
        |
        */
        'user_model' => \App\Models\User::class,

        /*
        |
        | Concrete implementation for the "admin model".
        | To extend or replace this functionality, change the value below with your full "admin model" FQN.
        |
        */
        'admin_model' => \Zbiller\Crudhub\Models\Admin::class,

        /*
        |
        | Concrete implementation for the "media model".
        | To extend or replace this functionality, change the value below with your full "media model" FQN.
        |
        */
        'media_model' => \Zbiller\Crudhub\Models\Media::class,

        /*
        |
        | Concrete implementation for the "media_unassigned model".
        | To extend or replace this functionality, change the value below with your full "media_unassigned model" FQN.
        |
        */
        'media_unassigned_model' => \Zbiller\Crudhub\Models\MediaUnassigned::class,

    ],

    /*
    | --------------------------------------------------------------------------------------------------------------
    | Resource Class Bindings
    | --------------------------------------------------------------------------------------------------------------
    */
    'resources' => [

        /*
        |
        | Concrete implementation for the "user resource".
        | To extend or replace this functionality, change the value below with your full "user resource" FQN.
        |
        */
        'user_resource' => \Zbiller\Crudhub\Resources\UserResource::class,

        /*
        |
        | Concrete implementation for the "admin resource".
        | To extend or replace this functionality, change the value below with your full "admin resource" FQN.
        |
        */
        'admin_resource' => \Zbiller\Crudhub\Resources\AdminResource::class,

        /*
        |
        | Concrete implementation for the "media resource".
        | To extend or replace this functionality, change the value below with your full "media resource" FQN.
        |
        */
        'media_resource' => \Zbiller\Crudhub\Resources\MediaResource::class,

    ],

    /*
    | --------------------------------------------------------------------------------------------------------------
    | Controller Class Bindings
    | --------------------------------------------------------------------------------------------------------------
    */
    'controllers' => [

        /*
        |
        | Concrete implementation for the "login controller".
        | To extend or replace this functionality, change the value below with your full "login controller" FQN.
        |
        */
        'login_controller' => \Zbiller\Crudhub\Controllers\Auth\AuthenticatedSessionController::class,

        /*
        |
        | Concrete implementation for the "two-factor controller".
        | To extend or replace this functionality, change the value below with your full "two-factor controller" FQN.
        |
        */
        'two_factor_controller' => \Zbiller\Crudhub\Controllers\Auth\TwoFactorController::class,

        /*
        |
        | Concrete implementation for the "password_forgot controller".
        | To extend or replace this functionality, change the value below with your full "password_forgot controller" FQN.
        |
        */
        'password_forgot_controller' => \Zbiller\Crudhub\Controllers\Auth\PasswordForgotController::class,

        /*
        |
        | Concrete implementation for the "password_reset controller".
        | To extend or replace this functionality, change the value below with your full "password_reset controller" FQN.
        |
        */
        'password_reset_controller' => \Zbiller\Crudhub\Controllers\Auth\PasswordResetController::class,

        /*
        |
        | Concrete implementation for the "upload controller".
        | To extend or replace this functionality, change the value below with your full "upload controller" FQN.
        |
        */
        'upload_controller' => \Zbiller\Crudhub\Controllers\UploadController::class,

        /*
        |
        | Concrete implementation for the "dashboard controller".
        | To extend or replace this functionality, change the value below with your full "dashboard controller" FQN.
        |
        */
        'dashboard_controller' => \Zbiller\Crudhub\Controllers\DashboardController::class,

        /*
        |
        | Concrete implementation for the "admin controller".
        | To extend or replace this functionality, change the value below with your full "admin controller" FQN.
        |
        */
        'admin_controller' => \Zbiller\Crudhub\Controllers\AdminController::class,

        /*
        |
        | Concrete implementation for the "permission controller".
        | To extend or replace this functionality, change the value below with your full "permission controller" FQN.
        |
        */
        'permission_controller' => \Zbiller\Crudhub\Controllers\PermissionController::class,

    ],

    'form_requests' => [

        /*
        |
        | Concrete implementation for the "login form request".
        | To extend or replace this functionality, change the value below with your full "login form request" FQN.
        |
        */
        'login_form_request' => \Zbiller\Crudhub\Requests\LoginRequest::class,

        /*
        |
        | Concrete implementation for the "user form request".
        | To extend or replace this functionality, change the value below with your full "user form request" FQN.
        |
        */
        'user_form_request' => \Zbiller\Crudhub\Requests\UserRequest::class,

        /*
        |
        | Concrete implementation for the "admin form request".
        | To extend or replace this functionality, change the value below with your full "admin form request" FQN.
        |
        */
        'admin_form_request' => \Zbiller\Crudhub\Requests\AdminRequest::class,

    ],

    /*
    | --------------------------------------------------------------------------------------------------------------
    | Filter Class Bindings
    | --------------------------------------------------------------------------------------------------------------
    */
    'filters' => [

        /*
        |
        | Concrete implementation for the "admin filter".
        | To extend or replace this functionality, change the value below with your full "admin filter" FQN.
        |
        */
        'admin_filter' => \Zbiller\Crudhub\Filters\AdminFilter::class,

        /*
        |
        | Concrete implementation for the "user filter".
        | To extend or replace this functionality, change the value below with your full "user filter" FQN.
        |
        */
        'user_filter' => \Zbiller\Crudhub\Filters\UserFilter::class,

    ],

    /*
    | --------------------------------------------------------------------------------------------------------------
    | Sort Class Bindings
    | --------------------------------------------------------------------------------------------------------------
    */
    'sorts' => [

        /*
        |
        | Concrete implementation for the "admin sort".
        | To extend or replace this functionality, change the value below with your full "admin sort" FQN.
        |
        */
        'admin_sort' => \Zbiller\Crudhub\Sorts\AdminSort::class,

        /*
        |
        | Concrete implementation for the "user sort".
        | To extend or replace this functionality, change the value below with your full "user sort" FQN.
        |
        */
        'user_sort' => \Zbiller\Crudhub\Sorts\UserSort::class,

    ],

    /*
    | --------------------------------------------------------------------------------------------------------------
    | Helper Class Bindings
    | --------------------------------------------------------------------------------------------------------------
    */
    'helpers' => [

        /*
        |
        | Concrete implementation for the "flash helper".
        | To extend or replace this functionality, change the value below with your full "flash helper" FQN.
        |
        */
        'flash_helper' => \Zbiller\Crudhub\Helpers\FlashHelper::class,

    ],

    /*
    | --------------------------------------------------------------------------------------------------------------
    | Middleware Class Bindings
    | --------------------------------------------------------------------------------------------------------------
    */
    'middleware' => [

        /*
        |
        | Concrete implementation for the "inertia handle requests middleware".
        | To extend or replace this functionality, change the value below with your full "inertia handle requests middleware" FQN.
        |
        | Once the value below is changed, your new middleware will be automatically registered with the application.
        | You can then use the middleware by its alias: "crudhub.inertia.handle_requests"
        |
        */
        'inertia_handle_requests_middleware' => \Zbiller\Crudhub\Middleware\HandleInertiaRequests::class,

    ],

];
