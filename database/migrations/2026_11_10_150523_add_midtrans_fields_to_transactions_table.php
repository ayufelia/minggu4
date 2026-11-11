<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('midtrans_order_id')->nullable()->unique();
            $table->text('snap_token')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('stock_deducted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique(['midtrans_order_id']);

            $table->dropColumn([
                'midtrans_order_id',
                'snap_token',
                'paid_at',
                'stock_deducted_at',
            ]);
        });
    }
};
