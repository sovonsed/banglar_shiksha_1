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
         * 1️⃣ Parent partitioned table
         */
        DB::statement("
            CREATE TABLE bs_student_sentup_box_archive (
                id BIGINT NOT NULL,
                student_code CHAR(14) NOT NULL,
                academic_year CHAR(4) NOT NULL,
                cur_class_code_fk SMALLINT NOT NULL,
                school_code_fk BIGINT NOT NULL,
                circle_code_fk BIGINT NOT NULL,
                block_munc_code_fk BIGINT,
                district_code_fk BIGINT NOT NULL,
                gs_ward_code_fk BIGINT,
                cc_status SMALLINT DEFAULT 0,
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
            ) PARTITION BY LIST (academic_year);
        ");

        /**
         * 3️⃣ Initial partitions (2019–next year)
         */
        for ($year = 2019; $year <= date('Y') + 1; $year++) {
            DB::statement("
                CREATE TABLE bs_student_sentup_box_archive_{$year}
                PARTITION OF bs_student_sentup_box_archive
                FOR VALUES IN ('{$year}');
            ");
        }

        /**
         * 4️⃣ Foreign Keys (must be on parent)
         */
        DB::statement("
            ALTER TABLE bs_student_sentup_box_archive
            ADD CONSTRAINT fk_sentup_class
            FOREIGN KEY (cur_class_code_fk)
            REFERENCES bs_class_master(id)
            ON DELETE RESTRICT ON UPDATE CASCADE;
        ");

        DB::statement("
            ALTER TABLE bs_student_sentup_box_archive
            ADD CONSTRAINT fk_sentup_school
            FOREIGN KEY (school_code_fk)
            REFERENCES bs_school_master(id)
            ON DELETE RESTRICT ON UPDATE CASCADE;
        ");

        DB::statement("
            ALTER TABLE bs_student_sentup_box_archive
            ADD CONSTRAINT fk_sentup_circle
            FOREIGN KEY (circle_code_fk)
            REFERENCES bs_circle_master(id)
            ON DELETE RESTRICT ON UPDATE CASCADE;
        ");

       

        DB::statement("
            ALTER TABLE bs_student_sentup_box_archive
            ADD CONSTRAINT fk_sentup_district
            FOREIGN KEY (district_code_fk)
            REFERENCES bs_district_master(id)
            ON DELETE RESTRICT ON UPDATE CASCADE;
        ");
        /**
         * 5️⃣ Indexes
         */
        DB::statement("
            CREATE INDEX idx_sentup_archive_student_code
            ON bs_student_sentup_box_archive (student_code);
        ");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS bs_student_sentup_box_archive CASCADE;");
    }
};

