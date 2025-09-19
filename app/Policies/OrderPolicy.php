<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->user_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can pay the model.
     */
    public function pay(User $user, Order $order): bool
    {
        return ($user->id === $order->user_id && $order->status === \App\Enums\OrderStatus::PENDING) || $user->role === 'admin';
    }


}
