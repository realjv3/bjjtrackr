<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->unique();
            $table->string('cust_id', 30);
            $table->string('subscription_id', 30)->nullable();
            $table->string('item_id', 30)->nullable();
            $table->unsignedBigInteger('current_period_end')->nullable();
            $table->string('price_id', 30)->nullable();
            $table->string('status', 30)->nullable();
            $table->timestamps();
        });

        Schema::table('subscriptions', function(Blueprint $table) {

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function(Blueprint $table) {

            $table->dropForeign('subscriptions_client_id_foreign');
        });
        Schema::dropIfExists('subscriptions');
    }
}
