<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->string('id', 40)->primary()->comment('Matches Stripe payment intent id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('metadata');
            $table->string('status', 40);
            $table->unsignedBigInteger('total')->default(0);
            $table->string('payment_method', 40);
            $table->timestamps();
        });

        Schema::table('sales', function(Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function(Blueprint $table) {
            $table->dropForeign('sales_client_id_foreign');
            $table->dropForeign('sales_user_id_foreign');
        });
        Schema::dropIfExists('sales');
    }
}
