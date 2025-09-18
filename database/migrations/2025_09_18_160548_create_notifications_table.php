<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // In database/migrations/..._create_notifications_table.php

public function up(): void
{
    Schema::create("notifications", function (Blueprint $table) {
        $table->uuid("id")->primary();
        $table->string("type");
        $table->morphs("notifiable"); // Polymorphic relation to user/other models
        $table->json("data");
        $table->timestamp("read_at")->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
