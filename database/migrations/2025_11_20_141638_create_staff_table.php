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
    Schema::create('staff', function (Blueprint $table) {
        $table->id();
        // Link to the shop
        $table->foreignId('shop_id')->constrained()->onDelete('cascade');

        $table->string('name');
        $table->string('email');
        $table->string('phone');
        $table->string('role'); // e.g., Manager, Florist, Driver
        $table->string('status')->default('Active'); // Active, Suspended

        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
