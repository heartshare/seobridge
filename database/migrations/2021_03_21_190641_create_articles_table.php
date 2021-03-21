<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('url')->unique();
            $table->string('title')->nullable();
            $table->string('intro_image')->nullable();
            $table->text('intro_text')->nullable();
            $table->longtext('full_text')->nullable();
            $table->string('author_id')->nullable();
            $table->string('category_id')->nullable();
            $table->text('metadata');
            $table->datetime('published_at')->nullable();
            $table->timestamps();

            $table->foreign('author_id')->references('id')->on('author_profiles')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('article_categories')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
