<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()
                ->constrained('contacts')->nullOnDelete();
            $table->string('email');
            $table->string('name')->nullable();
            $table->enum('status', [
                'pending', 'processing', 'sent', 'failed', 'skipped_unsubscribed', 'skipped_invalid',
            ])->default('pending');
            $table->text('smtp_response')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            // The sender claims pending rows in batches; this index drives that.
            $table->index(['campaign_id', 'status']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_recipients');
    }
};
