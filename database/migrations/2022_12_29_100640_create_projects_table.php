<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->nullable();
            $table->unsignedBigInteger('created_by')->default(0)->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_address')->nullable();
            $table->boolean('formation_type')->comment('1 - US Jurisdiction | 2 - Foreign Formation')->nullable();
            $table->string('state_of_formation')->nullable();
            $table->string('tin_ein_number')->nullable();
            $table->string('foreign_based_company_us')->nullable();
            $table->string('company_registration_number_or_code')->nullable();
            $table->string('country_of_formation')->nullable();
            $table->string('foreign_state_of_formation')->nullable();
            $table->string('foreign_tin_ein_number')->nullable();
            $table->string('foreign_based_company')->nullable();
            $table->string('foreign_company_registration_number_or_code')->nullable();
            $table->string('document')->nullable();
            $table->boolean('status')->default(1)->comment('1 - active | 2 - deactive')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
