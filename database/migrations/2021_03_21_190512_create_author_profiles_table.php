<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('author_profiles', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('user_id');
            $table->string('url')->unique();
            $table->string('full_name')->nullable();
            $table->string('display_name');
            $table->string('image')->nullable();
            $table->text('biography')->nullable();
            $table->text('social_links');
            $table->text('metadata');
            $table->datetime('verified_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('author_profiles');
    }
}
