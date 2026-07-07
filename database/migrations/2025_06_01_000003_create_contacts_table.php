<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('tags')->nullable(); // comma-delimited simple tags
            $table->boolean('is_unsubscribed')->default(false);
            $table->timestamps();

            // Prevent duplicate emails per user; speed up active-contact lookups.
            $table->unique(['user_id', 'email']);
            $table->index(['user_id', 'is_unsubscribed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
