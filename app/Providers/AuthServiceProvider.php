<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Model::class => ModelPolicy::class,
        // nếu không map model cụ thể thì có thể để trống
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('access-admin', fn($user) => $user->role === 'admin');
    }
}
