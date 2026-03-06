<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('price_items')->update([
            'price' => DB::raw('CAST(price AS UNSIGNED)')
        ]);
        Schema::table('price_items', function (Blueprint $table) {
            $table->string('price')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_items', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->change();
        });
    }
};
