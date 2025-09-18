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
    Schema::create('tickets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ticket_type_id')->constrained('ticket_types')->onDelete('cascade');
        $table->foreignId('attendee_id')->constrained('users')->onDelete('cascade');
        $table->string('qr_code')->unique();
        $table->enum('status', ['valid', 'used', 'cancelled'])->default('valid');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
