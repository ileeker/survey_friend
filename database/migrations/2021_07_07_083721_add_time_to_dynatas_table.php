<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeToDynatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dynatas', function (Blueprint $table) {
            //
            $table->string('lengthOfInterview')->after('title')->nullable();
            $table->string('indicativeIncidence')->after('title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dynatas', function (Blueprint $table) {
            //
        });
    }
}
