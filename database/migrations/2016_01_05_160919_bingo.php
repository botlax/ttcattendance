<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Bingo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bi_server', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->tinyInteger('winners');
            $table->string('start',10);
            $table->string('name',100);
            $table->string('password', 60);
            $table->string('mode',20);
        });

        Schema::create('bi_players', function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('status',20);
            $table->string('name',50);
            $table->integer('server_id')->unsigned();
            $table->foreign('server_id')
                  ->references('id')->on('bi_server')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('user_id')
                  ->references('id')->on('user')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->index('user_id');
            $table->index('server_id');
        });

        Schema::create('bi_cards', function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('player_id')->unsigned();
            $table->foreign('player_id')
                  ->references('id')->on('bi_players')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->index('player_id');
        });

        Schema::create('bi_items', function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->tinyInteger('item')->unsigned();
        });

        Schema::create('bi_card_items', function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->string('thicked',10);
            $table->integer('item_id')->unsigned();
            $table->integer('card_id')->unsigned();
            $table->foreign('item_id')
                  ->references('id')->on('bi_items')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('card_id')
                  ->references('id')->on('bi_cards')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->index('card_id');
            $table->index('item_id');
        });

        Schema::create('bi_balls', function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->integer('server_id')->unsigned();
            $table->foreign('item_id')
                  ->references('id')->on('bi_items')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('server_id')
                  ->references('id')->on('bi_server')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->index('server_id');
            $table->index('item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bi_balls');

        Schema::drop('bi_card_items');

        Schema::drop('bi_items');

        Schema::drop('bi_cards');

        Schema::drop('bi_players');

        Schema::drop('bi_server');
    }
}
