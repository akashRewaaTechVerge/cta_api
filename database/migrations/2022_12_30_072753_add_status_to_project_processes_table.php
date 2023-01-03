<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToProjectProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_processes', function (Blueprint $table) {
            $table->boolean('status')->comment('1 - active | 2 - deactive')->default(1)->after('created_by_user_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_processes', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
