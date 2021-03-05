<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserReportTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_report_tasks', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('report_group_id')->nullable();
            $table->string('url')->nullable();
            $table->string('status')->nullable();
            $table->integer('progress')->nullable();
            $table->integer('progress_max')->nullable();
            $table->text('metadata');
            $table->timestamps();

            $table->foreign('report_group_id')->references('id')->on('user_report_groups')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_report_tasks');
    }
}
