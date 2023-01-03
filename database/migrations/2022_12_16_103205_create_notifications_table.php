<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->nullable();
            $table->unsignedInteger('user_id')->default(0)->nullable();
            $table->unsignedInteger('notification_duration_id')->default(0)->nullable();
            $table->string('title')->nullable();
            $table->string('duration_in_day')->nullable();
            $table->longtext('description')->nullable();
            $table->string('photo')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('result')->nullable();
            $table->boolean('status')->default(1)->comment('1-active | 2-deactive');
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
        Schema::dropIfExists('notifications');
    }
}
