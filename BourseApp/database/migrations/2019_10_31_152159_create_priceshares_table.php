<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricesharesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_shares', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('share_id')->index();
            $table->foreign('share_id')
                ->references('id')
                ->on('shares')
                ->onDelete('cascade');           
            $table->timestamp('date');
            $table->float('open')->default(0.0);
            $table->float('highest')->default(0.0);
            $table->float('lowest')->default(0.0);
            $table->float('close')->default(0.0);
            $table->float('volume',16,2)->default(0.0);
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
        Schema::dropIfExists('price_shares');
    }
}
