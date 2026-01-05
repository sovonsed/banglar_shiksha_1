{{-- =========================================================
   TAB 2 : ENROLLMENT DETAILS
========================================================= --}}

@php
    $enrollment = $data['enrollment_info'] ?? [];
    $configDropdowns = config('student');
    
    $previous_schooling_type_master = DB::table('bs_previous_schooling_type_master')->pluck('name', 'id')->toArray();  
    $stu_appeared_master = DB::table('bs_stu_appeared_master')->pluck('name', 'id')->toArray(); 
    $class_master = DB::table('bs_class_master')->pluck('name', 'id')->toArray(); 
    $class_section_master = DB::table('bs_class_section_master')->pluck('name', 'id')->toArray(); 
    $stream_master = DB::table('bs_stream_master')->pluck('name', 'id')->toArray(); 

    $school_medium = DB::table('bs_school_master as sm')
    ->join('bs_medium_master as mm', 'mm.id', '=', 'sm.id')
    ->where('mm.id', 1)
    ->pluck('mm.name', 'mm.id')
    ->toArray();

    $school_classwise_section = DB::table('bs_school_classwise_section')->pluck('class_code_fk', 'id')->toArray();  
    $admission_type_master = DB::table('bs_admission_type_master')->pluck('name', 'id')->toArray(); 
@endphp

<div class="tab-pane fade" id="enrollment_details" role="tabpanel">

<form id="student_enrollment_details_edit"
      method="POST"
      action="{{ route('student.store_enrollment_details_edit') }}"
      novalidate>

@csrf

<h6 class="card-header bg-heading-primary text-white py-2">
    ENROLLMENT DETAILS OF STUDENT IN PRESENT SCHOOL FOR CURRENT YEAR
</h6>

