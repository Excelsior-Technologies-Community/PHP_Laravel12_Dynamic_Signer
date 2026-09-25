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

            $table->string('name')
                ->nullable()
                ->after('id');

            $table->unsignedInteger('max_accesses')
                ->nullable()
                ->after('expires_at');

            $table->boolean('one_time')
                ->default(false)
                ->after('max_accesses');

            $table->timestamp('first_accessed_at')
                ->nullable()
                ->after('last_accessed_at');

            $table->ipAddress('created_ip')
                ->nullable()
                ->after('first_accessed_at');

            $table->text('notes')
                ->nullable()
                ->after('created_ip');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signed_urls', function (Blueprint $table) {

            $table->dropColumn([
                'name',
                'max_accesses',
                'one_time',
                'first_accessed_at',
                'created_ip',
                'notes',
            ]);

        });
    }
};
