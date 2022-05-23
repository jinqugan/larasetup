<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOneTimePasswordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('one_time_passwords', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment('otp_id');
            $table->uuid('user_id')->nullable();
            $table->string('username', 100);
            $table->string('entry', 100)->nullable();
            $table->string('user_type', 100)->nullable();
            $table->string('otp', 10);
            $table->unsignedSmallInteger('retry')->default(0);
            $table->string('session_id')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->json('form_data')->nullable();
            $table->dateTime('resend_at')->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->timestamps();
        });

        /*
         * Schema for index creation
         */
        Schema::table('one_time_passwords', function (Blueprint $table) {
            $table->index(['username', 'entry']);
            $table->index(['session_id']);

            // $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('one_time_passwords');
    }
}
