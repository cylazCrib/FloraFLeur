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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            
            // Link to the order
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            
            // Link to the product (optional, in case product is deleted later, we keep the data)
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('product_name');
            $table->integer('quantity');
            $table->decimal('price', 10, 2); // Price at the time of purchase
            
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
