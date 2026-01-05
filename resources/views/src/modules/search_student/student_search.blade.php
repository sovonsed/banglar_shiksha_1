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
                @if(optional($user->roles()->first())->name === 'HOI Primary')
                <th>Select Reason</th>
                @endif
                <th>Action</th>
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

