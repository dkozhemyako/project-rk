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
        Schema::create('client_agreements', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('date_from_client', 10);
            $table->enum('type', ['FO', 'FOP']);
            $table->string('name', 100);
            $table->string('phone', 10);
            $table->string('client_inn', 10);
            $table->string('passport_series_number', 50)->nullable();
            $table->string('passport_issue', 250)->nullable();
            $table->string('passport_date', 10)->nullable();
            $table->string('fop_inn', 19)->nullable();
            $table->string('fop_register_date', 10)->nullable();
            $table->string('client_region', 50);
            $table->string('client_town', 50);
            $table->string('client_street', 50);
            $table->string('client_house');
            $table->string('client_flat', 50)->nullable();
            $table->string('equip_region', 50);
            $table->string('equip_town', 50);
            $table->string('equip_street', 50);
            $table->string('equip_house');
            $table->text('equip_address_add');
            $table->string('file_fop_edr', 100)->nullable();
            $table->string('file_fop_agr_rent', 100)->nullable();
            $table->string('file_fo_pas1st', 100)->nullable();
            $table->string('file_fo_pas2nd', 100)->nullable();
            $table->string('file_fo_pasReg', 100)->nullable();
            $table->string('file_fo_agr_rent', 100)->nullable();
            $table->string('file_draft_agreement', 100)->nullable();
            $table->string('file_signed_agreement', 100)->nullable();
            $table->bigInteger('telegram_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_agreements');
    }
};
