<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->index();
            $table->string('title')->nullable();
            $table->string('type')->nullable()->index();
            $table->string('mobileno')->nullable();
            $table->string('address_1');
            $table->string('address_2')->nullable();
            $table->unsignedInteger('country_id')->nullable()->index();
            $table->unsignedInteger('state_id')->nullable()->index();
            $table->unsignedInteger('city_id')->nullable()->index();
            $table->string('postal_code');
            $table->boolean('default')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('city_id')->references('id')->on('cities');
            // $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
