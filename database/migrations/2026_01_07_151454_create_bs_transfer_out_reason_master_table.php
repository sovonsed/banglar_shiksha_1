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
        Schema::create('bs_student_transfer_out_reason_master', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->smallInteger('status')->default(1)->comment('1 = active');
            // Audit fields
            $table->timestamps();
            
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_student_transfer_out_reason_master');
    }
};
