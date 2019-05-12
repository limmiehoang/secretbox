<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEncFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enc_files', function (Blueprint $table) {
            $table->string('id', 16)->index()->unique();
            $table->primary('id');
            $table->string('group_id', 16)->index();
            $table->string('prefix')->nullable();
            $table->binary('enc_metadata');

            $table->foreign('group_id')->references('id')->on('groups')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enc_files');
    }
}
