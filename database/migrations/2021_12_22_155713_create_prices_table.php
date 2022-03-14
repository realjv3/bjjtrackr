<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->string('id', 40)->primary()->comment('Matches Stripe price id');
            $table->unsignedBigInteger('client_id');
            $table->string('product_id', 40);
            $table->unsignedInteger('amount');
            $table->boolean('recurring')->default(false);
            $table->string('period')->nullable();
            $table->unsignedTinyInteger('period_count')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('prices', function(Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prices', function(Blueprint $table) {
            $table->dropForeign('prices_client_id_foreign');
            $table->dropForeign('prices_product_id_foreign');
        });
        Schema::dropIfExists('prices');
    }
}
