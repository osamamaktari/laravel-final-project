<?php

namespace App\Providers;

// use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{

  /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
 protected $policies = [
    Event::class => EventPolicy::class,
    Order::class => OrderPolicy::class,
    Ticket::class => TicketPolicy::class,
];


    /**
     * Register any authentication / authorization services.
     */


    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

  public function boot(): void
    {

        Gate::define("isAdmin", function (User $user) {
            return $user->role === "admin";
        });

        Gate::define("isOrganizer", function (User $user) {
            return $user->role === "organizer";
        });

        Gate::define("isAttendee", function (User $user) {
            return $user->role === "attendee";
        });

        Gate::define("isOrganizerOrAdmin", function (User $user) {
            return $user->role === "organizer" || $user->role === "admin";
        });
    }
}

