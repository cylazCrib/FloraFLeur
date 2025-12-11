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
    Schema::table('custom_requests', function (Blueprint $table) {
        if (!Schema::hasColumn('custom_requests', 'shop_id')) {
            $table->foreignId('shop_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');
        }
    });
}

public function down(): void
{
    Schema::table('custom_requests', function (Blueprint $table) {
        $table->dropForeign(['shop_id']);
        $table->dropColumn('shop_id');
    });
}
};
