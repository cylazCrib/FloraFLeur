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
        Schema::table('users', function (Blueprint $table) {
            // Add shop_id nullable because customers won't have a shop
            if (!Schema::hasColumn('users', 'shop_id')) {
                $table->foreignId('shop_id')->nullable()->after('id')->constrained()->onDelete('set null');
            }
            
            // Ensure role column exists too while we are here
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('customer')->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
            $table->dropColumn('shop_id');
        });
    }
};

