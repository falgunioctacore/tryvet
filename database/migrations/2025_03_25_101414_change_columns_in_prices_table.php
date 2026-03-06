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
        Schema::table('prices', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropColumn('state_id');
            $table->string('title')->nullable();
            $table->string('heading')->nullable();
            $table->renameColumn('entry_date','date');
            $table->renameColumn('entry_time','time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('heading');
            $table->renameColumn('date','entry_date');
            $table->renameColumn('time','entry_time');
        });
    }
};
