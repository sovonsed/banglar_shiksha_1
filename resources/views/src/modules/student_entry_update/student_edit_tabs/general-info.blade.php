@php
    // ================= SAFE DATA MAPPING =================
    $basic = $data['basic_info'] ?? [];
    $configDropdowns = config('student');

    $gender_master = DB::table('bs_gender_master')->pluck('name', 'id')->toArray();
    $mother_tongue_master = DB::table('bs_mother_tongue_master')->pluck('name', 'id')->toArray();
    $social_category_master = DB::table('bs_social_category_master')->pluck('name', 'id')->toArray();
            
    $religion_master = DB::table('bs_religion_master')->pluck('name', 'id')->toArray();
    $nationality_master = DB::table('bs_nationality_master')->pluck('name', 'id')->toArray();
    $blood_group_master = DB::table('bs_blood_group_master')->pluck('name', 'id')->toArray();  

    $guardian_relationship_master = DB::table('bs_guardian_relationship_master')->pluck('name', 'id')->toArray();
    $income_master = DB::table('bs_income_master')->pluck('name', 'id')->toArray();
    $guardian_qualification_master = DB::table('bs_guardian_qualification_master')->pluck('name', 'id')->toArray();  
    $bs_stu_disability_type_master = DB::table('bs_stu_disability_type_master')->pluck('name', 'id')->toArray();  
    $bs_child_mainstreamed_master = DB::table('bs_child_mainstreamed_master')->pluck('name', 'id')->toArray();  



@endphp

