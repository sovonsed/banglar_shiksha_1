@extends('layouts.app')

@section('title', 'Students List')

@section('content')
<div class="container-fluid full-width-content">



  <!-- STUDENT SEARCH -->
@if(optional($user->roles()->first())->name ==='HOI Primary')
 <!-- PAGE HEADING -->
  <div class="page-header mb-3 d-flex justify-content-between align-items-center">
    <h5 class="fw-bold mb-0">Search Student for Deletion</h5>
  </div>
    @include('src.modules.search_student.student_search')
@endif
 <!-- Table card -->
<div class="card card-full mb-4">
    <div class="custom-header-data-table">
        <span class="fw-semibold">Deleted Student's List</span>
        
         <div class="btn-group float-end ">
              <button type="button" class="btn btn-success dropdown-toggle btn-export " data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-export"></i> Export</button>
            <ul class="dropdown-menu dropdown-menu-end dropdown-export">
                <li><a class="dropdown-item text-primary export-print" href="#"><i class="bx bx-printer me-1"></i> Print</a></li>
                <li><a class="dropdown-item text-info export-csv" href="#"><i class="bx bx-file me-1"></i> Csv</a></li>
                <li><a class="dropdown-item text-success export-excel" href="#"><i class="bx bxs-file-export me-1"></i> Excel</a></li>
                <li><a class="dropdown-item text-danger export-pdf" href="#"><i class="bx bxs-file-pdf me-1"></i> Pdf</a></li>
            </ul>

         </div>
    </div>
    
    <div class="card-body">
      <div class="table-responsive">
      <table id="example" class="table table-striped">
        <thead>
            <tr>
                <th>SL No. </th>
                <th>Student Code</th>
                @if(optional($user->roles()->first())->name !== 'HOI Primary')
                <th>School</th>
                @endif
                <th>Name</th>
                <th>DOB</th>
                <th>Guardian Name</th>
                <th>Present Roll No.</th>
                <th>Delete Reason</th>
                @if(optional($user->roles()->first())->name === 'HOI Primary')
                <th>Student Status</th>
                @endif
                @if(optional($user->roles()->first())->name === 'SI')
                <th>Action</th>
                @endif            
              </tr>
        </thead>

        <tbody>
          @if(!empty($deleted_students) && $deleted_students->count() > 0)
              @foreach($deleted_students as $student)
                  <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $student->student_code }}</td>
                      @if(optional($user->roles()->first())->name !== 'HOI Primary')
                      <td>{{ $student->schoolInfo->school_name ?? 'N/A' }}</td>
                      @endif
                      <td>{{ $student->student_name ?? 'N/A' }}</td>
                      <td>{{ $student->studentInfo->dob ?? 'N/A' }}</td>
                      <td>{{ $student->studentInfo->guardian_name ?? 'N/A' }}</td>
                      <td>{{ $student->studentInfo->cur_roll_number ?? 'N/A' }}</td>
                      <td>{{ $student->deleteReason->name ?? 'N/A' }}</td>
                      @if(optional($user->roles()->first())->name === 'HOI Primary')
                      <td>
                          @switch($student->status)
                              @case(1)
                                  <span class="badge bg-primary">Sent to SI</span>
                                  @break

                              @case(2)
                                  <span class="badge bg-danger">Deleted</span>
                                  @break

                              @case(3)
                                  <span class="badge bg-warning text-dark">Rejected</span>
                                  @break

                              @default
                                  <span class="badge bg-secondary">N/A</span>
                          @endswitch
                      </td>
                      @endif
                      @if(optional($user->roles()->first())->name === 'SI')
                      @switch($student->status)
                        @case(1)
                          <td>
                            <div class="row g-2">
                                <div class="col-6 text-center">
                                    <button type="button"
                                        class="btn btn-warning btn-sm w-100 btn-reject" data-student-code="{{ $student->student_code }}" data-status="3">
                                        <i class="bx bx-x"></i> Reject
                                    </button>
                                </div>
                                <div class="col-6 text-center">
                                    <button type="button"
                                        class="btn btn-success btn-sm w-100 btn-approve" data-student-code="{{ $student->student_code }}" data-status="2">
                                        <i class="bx bx-check-circle"></i> Approve
                                    </button>
                                </div>
                            </div>
                          </td>
                          @break
                        @case(2)
                        <td>
                          <span class="badge bg-danger">Deleted</span>
                          @break
                        </td>

                      @case(3)
                        <td>
                          <span class="badge bg-warning text-dark">Rejected</span>
                          @break
                        </td>

                      @default
                      <td>
                          <span class="badge bg-secondary">N/A</span>
                      </td>
                      @break
                      @endswitch
                      @endif
                  </tr>
              @endforeach
          @endif
        </tbody>

      </table>
      </div>
    </div>
  </div>
