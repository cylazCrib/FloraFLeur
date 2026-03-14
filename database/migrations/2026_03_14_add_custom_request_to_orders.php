<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'custom_request_id')) {
                $table->foreignId('custom_request_id')->nullable()->constrained('custom_requests')->onDelete('set null')->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'custom_request_id')) {
                $table->dropForeign(['custom_request_id']);
                $table->dropColumn('custom_request_id');
            }
        });
    }
};
