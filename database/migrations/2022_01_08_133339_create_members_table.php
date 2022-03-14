<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('user_id');
            $table->string('cust_id', 30);
            $table->string('subscription_id', 30)->nullable();
            $table->string('item_id', 30)->nullable();
            $table->unsignedBigInteger('current_period_end')->nullable();
            $table->string('price_id', 40)->nullable();
            $table->string('status', 30)->nullable();
            $table->boolean('pause_collection')->default(false);
            $table->unsignedInteger('resumes_at')->nullable();
            $table->boolean('cancel_at_period_end')->default(false);
            $table->timestamps();
        });

        Schema::table('members', function(Blueprint $table) {

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('price_id')->references('id')->on('prices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('members', function(Blueprint $table) {

            $table->dropForeign('members_client_id_foreign');
            $table->dropForeign('members_user_id_foreign');
            $table->dropForeign('members_price_id_foreign');
        });
        Schema::dropIfExists('members');
    }
}
