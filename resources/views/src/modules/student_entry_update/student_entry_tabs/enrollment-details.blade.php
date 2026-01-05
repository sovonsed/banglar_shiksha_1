 <!-- ======== TAB 2: ENROLMENT DETAILS  -- SUBHAJIT DAS========-- -->
          <div class="tab-pane fade" id="enrollment_details" role="tabpanel" aria-labelledby="enrollment_details-tab">
            <form id="student_enrollment_details" method="POST" action="{{ route('student.store_enrollment_details') }}" novalidate>

              @csrf

              <h6 class=" card-header bg-heading-primary text-white py-2">
              ENROLLMENT DETAILS OF STUDENT IN PRESENT SCHOOL FOR CURRENT YEAR
              </h6> 
              <div class="row">
                <div class="col-md-6">
                  <!-- Admission Number in School -->
                  <div class="mb-3">
                    <label class="form-label small">Admission Number in School<span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-hash"></i></span>
                      <input name="admission_number" 
                        type="text" 
                        class="form-control" 
                        placeholder="Admission number in school"
                        maxlength="10"
                        pattern="\d*"
                        inputmode="numeric"
                        value="{{ old('admission_no', $enrollment_info['admission_no'] ?? '') }}">  
                    </div>
                  </div>

                  <!-- Status of Admission in Previous Academic Year -->
                  <div class="mb-3" id ="previous_school_status">
                    <label class="form-label small">Status of student in Previous Academic Year of Schooling<span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-history"></i></span>
                      <select name="admission_status_prev"  id="admission_status_prev" class="form-select">
                        <option value="">-Please Select-</option> 
                          @foreach($previous_schooling_type_master ?? [] as $val => $label)
                          <option value="{{ $val }}"
                              {{ ($enrollment_info['status_pre_year'] ?? '') == $val ? 'selected' : '' }}>
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
                          @foreach($dropdowns['prev_class_appeared_exam'] as $val => $label)
                          <option value="{{ $val }}"
                              {{ ($enrollment_info['prev_class_appeared_exam'] ?? '') == $val ? 'selected' : '' }}>
                              {{ $label }}
                          </option>
                          @endforeach
                        </select>
                    </div>
                  </div>


                  <div class="mb-3" id="previous_class_studied_result_examination" style="display:none;">
                    <label class="form-label small">In the previous class studied – Result of the examination<span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-history"></i></span>
                        <select name="previous_class_result_examination" id="previous_class_result_examination" class="form-select">
                            <option value="">-Please Select-</option>
                        @foreach($stu_appeared_master ?? [] as $val => $label)
                          <option value="{{ $val }}"
                              {{ ($enrollment_info['prev_class_exam_result'] ?? '') == $val ? 'selected' : '' }}>
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
                         value="{{ old('prev_class_marks_percent', $enrollment_info['prev_class_marks_percent'] ?? '') }}"
                      >
                    </div>
                  </div>

                    <div class="mb-3" id="no_of_days_attended_section" style="display:none;">
                    <label class="form-label small">No. of days child attended school (in the previous academic year)<span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-history"></i></span>
                    <input name="no_of_days_attended" id="no_of_days_attended" 
                        type="text" 
                        class="form-control" 
                        placeholder="No of days child attended school"
                        maxlength="3"
                        pattern="\d*"
                        inputmode="numeric"
                        value="{{ old('attendention_pre_year', $enrollment_info['attendention_pre_year'] ?? '') }}"
                      >
                    </div>
                  </div>
                  <div class="mb-3" id="previous_class_studied" style="display:none;">
                    <label class="form-label small">Grade/Class Studied in the Previous/Last Academic Year (Previous Class)<span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-history"></i></span>
                      <select name="previous_class" id="previous_class" class="form-select">
                            <option value="">-Please Select-</option>
                          @foreach($class_master ?? [] as $val => $label)
                            <option value="{{ $val }}"
                                {{ ($enrollment_info['pre_class_code_fk'] ?? '') == $val ? 'selected' : '' }}>
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
                                {{ ($enrollment_info['pre_section_code_fk'] ?? '') == $val ? 'selected' : '' }}>
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
                                {{ ($enrollment_info['pre_stream_code_fk'] ?? '') == $val ? 'selected' : '' }}>
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
                        value="{{ old('pre_roll_number', $enrollment_info['pre_roll_number'] ?? '') }}"
                      >
                    </div>
                  </div>

                  <!-- ================================================== -->

                  <!-- Present Class -->
                  <div class="mb-3">
                    <label class="form-label small">Present Class</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-book-open"></i></span>
                      <select name="present_class" id="present_class" class="form-select">
                          <option value="">-Please Select-</option>
                          @foreach($class_master ?? [] as $val => $label)
                            <option value="{{ $val }}"
                                {{ ($enrollment_info['cur_class_code_fk'] ?? '') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                          @endforeach
                      </select>
                    </div>
                  </div>

                  <!-- Academic Year -->
                  <div class="mb-3">
                    <label class="form-label small">Academic Year</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-calendar-alt"></i></span>
                    <select name="accademic_year" id="accademic_year"  class="form-select">
                          <option value="">-Please Select-</option>
                          @foreach($dropdowns['accademic_year'] as $val => $label)
                         <option value="{{ $val }}"
                                {{ ($enrollment_info['academic_year'] ?? '') == $val ? 'selected' : '' }}>
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
                                {{ ($enrollment_info['cur_section_code_fk'] ?? '') == $val ? 'selected' : '' }}>
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
                                {{ ($enrollment_info['medium_code_fk'] ?? '') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                          @endforeach
                      </select>
                    </div>
                  </div>

                </div>

                <div class="col-md-6">
                  <!-- Admission Date in Present Class -->
                  <div class="mb-3">
                    <label class="form-label small">Admission Date in Present Class</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                      <input name="admission_date_present" type="date" class="form-control" value="{{ old('admission_date', $enrollment_info['admission_date'] ?? '') }}">
                    </div>
                  </div>

                  <div class="mb-3" id="cur_stream_wrapper" style="display:none;">
                    <label class="form-label small">
                        Academic Stream opted by student (For Higher Secondary Classes only)
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                        <select name="cur_stream_code" id="cur_stream_code" class="form-select">
                            <option value="">-Please Select-</option>
                            @foreach($stream_master ?? [] as $val => $label)
                                <option value="{{ $val }}"
                                    {{ ($enrollment_info['cur_stream_code'] ?? '') == $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                  </div>


                  <!-- Present Roll No -->
                  <div class="mb-3">
                    <label class="form-label small">Present Roll No</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-list-ol"></i></span>
                      <input name="present_roll_no" type="number" class="form-control" placeholder="Roll number" value="{{ old('cur_roll_number', $enrollment_info['cur_roll_number'] ?? '') }}">
                    </div>
                  </div>

                  <!-- Admission Type -->
                  <div class="mb-3">
                    <label class="form-label small">Admission Type</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-transfer-alt"></i></span> 
                          <select name="admission_type" id="admission_type" class="form-select" >
                          <option value="">-Please Select-</option>
                              @foreach($admission_type_master ?? [] as $val => $label)
                            <option value="{{ $val }}"
                                {{ ($enrollment_info['admission_type_code_fk'] ?? '') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                          @endforeach
                      </select>
                    </div>
                  </div>

                  <!-- Admission Category -->
                </div>
              </div>

              <div class="form-actions text-end mt-3">
                <button class="btn btn-secondary me-2" data-bs-toggle="tab" type="button">Previous</button>
                <button id="enrollment_details_save_btn" class="btn btn-success" data-bs-toggle="tab" type="button">Next</button>
              </div>

            </form>
          </div>


<script>
$(document).ready(function() {
function toggleStreamField(value) {
    value = (value || '').toString().toLowerCase();
    // alert(value);

    // Treat both numeric and roman codes as XI / XII
    const isHigherSecondary =
        value === '11' || value === '12' || value === 'xi' || value === 'xii';

    if (isHigherSecondary) {
        $('#cur_stream_wrapper').show();
    } else {
        $('#cur_stream_wrapper').hide();
        $('#cur_stream_code').val('');  // clear if not XI/XII
    }
}

// Run when user changes Present Class
$('#present_class').on('change', function () {
    toggleStreamField($(this).val());
});

// IMPORTANT: also run once for the value loaded from DB
toggleStreamField($('#present_class').val());
});



 function togglePrevFields(value) {
        value = (value || '').toString().toLowerCase();

        if (value === '1' || value === 'yes') {
            $('#prev_class_studied_appeared_exam').show();
            $('#no_of_days_attended_section').show();
            $('#previous_class_studied').show();
            $('#previous_section_section').show();
            $('#previous_roll_no_section').show();
            $('#previous_stream_section').show();
        } else {
            $('#prev_class_studied_appeared_exam').hide();
            $('#no_of_days_attended_section').hide();
            $('#previous_class_studied').hide();
            $('#previous_section_section').hide();
            $('#previous_roll_no_section').hide();
            $('#previous_stream_section').hide();

            // clear selection
            $('#prev_class_appeared_exam').val('');
            $('#no_of_days_attended').val('');  
            $('#previous_class').val('');  
            $('#class_section').val('');  
            $('#previous_student_roll_no').val('');  
            $('#student_stream').val(''); 
        }
    }
</script>