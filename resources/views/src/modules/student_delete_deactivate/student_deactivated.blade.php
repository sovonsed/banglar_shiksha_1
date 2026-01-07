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
    let SELECTED_STUDENT_CODE = null;

    /* Load reasons once */
    function loadDeactivateReasons() {
        return sendRequest(
            "{{ route('get.reason.for.deactivation') }}",
            "GET"
        ).then(res => {
            DEACTIVATION_REASONS = (res.status && Array.isArray(res.data))
                ? res.data
                : [];
            populateDeactivateReasonDropdown();
        });
    }

    function populateDeactivateReasonDropdown() {
        let options = `<option value="">Select Reason</option>`;

        if (DEACTIVATION_REASONS.length) {
            DEACTIVATION_REASONS.forEach(r => {
                options += `<option value="${r.id}">${r.name}</option>`;
            });
        } else {
            options += `<option value="">No reasons available</option>`;
        }

        $("#deactivation_reason").html(options);
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
        $("#deactivate_details_card").removeClass('d-none');
    }

    /* call once on page load */
    loadDeactivateReasons();

    $("#btn_search_student").on("click", function (e) {
        e.preventDefault();

        if (!validateRequiredFields("#student_search_form")) return;

        const $btn = $(this).prop('disabled', true).text('Searching...');

        sendRequest(
            "{{ route('student.search.student_code') }}",
            "POST",
            "#student_search_form"
        )
        .then(res => {
            if (res.status) {

                SELECTED_STUDENT_CODE = res.data.student_code;
                $("#selected_student_code").val(SELECTED_STUDENT_CODE);

                populateStudentRow(res.data);

                $("#deactivate_details_card").removeClass('d-none');
            } else {
                SELECTED_STUDENT_CODE = null;
                $("#deactivate_details_card").addClass('d-none');
                showEmptyRow(res.message || 'Student not found');
            }
        })
        .catch(() => {
            SELECTED_STUDENT_CODE = null;
            $("#deactivate_details_card").addClass('d-none');
            showEmptyRow('Something went wrong');
        })
        .finally(() => {
            $btn.prop('disabled', false).text('Search');
        });
    });

    $(document).on('click', '#btn_deactivate_student', function () {

        if (!SELECTED_STUDENT_CODE) {
            showAlert({ type: 'info', message: 'Search a student first.' });
            return;
        }

        const deactivate_reason_code_fk = $("#deactivation_reason").val();

        if (!deactivate_reason_code_fk) {
            showAlert({
                type: 'info',
                message: 'Please select a deactivation reason.'
            });
            return;
        }

        showAlert({
            type: 'warning',
            title: 'Deactivate Student',
            message: 'Do you really want to deactivate this student?',
            confirmText: 'Deactivate'
        }).then(confirmed => {

            if (!confirmed) return;

            const $btn = $(this).prop('disabled', true).text('Deactivating...');

            sendRequest(
                "{{ route('student.deactivate') }}",
                "POST",
                null,
                {
                    student_code: SELECTED_STUDENT_CODE,
                    deactivate_reason_code_fk,
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
                        message: res.message || 'Deactivation failed'
                    });
                }
            })
            .catch(() => {
                showAlert({
                    type: 'error',
                    message: 'Something went wrong'
                });
            })
            .finally(() => {
                $btn.prop('disabled', false).text('Deactivate');
            });
        });
    });

    $(document).on('click', '#btn_activate_student', function () {

        if (!SELECTED_STUDENT_CODE) {
            showAlert({ type: 'info', message: 'Search a student first.' });
            return;
        }

        showAlert({
            type: 'warning',
            title: 'Activate Student',
            message: 'Do you really want to activate this student?',
            confirmText: 'Activate'
        }).then(confirmed => {

            if (!confirmed) return;

            const $btn = $(this).prop('disabled', true).text('Activating...');

            sendRequest(
                "{{ route('student.activate') }}",
                "POST",
                null,
                {
                    student_code: SELECTED_STUDENT_CODE,
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
                        message: res.message || 'Activation failed'
                    });
                }
            })
            .catch(() => {
                showAlert({
                    type: 'error',
                    message: 'Something went wrong'
                });
            })
            .finally(() => {
                $btn.prop('disabled', false).text('Activate');
            });
        });
    });

    });
</script>
@endpush
