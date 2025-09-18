<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */


public function up(): void
{
    Schema::create("orders", function (Blueprint $table) {
        $table->id();
        $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
        $table->foreignId('event_id')->constrained('events')->onDelete('cascade');

        $table->decimal("total_amount", 10, 2);
        $table->enum("status", ["pending", "paid", "cancelled", "refunded"])->default("pending");
        $table->string("payment_intent_id")->nullable(); // For payment gateway integration
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
