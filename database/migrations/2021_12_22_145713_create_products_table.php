<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('id', 40)->primary()->comment('Matches Stripe product id');
            $table->unsignedBigInteger('client_id');
            $table->string('name');
            $table->string('unit');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('products', function(Blueprint $table) {
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
        Schema::table('products', function(Blueprint $table) {
            $table->dropForeign('products_client_id_foreign');
        });
        Schema::dropIfExists('products');
    }
}
