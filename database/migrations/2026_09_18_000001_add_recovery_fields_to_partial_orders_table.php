<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('partial_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('partial_orders', 'recovery_email_count')) {
                $table->unsignedTinyInteger('recovery_email_count')->default(0)->after('cart_total');
            }
            if (!Schema::hasColumn('partial_orders', 'last_recovery_email_sent_at')) {
                $table->timestamp('last_recovery_email_sent_at')->nullable()->after('recovery_email_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partial_orders', function (Blueprint $table) {
            if (Schema::hasColumn('partial_orders', 'last_recovery_email_sent_at')) {
                $table->dropColumn('last_recovery_email_sent_at');
            }
            if (Schema::hasColumn('partial_orders', 'recovery_email_count')) {
                $table->dropColumn('recovery_email_count');
            }
        });
    }
};
