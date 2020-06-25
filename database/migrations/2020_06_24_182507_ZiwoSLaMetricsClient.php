<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ZiwoSLaMetricsClient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ziwo-client-sla', function (Blueprint $table) {
            $table->increments('id')->primary();
            $table->string('instance');
            $table->bigInteger('total_all_queries')->default(0);
            $table->bigInteger('total_queries')->default(0);
            $table->bigInteger('successful_queries')->default(0);
            $table->bigInteger('failed_queries')->default(0);
            $table->decimal('calculated_sla',8,5);
            $table->timestamps();
            $table->index(['instance']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::table('ziwo-client-sla', function (Blueprint $table) {
//            $table->drop('ziwo-client-sla');
//        });
    }
}
