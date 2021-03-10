<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserReportGroupSharesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_report_group_shares', function (Blueprint $table) {
            $table->id();
            $table->string('owner_id')->nullable();
            $table->string('report_group_id')->nullable();
            $table->string('team_id')->nullable();
            $table->string('user_id')->nullable();
            $table->boolean('is_assigned')->default(0);
            $table->string('assigned_to_user_id')->nullable();
            $table->text('metadata');
            $table->datetime('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('report_group_id')->references('id')->on('user_report_groups')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('owner_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('assigned_to_user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_report_group_shares');
    }
}
