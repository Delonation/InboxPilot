<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()
                ->constrained('email_templates')->nullOnDelete();
            $table->string('name');
            $table->string('subject_override')->nullable();
            $table->enum('status', [
                'draft', 'sending', 'completed', 'completed_with_errors', 'failed',
            ])->default('draft');

            // Snapshot of the sender/SMTP used, so reports stay accurate even if
            // the user later changes their SMTP settings. Never stores secrets.
            $table->string('sender_email')->nullable();
            $table->string('smtp_summary')->nullable();

            $table->unsignedInteger('total_recipients')->default(0);
            $table->unsignedInteger('total_attempted')->default(0);
            $table->unsignedInteger('total_sent')->default(0);
            $table->unsignedInteger('total_failed')->default(0);
            $table->unsignedInteger('total_skipped')->default(0);

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
