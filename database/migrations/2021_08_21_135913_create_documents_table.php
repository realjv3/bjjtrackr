<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('user_id');
            $table->string('file_name')->nullable();
            $table->string('original_name')->nullable();
            $table->string('template_id')->nullable();
            $table->string('status')->default('processing');
            $table->string('contract_id')->nullable();
            $table->text('contract_pdf_url')->nullable();
            $table->timestamps();
        });

        Schema::table('documents', function(Blueprint $table) {
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
        Schema::table('documents', function(Blueprint $table) {
            $table->dropForeign('documents_client_id_foreign');
            $table->dropForeign('documents_user_id_foreign');
        });
        Schema::dropIfExists('documents');
    }
}
