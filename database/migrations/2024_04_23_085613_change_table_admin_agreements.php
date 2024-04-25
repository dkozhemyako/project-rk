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
        Schema::table('admin_agreements', function (Blueprint $table) {
            $table->string('cm_model', 255)->nullable();
            $table->unsignedBigInteger('cm_cost')->nullable();
            $table->string('cm_condition', 255)->nullable();
            $table->string('cg_model', 255)->nullable();
            $table->unsignedBigInteger('cg_cost')->nullable();
            $table->string('cg_condition', 255)->nullable();
            $table->string('equipment_model', 255)->nullable()->change();
            $table->unsignedBigInteger('equipment_cost')->nullable()->change();
            $table->string('equipment_condition', 255)->nullable()->change();
            $table->unsignedBigInteger('equipment_rent_cost')->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
