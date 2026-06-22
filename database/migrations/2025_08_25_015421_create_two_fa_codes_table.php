<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(config('2fa.tables.codes', 'two_fa_codes'), function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->index();
            $table->string('purpose')->nullable()->index();
            $table->string('channel', 20)->index();
            $table->string('destination');
            $table->string('code_hash')->nullable()->index();
            $table->string('code_plain')->nullable()->index();
            $table->timestamp('expires_at')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->unsignedInteger('max_attempts')->default(5);
            $table->string('ip', 64)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('used_at')->nullable()->index();
            $table->timestamp('locked_until')->nullable();
            $table->timestamps();

            // Only one active code per user+purpose
            $table->index(['user_id','purpose','used_at','expires_at'], 'two_fa_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('2fa.tables.codes', 'two_fa_codes'));
    }
};
