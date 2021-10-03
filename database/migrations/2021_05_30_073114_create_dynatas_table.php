<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDynatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dynatas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('projectId')->nullable();
            $table->integer('lineItemId')->nullable();
            $table->integer('count')->nullable();
            $table->integer('total')->nullable();
            $table->string('incentive')->nullable();
            $table->string('title')->nullable();
            $table->string('countryISOCode')->nullable();
            $table->LONGTEXT('filters')->nullable();
            $table->LONGTEXT('quotaGroups')->nullable();
            $table->datetime('ctime')->nullable();
            $table->datetime('mtime')->nullable();
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
        Schema::dropIfExists('dynatas');
    }
}
