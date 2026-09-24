<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('signed_url_accesses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('signed_url_id')
                ->constrained('signed_urls')
                ->cascadeOnDelete();

            $table->ipAddress('ip_address')->nullable();

            $table->text('user_agent')->nullable();

            $table->timestamp('accessed_at');

            $table->timestamps();

            $table->index('accessed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signed_url_accesses');
    }
};