<?php namespace Jab\Comments\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateCommentsTable extends Migration
{

    public function up()
    {
        Schema::create('jab_comments_comments', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('cid')->nullable();
            $table->integer('pid')->unsigned();
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->string('hostname')->nullable();
            $table->boolean('published')->default(true);
            $table->string('name')->nullable();
            $table->string('mail')->nullable();
            $table->string('homepage')->nullable();
            $table->text('content')->nullable();
            $table->text('content_html')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('jab_comments_comments');
    }

}
