<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSamplecubesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('samplecubes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('surveyid')->nullable();
            $table->string('surveyname')->nullable();
            $table->integer('totalquota')->nullable();
            $table->integer('remainquota')->nullable();
            $table->string('country')->nullable();
            $table->string('cpi')->nullable();
            $table->string('loi')->nullable();
            $table->string('ir')->nullable();
            $table->string('url')->nullable();
            $table->string('UpdateTimeStamp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('samplecubes');
    }
}