<div class="row mt-3">

    {{-- ================= LEFT COLUMN ================= --}}
    <div class="col-md-6">

        {{-- Admission Number --}}
        <div class="mb-3">
            <label class="form-label small">Admission Number in School</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bx bx-hash"></i></span>
                <input type="text"
                    name="admission_number"
                    class="form-control"
                    maxlength="10"
                    inputmode="numeric"
                    value="{{ old('admission_number', $enrollment['admission_no'] ?? '') }}">
            </div>
        </div>

        {{-- Status in Previous Academic Year --}}
        <div class="mb-3">
                <label class="form-label small">Status of student in Previous Academic Year of Schooling<span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bx bx-history"></i></span>
                <select name="admission_status_prev"
                        id="admission_status_prev"
                        class="form-select">
                    <option value="">-Please Select-</option>
                    @foreach($previous_schooling_type_master as $val => $label)
                        <option value="{{ $val }}"
                            {{ ($enrollment['status_pre_year'] ?? '') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>


        <div class="mb-3"  id ="prev_class_studied_appeared_exam" style="display:none;">
            <label class="form-label small">In the Previous class studied – whether appeared for examinations<span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bx bx-history"></i></span>
                    <select name="prev_class_appeared_exam" id="prev_class_appeared_exam"  class="form-select">
                    <option value="">-Please Select-</option>
                    @foreach($configDropdowns['prev_class_appeared_exam'] as $val => $label)
                    <option value="{{ $val }}"
                        {{ ($enrollment['prev_class_appeared_exam'] ?? '') == $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                    </select>
            </div>
        </div>


        {{-- Appeared for Examination --}}
        <div class="mb-3" id="prev_class_studied_appeared_exam" style="display:none;">
            <label class="form-label small">Whether appeared for examination</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bx bx-history"></i></span>
                <select name="prev_class_appeared_exam"
                        id="prev_class_appeared_exam"
                        class="form-select">
                    <option value="">-Please Select-</option>
                    @foreach($configDropdowns['yes_no'] ?? [] as $val => $label)
                        <option value="{{ $val }}"
                            {{ ($enrollment['prev_class_appeared_exam'] ?? '') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Result of Examination --}}
        <div class="mb-3" id="previous_class_studied_result_examination" style="display:none;">
            <label class="form-label small">In the previous class studied – Result of the examination<span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bx bx-history"></i></span>
                <select name="previous_class_result_examination"
                        class="form-select">
                    <option value="">-Please Select-</option>
                    @foreach($stu_appeared_master as $val => $label)
                        <option value="{{ $val }}"
                            {{ ($enrollment['prev_class_exam_result'] ?? '') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>


        <div class="mb-3" id="percentage_of_overall_marks_section" style="display:none;">
            <label class="form-label small">In the previous class studied - % of overall marks obtained in the examination<span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bx bx-history"></i></span>
                <input name="percentage_of_overall_marks" id="percentage_of_overall_marks"
                type="text" 
                class="form-control" 
                placeholder="% of overall marks obtained"
                maxlength="3"
                pattern="\d*"
                inputmode="numeric"
                    value="{{ old('prev_class_marks_percent', $enrollment['prev_class_marks_percent'] ?? '') }}"
                >
            </div>
        </div>


        {{-- Percentage --}}
        <div class="mb-3" id="percentage_of_overall_marks_section" style="display:none;">
            <label class="form-label small">% of marks obtained</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bx bx-history"></i></span>
                <input type="text"
                    name="percentage_of_overall_marks"
                    class="form-control"
                    maxlength="3"
                    inputmode="numeric"
                    value="{{ old('percentage_of_overall_marks', $enrollment['prev_class_marks_percent'] ?? '') }}">
            </div>
        </div>

        {{-- No of Days Attended --}}
        <div class="mb-3" id="no_of_days_attended_section" style="display:none;">
        <label class="form-label small">No. of days child attended school (in the previous academic year)<span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bx bx-history"></i></span>
                <input type="text"
                    name="no_of_days_attended"
                    class="form-control"
                    maxlength="3"
                    inputmode="numeric"
                    value="{{ old('no_of_days_attended', $enrollment['attendention_pre_year'] ?? '') }}">
            </div>
        </div>

            {{-- Previous Class --}}
        <div class="mb-3" id="previous_class_studied" style="display:none;">
            <label class="form-label small">Grade/Class Studied in the Previous/Last Academic Year (Previous Class)<span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bx bx-history"></i></span>
                <select name="previous_class" class="form-select">
                    <option value="">-Please Select-</option>
                    @foreach($class_master as $val => $label)
                        <option value="{{ $val }}"
                            {{ ($enrollment['pre_class_code_fk'] ?? '') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>


        <div class="mb-3" id="previous_section_section" style="display:none;">
            <label class="form-label small">Previous Section<span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bx bx-history"></i></span>
                <select name="class_section" id="class_section" class="form-select">
                    <option value="">-Please Select-</option>
                    @foreach($class_section_master ?? [] as $val => $label)
                    <option value="{{ $val }}"
                        {{ ($enrollment['pre_section_code_fk'] ?? '') == $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>


        <div class="mb-3" id="previous_stream_section" style="display:none;">
            <label class="form-label small">Previous Stream<span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bx bx-history"></i></span>
                <select name="student_stream" id="student_stream" class="form-select">
                    <option value="">-Please Select-</option>
                    @foreach($stream_master ?? [] as $val => $label)
                    <option value="{{ $val }}"
                        {{ ($enrollment['pre_stream_code_fk'] ?? '') == $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>


        <div class="mb-3" id="previous_roll_no_section" style="display:none;">
            <label class="form-label small">Previous Roll No.<span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bx bx-history"></i></span>
                <input name="previous_student_roll_no" id="previous_student_roll_no"
                type="text" 
                class="form-control" 
                placeholder="Enter Previous Roll Number"
                maxlength="10"
                pattern="\d*"
                inputmode="numeric"
                value="{{ old('pre_roll_number', $enrollment['pre_roll_number'] ?? '') }}"
                >
            </div>
        </div>

        {{-- Present Class --}}
        <div class="mb-3">
            <label class="form-label small">Present Class</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bx bx-book"></i></span>
                <select name="present_class" class="form-select">
                    <option value="">-Please Select-</option>
                    @foreach($class_master as $val => $label)
                        <option value="{{ $val }}"
                            {{ ($enrollment['cur_class_code_fk'] ?? '') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Academic Year --}}
        <div class="mb-3">
            <label class="form-label small">Academic Year</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                <select name="accademic_year" class="form-select">
                    <option value="">-Please Select-</option>
                    @foreach($configDropdowns['accademic_year'] ?? [] as $val => $label)
                        <option value="{{ $val }}"
                            {{ ($enrollment['academic_year'] ?? '') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Present Section -->
        <div class="mb-3">
            <label class="form-label small">Present Section</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bx bx-layout"></i></span>
                <select name="present_section" id="present_section" class="form-select">
                    <option value="">-Please Select-</option>
                        @foreach($class_section_master ?? [] as $val => $label)
                    <option value="{{ $val }}"
                        {{ ($enrollment['cur_section_code_fk'] ?? '') == $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>



        <!-- Present Medium -->
        <div class="mb-3">
            <label class="form-label small">Medium</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bx bx-chat"></i></span>
                    <select name="school_medium" id="school_medium" class="form-select">
                    <option value="">-Please Select-</option>
                    @foreach($school_medium ?? [] as $val => $label)
                    <option value="{{ $val }}"
                        {{ ($enrollment['medium_code_fk'] ?? '') == $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ================= RIGHT COLUMN ================= --}}
    <div class="col-md-6">

        {{-- Admission Date --}}
        <div class="mb-3">
            <label class="form-label small">Admission Date</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                <input type="date"
                    name="admission_date_present"
                    class="form-control"
                    value="{{ old('admission_date_present', $enrollment['admission_date'] ?? '') }}">
            </div>
        </div>

        {{-- Present Roll No --}}
        <div class="mb-3">
            <label class="form-label small">Present Roll No</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bx bx-list-ol"></i></span>
                <input type="number"
                    name="present_roll_no"
                    class="form-control"
                    value="{{ old('present_roll_no', $enrollment['cur_roll_number'] ?? '') }}">
            </div>
        </div>

        {{-- Admission Type --}}
        <div class="mb-3">
            <label class="form-label small">Admission Type</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bx bx-transfer-alt"></i></span>
                <select name="admission_type" class="form-select">
                    <option value="">-Please Select-</option>
                    @foreach($admission_type_master as $val => $label)
                        <option value="{{ $val }}"
                            {{ ($enrollment['admission_type_code_fk'] ?? '') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

    </div>
</div>

<div class="text-end mt-3">
<button type="button"
        class="btn btn-success"
        data-bs-toggle="modal"
        data-bs-target="#confirmUpdateModalEnrollmentInfo">
    Update
</button>

</div>

</form>
</div>



<!-- ================= CONFIRM ENROLLMENT UPDATE MODAL ================= -->
<div class="modal fade" id="confirmUpdateModalEnrollmentInfo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">

      
            <button type="button"
                    class="btn-close position-absolute end-0 m-2"
                    data-bs-dismiss="modal"
                    aria-label="Close"></button>


            <div class="text-center p-3">
                              <img src="{{ asset('images/logo/update_details_logo.png') }}"
                     width="80"
                     height="80"
                     alt="Update Confirmation">

                <h5 class="fw-bold mt-2">Confirm Update</h5>

                <p class="text-muted small mb-0">
                    Are you sure you want to
                    <strong>update the enrollment details</strong>
                    of this student?
                </p>
            </div>

            <div class="modal-footer justify-content-center pt-0">
                <button type="button"
                        class="btn btn-outline-secondary px-4"
                        data-bs-dismiss="modal">
                    <i class="bx bx-x-circle me-1"></i> No
                </button>

                <button type="button"
                        class="btn btn-success px-4"
                        id="confirmUpdateEnrollmentBtn">
                    <i class="bx bx-check-circle me-1"></i> Yes, Update
                </button>
            </div>

        </div>
    </div>
</div>


<!-- ================= SUCCESS MODAL ================= -->
<div class="modal fade" id="enrollmentSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">

            <div class="text-center p-4">
                <img src="{{ asset('images/logo/update_details_logo.png') }}"
                     width="70"
                     height="70"
                     alt="Success">

                <h5 class="fw-bold mt-3 text-success">
                    Update Successful
                </h5>

                <p class="text-muted small mb-3">
                    Enrollment details updated successfully.
                </p>

                <button type="button"
                        class="btn btn-success px-4"
                        data-bs-dismiss="modal">
                    OK
                </button>
            </div>

        </div>
    </div>
</div>


<!-- ================= Failed MODAL ================= -->
<div class="modal fade" id="enrollmentFailedModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">

            <div class="text-center p-4">
                <img src="{{ asset('images/logo/update_details_logo.png') }}"
                     width="70"
                     height="70"
                     alt="Failed">

                <h5 class="fw-bold mt-3 text-danger">
                    Update Failed
                </h5>

                <p class="text-muted small mb-3">
                    Enrollment details Update Failed.
                </p>

                <button type="button"
                        class="btn btn-danger px-4"
                        data-bs-dismiss="modal">
                    OK
                </button>
            </div>

        </div>
    </div>
</div>



{{-- ================= JS : AUTO SHOW / HIDE ================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    let statusPrev = "{{ $enrollment['status_pre_year'] ?? '' }}";
    let appeared   = "{{ $enrollment['prev_class_appeared_exam'] ?? '' }}";

    if (statusPrev !== '') {
        document.getElementById('prev_class_studied_appeared_exam').style.display = 'block';
        document.getElementById('previous_class_studied').style.display = 'block';
        document.getElementById('no_of_days_attended_section').style.display = 'block';
    }

    if (appeared !== '') {
        document.getElementById('previous_class_studied_result_examination').style.display = 'block';
        document.getElementById('percentage_of_overall_marks_section').style.display = 'block';
    }
    
});



$(document).ready(function () {

    function togglePrevFields(value) {
        value = (value || '').toString();

        if (value === '1') { // YES / STUDIED
            $('#prev_class_studied_appeared_exam').show();
            $('#no_of_days_attended_section').show();
            $('#previous_class_studied').show();
            $('#previous_section_section').show();
            $('#previous_stream_section').show();
              $('#previous_roll_no_section').show();
        } else {
            $('#prev_class_studied_appeared_exam').hide();
            $('#no_of_days_attended_section').hide();
            $('#previous_class_studied').hide();
            $('#previous_section_section').hide();
            $('#previous_stream_section').hide();
              $('#previous_roll_no_section').hide();

            // clear values
            $('#prev_class_appeared_exam').val('');
            $('input[name="no_of_days_attended"]').val('');
            $('select[name="previous_class"]').val('');
            $('#class_section').val('');
        }
    }

    function toggleExamFields(value) {
        value = (value || '').toString();

        if (value === '1') { // Appeared = YES
            $('#previous_class_studied_result_examination').show();
            $('#percentage_of_overall_marks_section').show();
        } else {
            $('#previous_class_studied_result_examination').hide();
            $('#percentage_of_overall_marks_section').hide();
            $('input[name="percentage_of_overall_marks"]').val('');
        }
    }

    // 🔹 EVENT BINDINGS
    $('#admission_status_prev').on('change', function () {
        togglePrevFields($(this).val());
    });

    $('#prev_class_appeared_exam').on('change', function () {
        toggleExamFields($(this).val());
    });

    
    togglePrevFields("{{ $enrollment['status_pre_year'] ?? '' }}");
    toggleExamFields("{{ $enrollment['prev_class_appeared_exam'] ?? '' }}");

});


$(document).ready(function () {
    // alert("df");

    $('#confirmUpdateEnrollmentBtn').on('click', function () {

        var form = $('#student_enrollment_details_edit');
        var url  = form.attr('action');
        var data = form.serialize();

        // disable button to avoid double submit
        $('#confirmUpdateEnrollmentBtn').prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function (response) {

                // close modal
                $('#confirmUpdateModalEnrollmentInfo').modal('hide');

                $('#enrollmentSuccessModal').modal('show');

            },
            error: function () {
         $('#enrollmentFailedModal').modal('show');

            },
            complete: function () {
                $('#confirmUpdateEnrollmentBtn').prop('disabled', false);
            }
        });

    });

});
</script>
