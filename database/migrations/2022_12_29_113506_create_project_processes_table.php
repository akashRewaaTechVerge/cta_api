<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_processes', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->nullable();
            $table->unsignedInteger('project_id')->nullable();
            $table->unsignedBigInteger('updated_by')->default(0)->nullable();
            $table->unsignedBigInteger('created_by_user_id')->default(0)->nullable();
            $table->boolean('project_status')->comment('1 - file_details | 2 - review_by_cta | 3 - send_to_fincen | 4 - cta_filled')->default(1)->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('project_processes');
    }
}
