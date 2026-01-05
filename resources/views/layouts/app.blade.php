<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template-free">
<meta name="csrf-token" content="{{ csrf_token() }}">

<head>
    
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>@yield('title', 'Dashboard | BSP')</title>
    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/OpenSans/opensans.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/common.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-select/select2.min.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>

    @stack('styles')
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Toast Notifications (top-right corner) -->
            <x-toast-notification position="top-end" />
            {{-- Sidebar --}}
            @include('layouts.partials.sidebar')

            <div class="layout-page">

                {{-- Navbar --}}
                @include('layouts.partials.navbar')


                <div class="content-wrapper">
                    <div class="flex-grow-1 container-p-y">
                        <!-- Error Display Component -->
                        <x-error-display />
                        @yield('content')
                    </div>

                    <!-- Impersonation Banner -->

                    @if (Auth::check() && Auth::user()->isImpersonated())
                        <div class="impersonation-banner bg-warning text-dark py-2">
                            <div class="container-fluid">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="ti ti-user-switch me-2"></i>
                                            <strong>You are currently impersonating:</strong>
                                            <span class="mx-2">{{ Auth::user()->name }}
                                                ({{ Auth::user()->email }})</span>
                                            <form method="POST" action="{{ route('admin.users.stop-impersonate') }}"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm ms-2">
                                                    <i class="ti ti-user-off me-1"></i>Back to Super Admin
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Footer --}}
                    @include('layouts.partials.footer')
                </div>
            </div>
        </div>

        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

<div class="modal fade" id="globalAlertModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header" id="globalAlertHeader">
        <h5 class="modal-title" id="globalAlertTitle"></h5>
        {{-- <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button> --}}
      </div>

    <div class="modal-body text-center">
<i id="globalAlertIcon"
   class="bx fs-1 d-block mb-3"></i>
        <h6 id="globalAlertType" class="fw-bold mb-2"></h6>
        <p id="globalAlertMessage" class="mb-0"></p>
        <ul id="globalAlertList" class="small ps-3 d-none text-start mt-2"></ul>
    </div>


      <div class="modal-footer">
        <!-- ALERT OK -->
        <button type="button" class="btn btn-primary d-none" id="globalAlertOkBtn">OK</button>

        <!-- CONFIRM -->
        <button type="button" class="btn btn-secondary d-none" id="globalAlertCancelBtn">Cancel</button>
        <button type="button" class="btn btn-danger d-none" id="globalAlertConfirmBtn">Confirm</button>
      </div>

    </div>
  </div>
</div>



    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-select/select2.min.js') }}"></script>


    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>


    @stack('scripts')
</body>

</html>
