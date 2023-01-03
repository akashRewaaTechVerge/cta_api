<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectHasOwnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_has_owners', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->nullable();
            $table->unsignedInteger('project_id')->nullable();
            $table->string('owner_name')->nullable();
            $table->date('dob')->nullable();
            $table->string('address')->nullable();
            $table->boolean('unique_type')->comment('1 - US Jurisdiction | 2 - Foreign Formation')->nullable();
            $table->string('license_and_passport')->nullable();
            $table->string('passport')->nullable();
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
        Schema::dropIfExists('project_has_owners');
    }
}
