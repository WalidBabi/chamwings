<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'check-auth' => \App\Http\Middleware\CheckAuthentication::class,
        'manage-reservation' => \App\Http\Middleware\ManageReservationAuthentication::class,
        'read-reservation' => \App\Http\Middleware\ReadReservationAuthentication::class,
        'reset-password' => \App\Http\Middleware\ResetPasswordAuthentication::class,
        'manage-airplane' => \App\Http\Middleware\ManageAirplaneAuthentication::class,
        'read-airplane' => \App\Http\Middleware\ReadAirplaneAuthentication::class,
        'manage-airport' => \App\Http\Middleware\ManageAirportAuthentication::class,
        'read-airport' => \App\Http\Middleware\ReadAirportAuthentication::class,
        'manage-flight' => \App\Http\Middleware\ManageFlightAuthentication::class,
        'read-flight' => \App\Http\Middleware\ReadFlightAuthentication::class,
        'manage-employee' => \App\Http\Middleware\ManageEmployeeAuthentication::class,
        'read-employee' => \App\Http\Middleware\ReadEmployeeAuthentication::class,
        'manage-offer' => \App\Http\Middleware\ManageOfferAuthentication::class,
        'read-offer' => \App\Http\Middleware\ReadOfferAuthentication::class,
        'answer-question' => \App\Http\Middleware\AnswerQuestionAuthentication::class,
        'admin-auth' => \App\Http\Middleware\AdminAuthentication::class,
        'manage-policies' => \App\Http\Middleware\ManagePoliciesAuthentication::class
       ];
}