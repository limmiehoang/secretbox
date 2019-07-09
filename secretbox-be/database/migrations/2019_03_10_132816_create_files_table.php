<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->string('id',16)->index()->unique();
            $table->primary('id');
            $table->string('name');
            $table->string('type');
            $table->string('extension');
            $table->string('user_id')->index();
            $table->string('group_id',16)->nullable()->index();
            $table->string('url');
            $table->timestamps();

            $table->foreign('user_id')->references('sub')->on('users')
                ->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')
                ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
