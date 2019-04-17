<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_user', function (Blueprint $table) {
            $table->string('user_sub')->index();
            $table->string('group_id', 16)->index();
            $table->primary(['user_sub', 'group_id']);
            $table->binary('enc_key')->nullable()->default(null);

            $table->foreign('user_sub')->references('sub')->on('users')
                ->onDelete('cascade');

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
        Schema::dropIfExists('group_user');
    }
}
