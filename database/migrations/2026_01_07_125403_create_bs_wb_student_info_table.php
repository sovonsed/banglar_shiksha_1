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
        Schema::create('bs_wb_student_info', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('student_code_nat',100);
            $table->char('student_state_code',14);
            $table->string('udise_sch_code',100)->nullable();
            $table->string('class_id',100)->nullable();
            $table->string('student_name',100)->nullable();
            $table->string('adhaar_authenticatied',100)->nullable();
            $table->string('gender',100)->nullable();
            $table->string('aadhaar_no',100)->nullable();
            $table->string('student_dob',100)->nullable();
            $table->string('name_as_aadhaar',100)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('student_state_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_wb_student_info');
    }
};
