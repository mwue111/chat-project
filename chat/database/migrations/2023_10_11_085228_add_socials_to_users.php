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
        Schema::table('users', function (Blueprint $table) {
            $table->string('fb')->after('avatar')->nullable();
            $table->string('tw')->after('fb')->nullable();
            $table->string('ig')->after('tw')->nullable();
            $table->string('lnkdn')->after('ig')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('fb');
            $table->dropColumn('tw');
            $table->dropColumn('ig');
            $table->dropColumn('lnkdn');
        });
    }
};
