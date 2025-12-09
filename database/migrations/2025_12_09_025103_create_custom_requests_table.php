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
    Schema::create('custom_requests', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The Customer
        $table->text('description'); // The request details
        $table->string('status')->default('Pending'); // Pending, Accepted, Completed
        $table->decimal('budget', 10, 2)->nullable(); // Optional budget
        $table->string('contact_number')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('custom_requests');
}
};
