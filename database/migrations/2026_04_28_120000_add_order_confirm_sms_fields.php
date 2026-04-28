<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_gateways', function (Blueprint $table) {
            $table->text('order_confirm_message')->nullable()->after('order');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('confirm_sms_sent')->default(0)->after('complete_sms_sent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('confirm_sms_sent');
        });

        Schema::table('sms_gateways', function (Blueprint $table) {
            $table->dropColumn('order_confirm_message');
        });
    }
};
