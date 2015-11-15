<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LoanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan', function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('amount')->unsigned();
            $table->tinyInteger('months-to-pay')->unsigned();
            $table->timestamp('starting_date');
            $table->tinyInteger('interval')->unsigned();
            $table->integer('deduction')->unsigned();
            $table->integer('labor_id')->unsigned();
            $table->foreign('labor_id')
                  ->references('id')->on('labor')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->index('labor_id');
        });

        Schema::create('loan_months', function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->timestamp('deduction_date');
            $table->integer('loan_id')->unsigned();
            $table->foreign('loan_id')
                  ->references('id')->on('loan')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');      
            $table->index('loan_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('loan');
        Schema::drop('loan_months');
    }
}
