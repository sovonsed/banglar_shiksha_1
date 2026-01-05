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
        Schema::create('bs_location_master', function (Blueprint $table) {
            $table->id();


            $table->string('location_type', 30); // STATE, DISTRICT, SUBDIV, BLOCK, CIRCLE, GS_WARD, SCHOOL, WEBMASTER

            $table->bigInteger('parent_id')->nullable();

            // ================= COMMON FIELDS =================
            $table->string('schcd', 20)->nullable();

            $table->string('state_name')->nullable();
            $table->string('state_code_pk')->nullable();
            $table->string('state_code_fk')->nullable();

            $table->string('district_name')->nullable();
            $table->string('district_code_pk')->nullable();
            $table->string('district_code_fk')->nullable();

            $table->string('subdiv_name')->nullable();
            $table->string('subdiv_code_pk')->nullable();
            $table->string('subdiv_code_fk')->nullable();

            $table->string('block_name')->nullable();
            $table->string('block_code_pk')->nullable();
            $table->string('block_mun_corp_flag')->nullable();

            $table->string('circle_name')->nullable();
            $table->string('circle_code_pk')->nullable();
            $table->string('login_code')->nullable();
            $table->string('district_code')->nullable();

            $table->string('gs_ward_name')->nullable();
            $table->string('gs_ward_code_pk')->nullable();
            $table->string('gs_ward_flag')->nullable();
            $table->string('block_code_fk')->nullable();

            // ================= SCHOOL =================
            $table->string('school_name')->nullable();
            $table->string('school_code_pk')->nullable();
            $table->string('school_code_new')->nullable();
            $table->string('category_code_fk')->nullable();
            $table->string('management_code_fk')->nullable();
            $table->string('circle_code_fk')->nullable();
            $table->string('type_code_fk')->nullable();
            $table->string('area_code_fk')->nullable();
            $table->string('gs_ward_code_fk')->nullable();

            $table->timestamp('entry_time')->nullable();
            $table->string('enter_by')->nullable();
            $table->string('enter_by_stake_cd')->nullable();
            $table->string('entry_ip')->nullable();
            $table->string('contact_status')->nullable();
            $table->string('enroll_status')->nullable();
            $table->string('mapping_gs_status')->nullable();
            $table->string('verify_status')->nullable();
            $table->timestamp('last_forwarded_time')->nullable();
            $table->string('modify_status')->nullable();
            $table->string('mapping_block_status')->nullable();

            // ================= WEBMASTER =================
            $table->string('wm_name')->nullable();
            $table->string('wm_code_pk')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_location_master');
    }
};
