@extends('layouts.app') @section('title', 'Students List') @section('content')
<div class="container-fluid full-width-content">
    <!-- STUDENT SEARCH -->


    @if(optional($user->roles()->first())->name ==='HOI Primary')
        <!-- PAGE HEADING -->
    <div
        class="page-header mb-3 d-flex justify-content-between align-items-center"
    >
        <h5 class="fw-bold mb-0">Search Student for Dectivation</h5>
</div>
        @include('src.modules.search_student.student_search')
    @endif
    <!-- Table card -->
    <div class="card card-full mb-4">
        <div class="custom-header-data-table">
            <span class="fw-semibold">Deactivated Student's List</span>

            <div class="btn-group float-end">
                <button
                    type="button"
                    class="btn btn-success dropdown-toggle btn-export"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    <i class="bx bx-export"></i> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-export">
                    <li>
                        <a
                            class="dropdown-item text-primary export-print"
                            href="#"
                            ><i class="bx bx-printer me-1"></i> Print</a
                        >
                    </li>
                    <li>
                        <a class="dropdown-item text-info export-csv" href="#"
                            ><i class="bx bx-file me-1"></i> Csv</a
                        >
                    </li>
                    <li>
                        <a
                            class="dropdown-item text-success export-excel"
                            href="#"
                            ><i class="bx bxs-file-export me-1"></i> Excel</a
                        >
                    </li>
                    <li>
                        <a class="dropdown-item text-danger export-pdf" href="#"
                            ><i class="bx bxs-file-pdf me-1"></i> Pdf</a
                        >
                    </li>
                </ul>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped">
                    <thead>
                        <tr>
                            <th>SL No.</th>
                            <th>Student Code</th>
                            @if(optional($user->roles()->first())->name !== 'HOI
                            Primary')
                            <th>School</th>
                            @endif

                            <th>Name</th>
                            <th>DOB</th>
                            <th>Guardian Name</th>
                            <th>Present Class</th>
                            <th>Present Section</th>
                            <th>Present Roll No.</th>
                            <th>Deactivation Reason</th>
                            @if(optional($user->roles()->first())->name ===
                            'SI')
                            <th>Action</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @if(!empty($deactive_students) &&
                        $deactive_students->count() > 0)
                        @foreach($deactive_students as $student)
                        <tr>
                            <td>{{ $loop->iteration}}</td>
                            <td
                                class="student-code"
                                data-student-code="{{ $student->student_code }}"
                            >
                                {{ $student->student_code }}
                            </td>
                            @if(optional($user->roles()->first())->name !== 'HOI
                            Primary')
                            <td>
                                {{ $student->schoolInfo->school_name ?? 'N/A' }}
                            </td>
                            @endif
                            <td>
                                {{ $student->studentInfo->getAttributes()['studentname'] ?? 'N/A' }}
                            </td>
                            <td>{{ $student->studentInfo->dob ?? 'N/A' }}</td>
                            <td>
                                {{ $student->studentInfo->guardian_name ?? 'N/A' }}
                            </td>
                            <td>{{ $student->currentClass->name ?? 'N/A'}}</td>
                            <td>
                                {{ $student->currentSection->name ?? 'N/A'}}
                            </td>
                            <td>
                                {{ $student->studentInfo->cur_roll_number ?? 'N/A' }}
                            </td>
                            <td>{{ $student->deleteReason->name ?? 'N/A' }}</td>
                            @if(optional($user->roles()->first())->name ===
                            'SI')
                            <td>
                                <button
                                    type="button"
                                    class="btn btn-success btn-activate btn-export"
                                >
                                    <i class="bx bx-check-circle"></i> Activate
                                </button>
                            </td>
                            @endif
                        </tr>
                        @endforeach @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection @push('styles')
<!-- Local DataTables CSS -->
<link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/common.css') }}" />
@endpush @push('scripts')
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
    $(document).ready(function () {
        let table = $("#example").DataTable({
            ordering: true,
            dom:
                '<"row mb-3"<"col-sm-6"l><"col-sm-6 text-end"f>>' +
                "rt" +
                '<"row mt-3"<"col-sm-6"i><"col-sm-6"p>>' +
                '<"d-none"B>',
            buttons: [
                {
                    extend: "print",
                    title: "Students",
                    exportOptions: {
                        // exclude the column that has class "no-export" (Actions)
                        columns: ":not(.no-export)",
                    },
                },
                {
                    extend: "csv",
                    title: "students_list",
                    exportOptions: {
                        columns: ":not(.no-export)",
                    },
                },
                {
                    extend: "excel",
                    title: "students_list",
                    exportOptions: {
                        columns: ":not(.no-export)",
                    },
                },
                {
                    extend: "pdf",
                    title: "students_list",
                    exportOptions: {
                        columns: ":not(.no-export)",
                    },
                },
            ],
        });

        // Attach export buttons to your dropdown menu using Buttons API selectors
        $(document).on("click", ".export-print", function (e) {
            e.preventDefault();
            table.button(".buttons-print").trigger();
        });

        $(document).on("click", ".export-csv", function (e) {
            e.preventDefault();
            table.button(".buttons-csv").trigger();
        });

        $(document).on("click", ".export-excel", function (e) {
            e.preventDefault();
            table.button(".buttons-excel").trigger();
        });

        $(document).on("click", ".export-pdf", function (e) {
            e.preventDefault();
            table.button(".buttons-pdf").trigger();
        });
    });
</script>
<script>
    $(document).ready(function () {
      const USER_ROLE = @json(optional($user->roles()->first())->name);
      let DEACTIVATION_REASONS = [];
      $("#search_purpose").val('1');
      loadDeactivateReasons();
      /* Fetch reasons only once */
    function loadDeactivateReasons() {
        return sendRequest(
            "{{ route('get.reason.for.deactivation') }}",
            "GET"
        )
        .then(res => {
            if (res.status && Array.isArray(res.data)) {
                DEACTIVATION_REASONS = res.data;   
            } else {
                DEACTIVATION_REASONS = [];
            }
            return DEACTIVATION_REASONS;
        })
        .catch(err => {
            console.error(err);
            DEACTIVATION_REASONS = [];
            return [];
        });
    }
    function buildDeactivateReasonOptions() {
        let options = `<option value="">Select Reason</option>`;

        if (DEACTIVATION_REASONS.length > 0) {
            DEACTIVATION_REASONS.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
        } else {
            options += `<option value="">No reasons available</option>`;
        }

        return options;
    }
    function populateStudentRow(d) {
      let actionTd = '';

      if (USER_ROLE === 'SI') {

          actionTd = `
              <td>
                  <button type="button"
                          class="btn btn-success btn-activate">
                      <i class="bx bx-check-circle"></i> Activate
                  </button>
              </td>
          `;

      } else if (USER_ROLE === 'HOI Primary') {

          actionTd = `
              <td>
                  <select class="form-select form-select-sm deactivation-reason"
                          data-student-code="${d.student_code}">
                      ${buildDeactivateReasonOptions()}
                  </select>
              </td>
              <td>
                  <button class="btn btn-sm btn-warning btn-export deactivate-btn"
                          data-student-code="${d.student_code}">
                      <i class="bx bx-x-circle"></i> Deactivate
                  </button>
              </td>
          `;

      } else {

          actionTd = `
              <td>
                  <span class="text-muted">No Action</span>
              </td>
          `;
      }

      let row = `
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
              ${actionTd}
          </tr>
      `;

      $("#student_result_body").html(row); // replace existing rows
    }
    function showEmptyRow(message) {
        $("#student_result_body").html(`
            <tr>
                <td colspan="10" class="text-center text-danger">
                    ${message}
                </td>
            </tr>
        `);
    }
    $("#btn_search_student").on("click", function (e) {
        if (!validateRequiredFields("#student_search_form")) {
            return;
        }

        let $btn = $(this);
        $btn.prop('disabled', true).text('Searching...');

        let url = "{{ route('search.student.by.student_code') }}";

        sendRequest(url, "POST", "#student_search_form")
            .then(res => {

                $btn.prop('disabled', false).text('Search');

                if (res.status) {
                    populateStudentRow(res.data);
                } else {
                    showEmptyRow(res.message || 'Student not found');
                }
            })
            .catch(err => {
                $btn.prop('disabled', false).text('Search');
                console.error(err);
                showEmptyRow('Something went wrong');
        });
    });
    $(document).on('click', '.deactivate-btn', function (e) {
        e.preventDefault();
        showAlert({
            type: 'warning',
            title: 'Deactivate',
            message: 'Do you really want to deactivate this student?',
            confirmText: 'Deactivate'
        }).then(confirmed  => {
            if (confirmed ) {
            let $btn = $(this);
            $btn.prop('disabled', true).text('Deactivating...');

            // collect values (example: from hidden inputs)
            let student_code = $(this).data('student-code');

            let deactivate_reason_code_fk = $('select.deactivation-reason').val();

            if (!deactivate_reason_code_fk) {
            showAlert({
                    type: 'info',
                    message: 'Please select a deactivation reason.'
                });            
                $btn.prop('disabled', false).text('Deactivate');
                return;
            }

            let url = "{{ route('student.deactivate') }}";

            sendRequest(url, "POST", null,{
                student_code,
                deactivate_reason_code_fk,
                _token: "{{ csrf_token() }}"
            })
            .then(res => {
                $btn.prop('disabled', false).text('Deactivate');

                if (res.status === true) {
                showAlert({
                    type: 'success',
                    message: res.message
                }).then(() => {
                location.reload();
                });

                } else {
                    showAlert({
                        type: 'error',
                        message: res.message || 'Failed to deactivate student'
                    });
                }
            })
            .catch(err => {
                $btn.prop('disabled', false).text('Deactivate');
            showAlert({
                    type: 'error',
                    message: 'Something went wrong.'
                });
            });
        }
        });
    });
    $(document).on('click', '.btn-activate', function (e) {
        e.preventDefault();
        showAlert({
            type: 'warning',
            title: 'Activate',
            message: 'Do you really want to activate this student deletion?',
            confirmText: 'Reject'
        }).then(confirmed  => {
            if (confirmed ) {
            let $btn = $(this);
            $btn.prop('disabled', true).text('Activating...');

            // collect values (example: from hidden inputs)
            let student_code = $(this)
            .closest('tr')
            .find('.student-code')
            .data('student-code');

            let url = "{{ route('student.activate') }}";

            sendRequest(url, "POST", null,{
                student_code,
                _token: "{{ csrf_token() }}"
            })
            .then(res => {
                $btn.prop('disabled', false).text('Activate');

                if (res.status === true) {
                showAlert({
                    type: 'success',
                    message: res.message
                }).then(() => {
                location.reload();
                });

                } else {
                    showAlert({
                        type: 'error',
                        message: res.message || 'Failed to activate student'
                    });
                }
            })
            .catch(err => {
                $btn.prop('disabled', false).text('Activate');
            showAlert({
                    type: 'error',
                    message: 'Something went wrong.'
                });
            });
        }
        });
    });
    });
</script>
@endpush
