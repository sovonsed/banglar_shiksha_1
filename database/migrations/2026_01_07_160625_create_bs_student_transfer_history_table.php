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
        Schema::create('bs_student_transfer_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('student_code', 14)->primary();
            $table->string('studentname', 500)->nullable();
            $table->date('dob')->nullable();

            $table->integer('academic_year');
            $table->smallInteger('cur_class_code_fk')->nullable();
            $table->smallInteger('cur_section_code_fk')->nullable();
            $table->smallInteger('cur_roll_number')->nullable();

            $table->bigInteger('old_school_code_fk')->nullable();
            $table->foreign('old_school_code_fk')->references('id')->on('bs_school_master');
            $table->bigInteger('new_school_code_fk')->nullable();
            $table->foreign('new_school_code_fk')->references('id')->on('bs_school_master');
            $table->integer('reason_code_fk')->nullable();
            $table->foreign('reason_code_fk')->references('id')->on('bs_student_transfer_out_reason_master');
            $table->char('entry_ip', 15)->nullable();

            $table->bigInteger('enter_by')->nullable();
            $table->smallInteger('enter_by_stake_cd')->nullable();

            $table->timestamp('transfer_out_date')->nullable();
            $table->smallInteger('status')->default(1)->comment('1 = active');

            $table->timestamps();
            $table->softDeletes();
            $table->index('student_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_student_transfer_history');
    }
};
