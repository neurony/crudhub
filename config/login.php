<?php

return [

    /*
    |
    | Enable the Two-Factor Authentication mechanism for admin login.
    | By default, the two-factor authentication works by sending an email with a code to the user trying to log in.
    |
    */
    'two_factor_enabled' => false,

    /*
    |
    | The expiration (in minutes) for the generated two-factor code.
    |
    */
    'two_factor_code_expiration' => 60,

    /*
    |
    | Allow only active users to login.
    | If an inactive user enters his correct credentials, he'll still not be able to login.
    |
    */
    'allow_active_only' => true,

    /*
    |
    | How many times should the authentication request be allowed before it reaches a rate limit.
    | This will be increased on every failed authentication request, until it reaches the number below.
    | After that, the user will be locked out before trying again.
    |
    */
    'allowed_attempts' => 3,

    /*
    |
    | How much time (in seconds) should the user be locked if he exceeded his allowed attempts.
    | After the below time passes, the user will be able to submit authentication requests again.
    |
    */
    'lockout_time' => 60,

    /*
    |
    | The route name (as string) to redirect to after a successful login.
    | It defaults to "admin.dashboard"
    |
    */
    'redirect_after_login' => 'admin.dashboard',

    /*
    |
    | The route name (as string) to redirect to after a logging out the user.
    | It defaults to "admin.login.create"
    |
    */
    'redirect_after_logout' => 'admin.login.create',

];
