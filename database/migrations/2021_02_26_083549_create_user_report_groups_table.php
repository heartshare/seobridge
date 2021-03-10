<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserReportGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_report_groups', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('owner_id')->nullable();
            $table->string('team_id')->nullable();
            $table->string('url')->nullable();
            $table->string('mode')->nullable();
            $table->string('host')->nullable();
            $table->string('device');
            $table->integer('score')->nullable();
            $table->longtext('data');
            $table->text('metadata');
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('team_id')->references('id')->on('teams')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_report_groups');
    }
}
