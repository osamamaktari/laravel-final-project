<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $this->authorize("isAdmin");

        $totalRevenue = Order::where("status", OrderStatus::PAID)->sum("total_amount");
        $totalTicketsSold = Ticket::where("status", "!=", \App\Enums\TicketStatus::CANCELLED)->count();
        $totalEvents = Event::count();
        $totalAttendees = User::where("role", "attendee")->count();

        // Tickets Sold Per Event (Bar Chart Data)
        $ticketsSoldPerEvent = Event::withCount(["tickets as total_tickets_sold" => function ($query) {
            $query->where("status", "!=", \App\Enums\TicketStatus::CANCELLED);
        }])
            ->orderByDesc("total_tickets_sold")
            ->limit(5)
            ->get(["id", "title", "total_tickets_sold"]);


        $revenueOverTime = Order::select(
            DB::raw("DATE(created_at) as date"),
            DB::raw("SUM(total_amount) as daily_revenue")
        )
            ->where("status", OrderStatus::PAID)
            ->where("created_at", ">=", now()->subDays(7))
            ->groupBy("date")
            ->orderBy("date", "asc")
            ->get();

        // Top Performing Events
        $topEvents = Event::withCount(["orders as total_orders"])
            ->orderByDesc("total_orders")
            ->limit(5)
            ->get(["id", "title"]);

        // Recent Orders
        $recentOrders = Order::with("user", "event")
            ->orderBy("created_at", "desc")
            ->limit(5)
            ->get();

       return response()->json([
    "totalRevenue" => $totalRevenue,
    "totalTicketsSold" => $totalTicketsSold,
    "totalEvents" => $totalEvents,
    "totalAttendees" => $totalAttendees,

    // Bar Chart
    "ticketsSoldPerEvent" => $ticketsSoldPerEvent->map(function ($event) {
        return [
            "title" => $event->title,
            "ticketsSold" => $event->total_tickets_sold,
        ];
    }),

    // Line Chart
    "revenueByMonth" => $revenueOverTime->map(function ($row) {
        return [
            "month" => $row->date,
            "revenue" => $row->daily_revenue,
        ];
    }),

    // Top Events
    "topEvents" => $topEvents->map(function ($event) {
        return [
            "id" => $event->id,
            "title" => $event->title,
            "revenue" => $event->total_orders,
        ];
    }),

    // Recent Orders
    "recentOrders" => $recentOrders->map(function ($order) {
        return [
            "id" => $order->id,
            "attendeeName" => $order->user->name ?? 'N/A',
            "eventName" => $order->event->title ?? 'N/A',
            "totalAmount" => (float) $order->total_amount,
        ];
    }),
]);
    }
}
