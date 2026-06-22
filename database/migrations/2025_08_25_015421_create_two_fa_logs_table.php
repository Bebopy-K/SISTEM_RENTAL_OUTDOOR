<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(config('2fa.tables.logs', 'two_fa_logs'), function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->index();
            $table->string('event')->index();
            $table->string('ip', 64)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('2fa.tables.logs', 'two_fa_logs'));
    }
};
