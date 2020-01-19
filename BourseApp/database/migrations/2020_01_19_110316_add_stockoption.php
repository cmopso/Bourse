<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStockoption extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        //Schema::table('shares', function (Blueprint $table) {
        //    $table->enum('type', ['share', 'option', 'tracker', 'fund', 'indice'])->default('share');
        //});
        DB::statement("ALTER TABLE shares MODIFY COLUMN type ENUM('share', 'option', 'tracker', 'fund', 'indice')");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        //Schema::table('shares', function (Blueprint $table) {
        //    $table->enum('type', ['share', 'tracker', 'fund', 'indice'])->default('share');
        //});
        DB::statement("ALTER TABLE shares MODIFY COLUMN type ENUM('share', 'tracker', 'fund', 'indice')");
    }
}
