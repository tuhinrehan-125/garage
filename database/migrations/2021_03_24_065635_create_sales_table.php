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
            $table->id();
            $table->string('ref_no')->unique()->nullable();

            $table->unsignedBigInteger('contact_id')->nullable();
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');

            $table->unsignedBigInteger('owner_id');
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->decimal('shipping_cost', 20, 2)->nullable();
            $table->enum('sale_status', ['completed', 'pending', 'final'])->nullable();
            $table->decimal('total_sale_quantity', 20, 2)->nullable();
            $table->decimal('subtotal_cost', 20, 2)->nullable();
            $table->decimal('sale_tax', 20, 2)->nullable();
            $table->decimal('sale_discount', 20, 2)->default(0);
            $table->decimal('total_cost', 20, 2)->nullable();
            $table->enum('payment_status', ['paid', 'due', 'partial', 'pending'])->nullable();
            $table->date('sale_date')->nullable();
            $table->text('sale_note')->nullable();
            $table->text('staff_note')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('sales');
    }
}
