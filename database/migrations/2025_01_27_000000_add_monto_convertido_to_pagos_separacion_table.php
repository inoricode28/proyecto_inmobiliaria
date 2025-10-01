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
        Schema::table('pagos_separacion', function (Blueprint $table) {
            if (!Schema::hasColumn('pagos_separacion', 'monto_convertido')) {
                $table->decimal('monto_convertido', 12, 2)->after('monto_pago')->nullable()->comment('Monto convertido con tipo de cambio aplicado');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pagos_separacion', function (Blueprint $table) {
            if (Schema::hasColumn('pagos_separacion', 'monto_convertido')) {
                $table->dropColumn('monto_convertido');
            }
        });
    }
};