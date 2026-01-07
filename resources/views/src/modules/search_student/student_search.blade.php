  <div class="card mb-3">
    <div class="card-header bg-primary text-white py-2">Student Search</div>
    <div class="card-body p-2">
      <form id="student_search_form">
        <div class="row align-items-center text-center">
        <div class="col-md-3"></div>
       <div class="col-md-5 mb-2">
        <label class="d-none" for="student_code">Student Code</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bx bx-id-card"></i></span>
            <input
                type="text"
                class="form-control"
                id="student_code"
                name="student_code"
                placeholder="Enter 14 Digit Student Code"
                maxlength="14"
                minlength="14"
                pattern="[0-9]{14}"
                inputmode="numeric"
                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                required/>
          </div>
        </div>
        <input type="text" class="d-none" id="search_purpose" name="search_purpose" value=""/>
        <div class="col-md-1 mb-2 text-end">
          <button type="submit" class="btn btn-primary w-100" id="btn_search_student">Search</button>
        </div>
        <div class="col-md-3"></div>
        
        </div>
      </form>
    </div>
  </div>
  <div class="card mb-3">
    <div class="card-header bg-primary text-white py-2">Student Details</div>
    <div class="card-body p-2">
      <table class="table table-striped">
        <thead>
            <tr>
                <th>Student Code</th>
                <th>Name</th>
                <th>DOB</th>
                <th>Guardian Name</th>
                <th>Present Class</th>
                <th>Present Section</th>
                <th>Present Roll No.</th>
            </tr>
        </thead>
        <tbody id="student_result_body">
            <tr>
                <td colspan="10" class="text-center text-muted">
                    Search student to view details
                </td>
            </tr>
        </tbody>
      </table>

    </div>
  </div>
<div class="card mb-3 d-none" id="deactivate_details_card">
    <div class="card-header bg-warning text-dark py-2">
        <i class="bx bx-x-circle"></i> Deactivate Details
    </div>

    <div class="card-body p-2">
        <form id="deactivate_form">
            <input type="hidden" id="selected_student_code">

            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Deactivation Reason</label>
                    <select class="form-select form-select-sm"
                            id="deactivation_reason">
                        <option value="">Loading...</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="button"
                            class="btn btn-warning btn-sm w-100"
                            id="btn_deactivate_student">
                        <i class="bx bx-x-circle"></i> Deactivate
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="card mb-3 d-none" id="delete_details_card">
    <div class="card-header bg-danger text-white py-2">
        <i class="bx bx-trash"></i> Delete Details
    </div>

    <div class="card-body p-2">
        <form id="delete_form">
            <input type="hidden" id="selected_student_code">

            <!-- HOI PRIMARY -->
            <div id="delete_hoi_section" class="d-none">
                <div class="row g-2">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Delete Reason</label>
                        <select class="form-select form-select-sm"
                                id="delete_reason">
                            <option value="">Loading...</option>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button"
                                class="btn btn-info btn-sm w-100"
                                id="btn_send_to_si">
                            <i class="bx bx-send"></i> Send to SI
                        </button>
                    </div>
                </div>
            </div>

            <!-- SI -->
            <div id="delete_si_section" class="d-none mt-3">
                <div class="row g-2">
                    <div class="col-6">
                        <button type="button"
                                class="btn btn-success btn-sm w-100"
                                id="btn_approve_delete">
                            <i class="bx bx-check-circle"></i> Approve
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button"
                                class="btn btn-warning btn-sm w-100"
                                id="btn_reject_delete">
                            <i class="bx bx-x-circle"></i> Reject
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<div class="card mb-3 d-none" id="transfer_out_details_card">
    <div class="card-header bg-info text-white py-2">
        <i class="bx bx-transfer"></i> Transfer Out Details
    </div>

    <div class="card-body p-2">
        <form id="transfer_out_form">
            <input type="hidden" id="selected_student_code">

            <div class="row g-2">
                  <div class="col-md-6">
                        <label class="form-label fw-bold">Reason</label>
                        <select class="form-control" id="transfer_out_reason">
                          <option value="">-Please Select- </option>
                        <option value="3">Change of Residence</option>
                        <option value="1">Change of School</option>
                        </select>
                    </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Date of Leaving</label>
                    <input type="date"
                           class="form-control form-control-sm"
                           id="date_of_leaving">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="button"
                            class="btn btn-info btn-sm w-100"
                            id="btn_transfer_out" id="btn_transfer_out">
                        <i class="bx bx-transfer"></i> Transfer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


