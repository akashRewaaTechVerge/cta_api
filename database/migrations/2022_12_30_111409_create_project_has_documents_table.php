<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectHasDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_has_documents', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->nullable();
            $table->unsignedInteger('project_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('document')->nullable();
            $table->string('license_and_passport')->nullable();
            $table->string('passport')->nullable();
            $table->string('type')->nullable();
            $table->boolean('status')->default(1)->comment('1 - active | 2 - deactive ')->nullable();
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
        Schema::dropIfExists('project_has_documents');
    }
}