<!-- =========TAB 1: Contact Info -- SUBHAJIT DAS--================================ -->
          <div class="tab-pane fade show active" id="general_info" role="tabpanel" aria-labelledby="general_info-tab">

            <form id="Edit_of_basic_info_of_student" method="POST" action="{{ route('student.edit_student_basic_details') }}" novalidate>

              @csrf
              <h6 class=" card-header bg-heading-primary text-white py-2">
              GENERAL INFORMATION OF THE STUDENT
              </h6>
              <div class="row form-row-gap">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label small">Name of the Student <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-user"></i></span>
                      <input name="student_name" 
                      type="text" 
                      class="form-control" 
                      placeholder="Name of the student" 
                      value="{{ old('student_name', $basic['student_name'] ?? '') }}"
                      required>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small">Gender <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-book"></i></span>
                       <select name="gender" class="form-select" required>
                            <option value="">-Please Select-</option>
                              @foreach($gender_master as $val => $label)
                                <option value="{{ $val }}" 
                                    {{ ($basic['gender'] ?? '') == $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                              @endforeach
                        </select>
                    </div>
                  </div>

                  <div class="mb-3">
                      <label class="form-label small" for="dobField">
                          Date of Birth <span class="text-danger">*</span>
                      </label>
                      <div class="input-group" id="dobGroup" style="cursor:pointer;">
                          <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                         <input id="dobField" 
                          name="dob" 
                          type="date" 
                          class="form-control" 
                          value="{{ old('dob', $basic['dob'] ?? '') }}"
                          required>
                      </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small">Father's Name <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-user"></i></span>
                     <input name="father_name" 
                      type="text" 
                      class="form-control" 
                      placeholder="Father's name" 
                      value="{{ old('father_name', $basic['father_name'] ?? '') }}"
                      required>

                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small">Mother's Name <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-user"></i></span>
                 <input name="mother_name" 
                  type="text" 
                  class="form-control" 
                  placeholder="Mother's name" 
                  value="{{ old('mother_name', $basic['mother_name'] ?? '') }}"
                  required>

                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small">Guardian's  Name <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-user"></i></span>
                      <input name="guardian_name" type="text" class="form-control" placeholder="Guardian's name" value="{{ old('guardian_name', $basic['guardian_name'] ?? '') }}" required>
                    </div>
                  </div>

                  <div class="mb-3">
                      <label class="form-label small">Aadhaar No of Child</label>
                      <div class="input-group">
                          <span class="input-group-text"><i class="bx bx-id-card"></i></span>
                          <input 
                              id="aadhaar_child"
                              name="aadhaar_child"
                              type="text"
                              class="form-control"
                              placeholder="Aadhaar no of child"
                              value="{{ old('aadhaar_child', $basic['aadhaar_child'] ?? '') }}"
                              maxlength="12"
                          >
                      </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small">Name of Student as Per Aadhaar</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-id-card"></i></span>
                    <input name="student_name_as_per_aadhaar" type="text" class="form-control" placeholder="Name of student as per Aadhaar"   value="{{ old('student_name_as_per_aadhaar', $basic['student_name_as_per_aadhaar'] ?? '') }}">
                    </div>
                  </div>

                  <div class="mb-3">
                      <label class="form-label small">Mother Tongue of the Child</label>
                      <div class="input-group">
                          <span class="input-group-text"><i class="bx bx-message-alt-detail"></i></span>
                          <select name="mother_tongue" class="form-select">
                           <option value="">-Select-</option>
                            @foreach($mother_tongue_master as $val => $label)
                                <option value="{{ $val }}" 
                                    {{ ($basic['mother_tongue'] ?? '') == $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                          </select>
                      </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small">Social Category<span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-book"></i></span>
                    <select name="social_category" class="form-select" required>
                     <option value="">-Please Select-</option>

                      @foreach($social_category_master ?? [] as $val => $label)
                          <option value="{{ $val }}" 
                              {{ ($basic['social_category'] ?? '') == $val ? 'selected' : '' }}>
                              {{ $label }}
                          </option>
                      @endforeach

                    </select>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small">Religion <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-book"></i></span>
                      <select name="religion" class="form-select" required>
                          <option value="">-Please Select-</option>

                          @foreach($religion_master ?? [] as $val => $label)
                              <option value="{{ $val }}"
                                  {{ ($basic['religion'] ?? '') == $val ? 'selected' : '' }}>
                                  {{ $label }}
                              </option>
                          @endforeach
                      </select>

                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small">Whether BPL Beneficiary?<span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-check"></i></span>
                    <select name="bpl_beneficiary" id="bpl_beneficiary" class="form-select">
                      <option value="">-Please Select-</option>
                    @foreach($configDropdowns['yes_no'] as $val => $label)
                          <option value="{{ $val }}"
                              {{ ($basic['bpl_beneficiary'] ?? '') == $val ? 'selected' : '' }}>
                              {{ $label }}
                          </option>
                      @endforeach
                    </select>
                    </div>
                  </div>

                  
                  <div class="mb-3" id="aay_section" style="display:none;">
                      <label class="form-label small">Whether Antyodaya Anna Yojana (AAY) beneficiary?<span class="text-danger">*</span></label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-check"></i></span>
                        <select name="antyodaya_anna_yojana" id="antyodaya_anna_yojana" class="form-select">
                            <option value="">-Please Select-</option>
                            @foreach($configDropdowns['yes_no'] ?? [] as $val => $label)
                            <option value="{{ $val }}" {{ ($basic['antyodaya_anna_yojana'] ?? '') == $val ? 'selected' : '' }}>
                            {{ $label }}
                            </option>
                            @endforeach
                        </select>
                      </div>
                  </div>

                  <div class="mb-3" id="bpl_numberID" style="display:none;">
                      <label class="form-label small">BPL Number<span class="text-danger">*</span></label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-check"></i></span>
                        <input name="bpl_number" id="bpl_number" type="text" class="form-control" placeholder="Enter Your BPL Number"  value="{{ old('bpl_number', $basic['bpl_number'] ?? '') }}">
                      </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small">Whether belongs to EWS / Disadvantaged Group</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-wallet"></i></span>
                      <select name="disadvantaged_group" class="form-select">
                        <option value="">-Please Select-</option>
                          @foreach($configDropdowns['yes_no'] ?? [] as $val => $label)
                          <option value="{{ $val }}" {{ ($basic['disadvantaged_group'] ?? '') == $val ? 'selected' : '' }}>
                          {{ $label }}
                          </option>
                          @endforeach                            
                      </select>
                    </div>
                  </div>


                  <div class="mb-3">
                  <label class="form-label small">Upload Student Photo</label>
                  <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-image"></i></span>
                      <input 
                          type="file" 
                          name="student_photo" 
                          id="student_photo" 
                          class="form-control"
                          accept="image/*">
                  </div>

          <!-- Preview section -->
          <div class="mt-2">
              <img id="photo_preview" 
                  src="{{ isset($basic['student_photo']) ? asset('uploads/students/'.$basic['student_photo']) : '' }}" 
                  style="max-width:150px; display: {{ isset($basic['student_photo']) ? 'block' : 'none' }}; border:1px solid #ccc; padding: 4px;">
           </div>
        </div>

                </div>

                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label small">Whether CWSN (Child with Special Needs)?</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-heart"></i></span>
                      <select name="cwsn" id ="cwsn"class="form-select">
                        <option value="">-Please Select-</option>
                         @foreach($configDropdowns['yes_no'] ?? [] as $val => $label)
                          <option value="{{ $val }}" {{ ($basic['cwsn'] ?? '') == $val ? 'selected' : '' }}>
                          {{ $label }}
                          </option>
                          @endforeach
                      </select>
                    </div>
                  </div>


                  <div class="mb-3" id="impairment"  style="display:none;">
                    <label class="form-label small">(a) Type of Impairment</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-info-circle"></i></span>
                  <select name="type_of_impairment" id="type_of_impairment" class="form-select">
                    <option value="">-Please Select-</option>
                    @foreach($stu_disability_type_master ?? [] as $val => $label)
                        <option value="{{ $val }}"
                            {{ ($basic['type_of_impairment'] ?? '') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                  </select>
                    </div>
                  </div>


                    <div class="mb-3" id="disability"  style="display:none;">
                    <label class="form-label small">(b) Disability Percentage (in %)<span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-info-circle"></i></span>
                        <input name="disability_percentage" id="disability_percentage" type="text" class="form-control" placeholder="Enter Disability in %"  value="{{ old('disability_percentage', $basic['disability_percentage'] ?? '') }}">
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small">Nationality</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-flag"></i></span>
                    <select name="nationality" class="form-select">
                        <option value="">-Please Select-</option>
                        @foreach($nationality_master ?? [] as $val => $label)
                            <option value="{{ $val }}"
                                {{ ($basic['nationality'] ?? '') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small">Is the Child enrolled as Out of School Child?</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-school"></i></span>
                      <select name="out_of_school" id="out_of_school" class="form-select">
                        <option value="">-Please Select-</option>
                            @foreach($configDropdowns['yes_no'] ?? [] as $val => $label)
                            <option value="{{ $val }}" {{ ($basic['out_of_school'] ?? '') == $val ? 'selected' : '' }}>
                            {{ $label }}
                            </option>
                            @endforeach
                      </select>
                    </div>
                  </div>

                    <div class="mb-3" id="mainstreamed_section" style="display:none;">
                    <label class="form-label small">When the child is mainstreamed</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-school"></i></span>
                      <select name="mainstreamed" id="mainstreamed" class="form-select">
                        <option value="">-Please Select-</option>
                           @foreach($child_mainstreamed_master ?? [] as $val => $label)
                            <option value="{{ $val }}"
                                {{ ($basic['mainstreamed'] ?? '') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small">Blood Group</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-droplet"></i></span>
                      <select name="blood_group" class="form-select">
                        <option value="">-Please Select-</option>
                          @foreach($blood_group_master ?? [] as $val => $label)
                            <option value="{{ $val }}"
                                {{ ($basic['blood_group'] ?? '') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                          @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small">Birth Registration Number</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-spreadsheet"></i></span>
                      <input name="birth_reg_no" id="birth_reg_no" type="text" class="form-control" placeholder="Birth registration number"   value="{{ old('birth_reg_no', $basic['birth_reg_no'] ?? '') }}">
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small">Identification Mark</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-id"></i></span>
                        <input name="identification_mark" id="identification_mark" type="text" class="form-control" placeholder="Identify mark (if any)"   value="{{ old('identification_mark', $basic['identification_mark'] ?? '') }}">
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small">Health ID</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-link-alt"></i></span>
                      <input name="health_id" id="health_id" type="text" class="form-control" placeholder="Health ID"   value="{{ old('health_id', $basic['health_id'] ?? '') }}">
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small">Relationship with Guardian</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-link-alt"></i></span>
                      <select name="relationship_with_guardian" class="form-select">
                        <option value="">-Please Select-</option>
                          @foreach($guardian_relationship_master ?? [] as $val => $label)
                            <option value="{{ $val }}"
                                {{ ($basic['relationship_with_guardian'] ?? '') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                          @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small">Anual Family income</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-school"></i></span>
                      <select name="family_income" class="form-select">
                        <option value="">-Please Select-</option>
                          @foreach($income_master ?? [] as $val => $label)
                            <option value="{{ $val }}"
                                {{ ($basic['family_income'] ?? '') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                          @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small">Student Height(in cms)</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-link-alt"></i></span>
                      <input name="student_height" type="text" class="form-control" placeholder="Student Height"   value="{{ old('student_height', $basic['student_height'] ?? '') }}">
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small">Student Weight(in Kg's)</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-link-alt"></i></span>
                      <input name="student_weight" type="text" class="form-control" placeholder="Student Weight"   value="{{ old('student_weight', $basic['student_weight'] ?? '') }}">
                    </div>
                  </div>

                    <div class="mb-3">
                      <label class="form-label small">Guardian's Qualification?</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-school"></i></span>
                      <select name="guardian_qualifications" class="form-select" required>
                      <option value="">-Select-</option>
                      <option value="1" {{ ($basic['guardian_qualifications'] ?? '' )==1 ? 'selected' : '' }}>GRADUATE</option>
                      <option value="2" {{ ($basic['guardian_qualifications'] ?? '' )==2 ? 'selected' : '' }}>BELOW GRADUATE</option>
                      <option value="2" {{ ($basic['guardian_qualifications'] ?? '' )==3 ? 'selected' : '' }}>POST GRADUATE </option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

              <div class="form-actions text-end mt-3">
                <button type="button"
                        class="btn btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmBasicInfoUpdateModal">
                    Update
                </button>
              </div>
            </form>
          </div>



  <!-- ================= CONFIRM BASIC INFO UPDATE MODAL ================= -->
<div class="modal fade" id="confirmBasicInfoUpdateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">

            <button type="button"
                    class="btn-close position-absolute end-0 m-2"
                    data-bs-dismiss="modal"></button>

            <div class="text-center p-3">
                <img src="{{ asset('images/logo/update_details_logo.png') }}"
                     width="80" height="80">

                <h5 class="fw-bold mt-2">Confirm Update</h5>

                <p class="text-muted small mb-0">
                    Are you sure you want to
                    <strong>update basic details</strong> of this student?
                </p>
            </div>

            <div class="modal-footer justify-content-center pt-0">
                <button type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">
                    No
                </button>

                <button type="button"
                        class="btn btn-success"
                        id="confirmBasicInfoUpdateBtn">
                    Yes, Update
                </button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="basicInfoSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">

            <div class="text-center p-4">
                <img src="{{ asset('images/logo/update_details_logo.png') }}"
                     width="70" height="70">

                <h5 class="fw-bold mt-3 text-success">
                    Update Successful
                </h5>

                <p class="text-muted small">
                    Basic details updated successfully.
                </p>

                <button type="button"
                        class="btn btn-success"
                        data-bs-dismiss="modal">
                    OK
                </button>
            </div>

        </div>
    </div>
</div>


<!-- ================= Failed MODAL ================= -->
<div class="modal fade" id="BasicInfoFailedModal" tabindex="-1">
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
<script>
$(document).ready(function () {

    $('#confirmBasicInfoUpdateBtn').click(function () {

        var form = $('#Edit_of_basic_info_of_student');
        var url  = form.attr('action');
        var data = form.serialize();

        $('#confirmBasicInfoUpdateBtn').prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function () {

                $('#confirmBasicInfoUpdateModal').modal('hide');
                $('#basicInfoSuccessModal').modal('show');
            },
            error: function () {
            $('#confirmBasicInfoUpdateModal').modal('hide');
            $('#BasicInfoFailedModal').modal('show');
            },
            complete: function () {
                $('#confirmBasicInfoUpdateBtn').prop('disabled', false);
            }
        });
    });

});
</script>


