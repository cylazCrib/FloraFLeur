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
    Schema::create('products', function (Blueprint $table) {
        $table->id();

        // This links each product to the shop that owns it
        $table->foreignId('shop_id')->constrained()->onDelete('cascade');

        $table->string('name');
        $table->text('description')->nullable();
        $table->decimal('price', 8, 2);
        $table->string('image')->nullable(); // Path to the product image

        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
