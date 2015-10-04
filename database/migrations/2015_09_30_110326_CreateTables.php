<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   

        //create trade table
        Schema::create('trade', function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->string('name',30);
        });

        //create role table
        Schema::create('role', function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->string('role',15);
        });

        //create attendance table
        Schema::create('attendance', function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->timestamp('att_date');
        });

        //create user table
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name',100);
            $table->integer('employee_no')->unsigned();
            $table->string('password', 60);
            $table->integer('role_id')->unsigned();
            $table->rememberToken();
            $table->foreign('role_id')
                  ->references('id')->on('role')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->index('employee_no');
        });

        //create site table
        Schema::create('site', function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->string('name',100);
            $table->string('code',10);
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                  ->references('id')->on('user')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        //create labor table
        Schema::create('labor', function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('employee_no')->unsigned();
            $table->string('name',100);
            $table->integer('trade_id')->unsigned();
            $table->foreign('trade_id')
                  ->references('id')->on('trade')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->integer('site_id')->unsigned();
            $table->foreign('site_id')
                  ->references('id')->on('site')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->index('employee_no');
            $table->string('deleted',10);
        });

        //create labor_attendance table
        Schema::create('labor_attendance',function(Blueprint $table){
            $table->integer('labor_id')->unsigned();
            $table->foreign('labor_id')
                  ->references('id')->on('labor')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->integer('attendance_id')->unsigned();
            $table->foreign('attendance_id')
                  ->references('id')->on('attendance')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->tinyInteger('attended')->unsigned()->nullable();
            $table->tinyInteger('ot')->unsigned()->nullable();
            $table->tinyInteger('bot')->unsigned()->nullable();
            $table->string('site',10);
            $table->string('locked',10);
            $table->index(['labor_id','attendance_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {         
        //drop labor_attendance table
        Schema::drop('labor_attendance');

        //drop labor table
        Schema::drop('labor');

        //drop site table
        Schema::drop('site');

        //drop user table
        Schema::drop('user');

        //drop attendance table
        Schema::drop('attendance');

        //drop role table
        Schema::drop('role');

        //drop trade table
        Schema::drop('trade');
    }
}