</div>
<div class="error-container" id="errorDisplay"></div>

@endsection

@push('styles')


  <!-- Local DataTables CSS -->
  <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/common.css') }}">

@endpush

@push('scripts')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>

<!-- Buttons extension -->
<script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>

<!-- dependencies for HTML5 export (must load BEFORE buttons.html5) -->
<script src="{{ asset('js/jszip.min.js') }}"></script>
<script src="{{ asset('js/pdfmake.min.js') }}"></script>
<script src="{{ asset('js/vfs_fonts.js') }}"></script>

<!-- Buttons HTML5 / Print -->
<script src="{{ asset('js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('js/buttons.print.min.js') }}"></script>
<script src="{{ asset('assets/js/common.js') }}"></script>

<script>
  $(document).ready(function() {
    let table = $('#example').DataTable({
      ordering: true,
      dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6 text-end"f>>' +
           'rt' +
           '<"row mt-3"<"col-sm-6"i><"col-sm-6"p>>' +
           '<"d-none"B>',
      buttons: [
        {
          extend: 'print',
          title: 'Students',
          exportOptions: {
            // exclude the column that has class "no-export" (Actions)
            columns: ':not(.no-export)'
          }
        },
        {
          extend: 'csv',
          title: 'students_list',
          exportOptions: {
            columns: ':not(.no-export)'
          }
        },
        {
          extend: 'excel',
          title: 'students_list',
          exportOptions: {
            columns: ':not(.no-export)'
          }
        },
        {
          extend: 'pdf',
          title: 'students_list',
          exportOptions: {
            columns: ':not(.no-export)'
          }
        }
      ]
    });

    // Attach export buttons to your dropdown menu using Buttons API selectors
    $(document).on('click', '.export-print', function(e) {
      e.preventDefault();
      table.button('.buttons-print').trigger();
    });

    $(document).on('click', '.export-csv', function(e) {
      e.preventDefault();
      table.button('.buttons-csv').trigger();
    });

    $(document).on('click', '.export-excel', function(e) {
      e.preventDefault();
      table.button('.buttons-excel').trigger();
    });

    $(document).on('click', '.export-pdf', function(e) {
      e.preventDefault();
      table.button('.buttons-pdf').trigger();
    });
  });
