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
    Schema::table('products', function (Blueprint $table) {
        if (!Schema::hasColumn('products', 'occasion')) $table->string('occasion')->nullable();
    });
    Schema::table('inventory_items', function (Blueprint $table) {
        if (!Schema::hasColumn('inventory_items', 'type')) $table->string('type')->default('item');
        if (!Schema::hasColumn('inventory_items', 'code')) $table->string('code')->nullable();
    });
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'phone')) $table->string('phone')->nullable();
        if (!Schema::hasColumn('users', 'status')) $table->string('status')->default('Active');
    });
}
};
