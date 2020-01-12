<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ShareAddFinancialData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shares', function (Blueprint $table) {
            $table->timestamp('dividendDate')->default(carbon::today());
            $table->float('dividendValue')->default(0.0);
            $table->float('fiveYearsAvgDividendYield')->default(0.0);
            $table->float('yield')->default(0.0);
            $table->string('code')->default('');
        });
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shares', function (Blueprint $table) {
            $table->dropColumn('dividendDate');
            $table->dropColumn('dividendValue');
            $table->dropColumn('fiveYearsAvgDividendYield');
            $table->dropColumn('yield');
            $table->dropColumn('code');
        });
        //
    }
}
