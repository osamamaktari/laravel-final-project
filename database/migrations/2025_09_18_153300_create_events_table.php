<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  // In database/migrations/..._create_events_table.php

public function up(): void
{
    Schema::create("events", function (Blueprint $table) {
        $table->id();
        $table->foreignId("organizer_id")->constrained("users")->onDelete("cascade");
        $table->string("title");
        $table->text("description")->nullable();
        $table->string("venue")->nullable();
        $table->dateTime("start_date");
        $table->dateTime("end_date")->nullable();
        $table->string("banner_url")->nullable();
        $table->enum("status", ["pending", "approved", "rejected", "cancelled"])->default("pending");
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
