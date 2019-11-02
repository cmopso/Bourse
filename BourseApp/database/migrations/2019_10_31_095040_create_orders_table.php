<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('share_id')->index();
            $table->foreign('share_id')
                ->references('id')
                ->on('shares')
                ->onDelete('cascade');           
            $table->timestamp('passedOn');
            $table->enum('type', ['buy', 'sale', 'dividend', 'other'])->defalut('other');
            $table->float('price')->default(0.0);
            $table->float('quantity')->default(0.0);
            $table->float('totalPrice')->default(0.0);
            $table->float('totalChargedPrice')->default(0.0);
            $table->float('charges')->default(0.0);
            $table->float('chargesPercent')->default(0.0);
            $table->string('comment')->nullable()->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
