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
        Schema::table('ziwo-client-sla', function (Blueprint $table) {
            $table->id();
            $table->string('instance')->index();;
            $table->bigInteger('total_all_queries')->unsigned();
            $table->bigInteger('total_queries')->unsigned();
            $table->bigInteger('successful_queries')->unsigned();
            $table->bigInteger('failed_queries')->unsigned();
            $table->decimal('calculated_sla',8,3);
            $table->timestamps();
//            $table->index(['instance']);
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
