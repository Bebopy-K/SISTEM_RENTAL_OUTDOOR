<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(config('2fa.tables.settings', 'two_fa_settings'), function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->unique();
            $table->boolean('enabled')->default(false);
            $table->string('method')->nullable(); // 'totp' or 'self'
            $table->text('totp_secret')->nullable();
            $table->json('recovery_codes')->nullable();
            $table->timestamp('locked_until')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('2fa.tables.settings', 'two_fa_settings'));
    }
};
