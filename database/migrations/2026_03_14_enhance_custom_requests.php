<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_requests', function (Blueprint $table) {
            // Add new fields only if they don't already exist
            if (!Schema::hasColumn('custom_requests', 'occasion')) {
                $table->string('occasion')->nullable()->after('description');
            }
            if (!Schema::hasColumn('custom_requests', 'date_needed')) {
                $table->dateTime('date_needed')->nullable()->after('occasion');
            }
            if (!Schema::hasColumn('custom_requests', 'color_preference')) {
                $table->string('color_preference')->nullable()->after('date_needed');
            }
            if (!Schema::hasColumn('custom_requests', 'reference_image_url')) {
                $table->text('reference_image_url')->nullable()->after('color_preference');
            }
            if (!Schema::hasColumn('custom_requests', 'vendor_quote')) {
                $table->decimal('vendor_quote', 10, 2)->nullable()->after('budget');
            }
        });
    }

    public function down(): void
    {
        Schema::table('custom_requests', function (Blueprint $table) {
            if (Schema::hasColumn('custom_requests', 'occasion')) {
                $table->dropColumn('occasion');
            }
            if (Schema::hasColumn('custom_requests', 'date_needed')) {
                $table->dropColumn('date_needed');
            }
            if (Schema::hasColumn('custom_requests', 'color_preference')) {
                $table->dropColumn('color_preference');
            }
            if (Schema::hasColumn('custom_requests', 'reference_image_url')) {
                $table->dropColumn('reference_image_url');
            }
            if (Schema::hasColumn('custom_requests', 'vendor_quote')) {
                $table->dropColumn('vendor_quote');
            }
        });
    }
};
