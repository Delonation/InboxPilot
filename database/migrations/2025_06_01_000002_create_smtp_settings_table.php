<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smtp_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('host');
            $table->unsignedInteger('port');
            $table->enum('encryption', ['none', 'ssl', 'tls'])->default('tls');
            $table->string('username');
            // AES-256 encrypted via APP_KEY (Laravel Crypt). Never exposed to any UI.
            $table->text('password_encrypted');
            $table->string('from_name');
            $table->string('from_email');
            $table->string('reply_to_email')->nullable();
            $table->timestamp('last_test_passed_at')->nullable();
            $table->string('last_test_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smtp_settings');
    }
};
