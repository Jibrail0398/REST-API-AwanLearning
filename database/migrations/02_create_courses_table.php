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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('image')->nullable(false);
            $table->unsignedBigInteger('instructor_id');
            $table->integer('price')->nullable(false);
            $table->enum('status', ['confirm', 'not_confirm'])->default('not_confirm');
            $table->string('pre_vidio')->nullable(false);
            $table->timestamps();

            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
