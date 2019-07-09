<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInitialDataToGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string('initial_user')->nullable()->index();
            $table->string('initial_data', 16)->nullable()->index();

            $table->string('identity_key')->nullable();

            $table->foreign('initial_user')->references('sub')->on('users')
                ->onDelete('SET NULL');
            $table->foreign('initial_data')->references('id')->on('enc_files')
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
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign(['initial_user']);
            $table->dropColumn('initial_user');

            $table->dropForeign(['initial_data']);
            $table->dropColumn('initial_data');
        });
    }
}
