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
    Schema::create('inventory_items', function (Blueprint $table) {
        $table->id();
        // Link to the shop
        $table->foreignId('shop_id')->constrained()->onDelete('cascade');

        $table->string('name');
        $table->string('code')->nullable(); // Optional item code
        $table->integer('quantity')->default(0);
        $table->string('type'); // 'item' (hard goods) or 'flower' (fresh goods)

        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
