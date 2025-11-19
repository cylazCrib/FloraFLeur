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
    Schema::create('shops', function (Blueprint $table) {
        $table->id();

        // This links the shop to the user who owns it
        $table->foreignId('user_id')->constrained()->onDelete('cascade');

        // Shop Details
        $table->string('name');
        $table->text('description')->nullable();
        $table->string('phone')->nullable();

        // Business Address
        $table->string('address')->nullable();
        $table->string('zip_code')->nullable();
        $table->text('delivery_coverage')->nullable();

        // Admin Fields
        $table->string('permit_url')->nullable(); // Path to the uploaded DTI/Business Permit
        $table->string('status')->default('pending'); // 'pending', 'approved', 'suspended'

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
