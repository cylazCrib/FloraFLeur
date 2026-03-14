<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->string('email')->nullable()->after('phone'); // Shop email
            $table->string('gcash_qr_url')->nullable()->after('email'); // GCash QR Code
            $table->string('maya_qr_url')->nullable()->after('gcash_qr_url'); // Maya QR Code
            $table->text('payment_instructions')->nullable()->after('maya_qr_url'); // Payment instructions
        });
    }

    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['email', 'gcash_qr_url', 'maya_qr_url', 'payment_instructions']);
        });
    }
};
