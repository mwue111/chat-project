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
        Schema::create('chat_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_name', 250);
            $table->string('type', 25);
            $table->string('resolution', 50)->nullable();
            $table->string('size', 50);
            $table->string('uniqid', 50);
            $table->string('file', 250);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_files');
    }
};
