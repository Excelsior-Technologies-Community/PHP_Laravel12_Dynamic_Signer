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
        Schema::table('signed_urls', function (Blueprint $table) {
            $table->string('passcode')->nullable()->after('notes');
            $table->boolean('is_passcode_protected')->default(false)->after('passcode');
            $table->boolean('burn_after_reading')->default(false)->after('is_passcode_protected');
            $table->boolean('is_burned')->default(false)->after('burn_after_reading');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signed_urls', function (Blueprint $table) {
            $table->dropColumn([
                'passcode',
                'is_passcode_protected',
                'burn_after_reading',
                'is_burned',
            ]);
        });
    }
};
