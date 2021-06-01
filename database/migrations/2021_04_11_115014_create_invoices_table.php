<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('invoice_number')->unique();
            $table->date('date')->nullable();
            $table->unsignedBigInteger('contact_id');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->string('client_name')->nullable();
            $table->string('client_phone')->nullable();
            $table->double('total_cost');
            $table->double('paid_price')->nullable();
            $table->double('due_price')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('created_by')->nullable();
            $table->double('discount')->nullable();
            $table->double('vat');
            $table->double('vat_parcentage')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
