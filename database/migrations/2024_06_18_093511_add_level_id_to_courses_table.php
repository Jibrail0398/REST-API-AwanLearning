<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLevelIdToCoursesTable extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedBigInteger('level_id')->after('instructor_id'); // Sesuaikan posisi kolom setelah kolom lain jika perlu
            // Atau sesuaikan dengan struktur tabel Anda
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('level_id');
        });
    }
}