</script>
<script>
    $(document).ready(function () {
        const USER_ROLE = @json(optional($user->roles()->first())->name);
      $("#search_purpose").val('1');
       let SELECTED_STUDENT_CODE = null;
        let DELETE_REASONS = [];

        $("#search_purpose").val('2');

        /* load once */
        function loadDeleteReasons() {
            return sendRequest(
                "{{ route('get.reason.for.deletion') }}",
                "GET"
            ).then(res => {
                DELETE_REASONS = (res.status && Array.isArray(res.data))
                    ? res.data
                    : [];
                populateDeleteReasons();
            });
        }

        function populateDeleteReasons() {
            let options = `<option value="">Select Reason</option>`;

            if (DELETE_REASONS.length) {
                DELETE_REASONS.forEach(r => {
                    options += `<option value="${r.id}">${r.name}</option>`;
                });
            } else {
                options += `<option value="">No reasons available</option>`;
            }

            $("#delete_reason").html(options);
        }
    function populateStudentRow(d) {

        // safety check
        if (!d || !d.student_code) {
            showEmptyRow('Invalid student data');
            return;
        }

        // store globally (card-based flow)
        SELECTED_STUDENT_CODE = d.student_code;
        $("#selected_student_code").val(d.student_code);

        const row = `
            <tr>
                <td class="student-code" data-student-code="${d.student_code}">
                    ${d.student_code}
                </td>
                <td>${d.studentname ?? '-'}</td>
                <td>${d.dob ?? '-'}</td>
                <td>${d.guardian_name ?? '-'}</td>
                <td>${d.current_class ?? '-'}</td>
                <td>${d.current_section ?? '-'}</td>
                <td>${d.cur_roll_number ?? '-'}</td>
            </tr>
        `;

        $("#student_result_body").html(row);

        // show deactivate card (HOI Primary flow)
            $("#delete_details_card").removeClass('d-none');
    }
        loadDeleteReasons();

$("#btn_search_student").on("click", function (e) {
    e.preventDefault();

    if (!validateRequiredFields("#student_search_form")) return;

    const $btn = $(this).prop('disabled', true).text('Searching...');

    sendRequest(
        "{{ route('search.student.by.student_code') }}",
        "POST",
        "#student_search_form"
    )
    .then(res => {
        if (res.status) {

            SELECTED_STUDENT_CODE = res.data.student_code;
            $("#selected_student_code").val(SELECTED_STUDENT_CODE);

            populateStudentRow(res.data);


            // role-based section
            $("#delete_hoi_section, #delete_si_section").addClass('d-none');

            if (USER_ROLE === 'HOI Primary') {
                $("#delete_hoi_section").removeClass('d-none');
            }
            if (USER_ROLE === 'SI') {
                $("#delete_si_section").removeClass('d-none');
            }

        } else {
            SELECTED_STUDENT_CODE = null;
            $("#delete_details_card").addClass('d-none');
            showEmptyRow(res.message || 'Student not found');
        }
    })
    .catch(() => {
        SELECTED_STUDENT_CODE = null;
        $("#delete_details_card").addClass('d-none');
        showEmptyRow('Something went wrong');
    })
    .finally(() => {
        $btn.prop('disabled', false).text('Search');
    });
});

$(document).on('click', '#btn_send_to_si', function () {

    if (!SELECTED_STUDENT_CODE) {
        showAlert({ type: 'info', message: 'Search a student first.' });
        return;
    }

    const delete_reason_code_fk = $("#delete_reason").val();

    if (!delete_reason_code_fk) {
        showAlert({
            type: 'info',
            message: 'Please select a delete reason.'
        });
        return;
    }

    showAlert({
        type: 'warning',
        title: 'Send to SI',
        message: 'Do you want to send this deletion request to SI?',
        confirmText: 'Send'
    }).then(confirmed => {

        if (!confirmed) return;

        const $btn = $(this).prop('disabled', true).text('Sending...');

        sendRequest(
            "{{ route('student.delete') }}",
            "POST",
            null,
            {
                student_code: SELECTED_STUDENT_CODE,
                delete_reason_code_fk,
                status: 1,
                _token: "{{ csrf_token() }}"
            }
        )
        .then(res => {
            if (res.status) {
                showAlert({
                    type: 'success',
                    message: res.message
                }).then(() => location.reload());
            } else {
                showAlert({
                    type: 'error',
                    message: res.message || 'Failed to send'
                });
            }
        })
        .catch(() => {
            showAlert({ type: 'error', message: 'Something went wrong' });
        })
        .finally(() => {
            $btn.prop('disabled', false).text('Send to SI');
        });
    });
});

$(document).on('click', '#btn_approve_delete', function () {

    showAlert({
        type: 'warning',
        title: 'Approve Deletion',
        message: 'Do you really want to delete this student?',
        confirmText: 'Approve'
    }).then(confirmed => {

        if (!confirmed) return;

        const $btn = $(this).prop('disabled', true).text('Approving...');

        sendRequest(
            "{{ route('student.delete') }}",
            "POST",
            null,
            {
                student_code: SELECTED_STUDENT_CODE,
                status: 2,
                _token: "{{ csrf_token() }}"
            }
        )
        .then(res => {
            if (res.status) {
                showAlert({
                    type: 'success',
                    message: res.message
                }).then(() => location.reload());
            } else {
                showAlert({
                    type: 'error',
                    message: res.message || 'Approval failed'
                });
            }
        })
        .catch(() => {
            showAlert({ type: 'error', message: 'Something went wrong' });
        })
        .finally(() => {
            $btn.prop('disabled', false).text('Approve');
        });
    });
});
$(document).on('click', '#btn_reject_delete', function () {

    showAlert({
        type: 'warning',
        title: 'Reject Deletion',
        message: 'Do you really want to reject this deletion request?',
        confirmText: 'Reject'
    }).then(confirmed => {

        if (!confirmed) return;

        const $btn = $(this).prop('disabled', true).text('Rejecting...');

        sendRequest(
            "{{ route('student.delete') }}",
            "POST",
            null,
            {
                student_code: SELECTED_STUDENT_CODE,
                status: 3,
                _token: "{{ csrf_token() }}"
            }
        )
        .then(res => {
            if (res.status) {
                showAlert({
                    type: 'success',
                    message: res.message
                }).then(() => location.reload());
            } else {
                showAlert({
                    type: 'error',
                    message: res.message || 'Reject failed'
                });
            }
        })
        .catch(() => {
            showAlert({ type: 'error', message: 'Something went wrong' });
        })
        .finally(() => {
            $btn.prop('disabled', false).text('Reject');
        });
    });
});

    });
</script>
@endpush




