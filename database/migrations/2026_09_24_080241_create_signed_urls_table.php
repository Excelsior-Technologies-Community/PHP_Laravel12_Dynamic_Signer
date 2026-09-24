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
        Schema::create('signed_urls', function (Blueprint $table) {
            $table->id();

            $table->text('target_url');

            $table->text('signed_url');

            $table->string('signature')->unique();

            $table->timestamp('expires_at');

            $table->timestamp('revoked_at')->nullable();

            $table->unsignedInteger('access_count')->default(0);

            $table->timestamp('last_accessed_at')->nullable();

            $table->timestamps();

            $table->index('expires_at');
            $table->index('revoked_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signed_urls');
    }
};