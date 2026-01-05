<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Step 1: Create parent partitioned table
         */
        DB::statement("
            CREATE TABLE bs_student_dropbox_archive (
                id BIGINT NOT NULL,
                dropbox_id BIGINT NOT NULL,
                student_code CHAR(14) NOT NULL,
                academic_year INT NOT NULL,
                school_code_fk BIGINT,
                circle_code_fk BIGINT,
                block_munc_code_fk BIGINT NULL,
                district_code_fk BIGINT,
                gs_ward_code_fk BIGINT NULL,
                reason_code_fk BIGINT,
                not_transfer_reason_code_fk SMALLINT,
                uniform_status VARCHAR(10) NULL,
                status SMALLINT DEFAULT 1,
                entry_ip VARCHAR(15) NULL,
                update_ip VARCHAR(15),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                created_by BIGINT NULL,
                updated_by BIGINT NULL,
                update_by_stake_cd BIGINT NULL,
                deleted_at TIMESTAMP NULL,
                PRIMARY KEY (student_code, academic_year)
            ) PARTITION BY RANGE (academic_year);
        ");

        for ($year = 2019; $year <= date('Y') + 1; $year++) {
            $nextYear = $year + 1;

            DB::statement("
                CREATE TABLE bs_student_dropbox_archive_{$year}
                PARTITION OF bs_student_dropbox_archive
                FOR VALUES FROM ({$year}) TO ({$nextYear});
            ");
        }

        /**
         * Step 4: Indexes (important for performance)
         */
        DB::statement("
            CREATE INDEX idx_bs_student_dropbox_archive_student_code
            ON bs_student_dropbox_archive (student_code);
        ");
        Schema::table('bs_student_dropbox_archive', function (Blueprint $table) {
            $table->foreign('district_code_fk')->references('id')->on('bs_district_master');
            $table->foreign('circle_code_fk')->references('id')->on('bs_circle_master');
            $table->foreign('school_code_fk')->references('id')->on('bs_school_master');
         });
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS bs_student_dropbox_archive CASCADE;");
    }
};
