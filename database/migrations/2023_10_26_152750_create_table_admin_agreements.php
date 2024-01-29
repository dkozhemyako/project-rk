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
        Schema::create('admin_agreements', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('date_from_admin', 10);
            $table->enum('equipment_condition', ['YES', 'NO']);
            $table->string('equipment_model', 250);
            $table->integer('equipment_cost');
            $table->integer('equipment_rent_cost');
            $table->string('file_agreement', 100)->nullable();
            $table->string('file_signed_agreement', 100)->nullable();
            $table->string('file_draft_agreement', 100)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_agreements');
    }
};
