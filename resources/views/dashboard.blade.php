<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard - ' . config('app.name'))
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back, ' . Auth::user()->name)

@section('page-actions')
    <div class="btn-list">
        <a href="#" class="btn btn-primary d-none d-sm-inline-block">
            <i class="ti ti-report-analytics me-2"></i>
            Generate Report
        </a>
        <a href="#" class="btn btn-outline-primary">
            <i class="ti ti-plus me-2"></i>
            New Entry
        </a>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('Leaflet_map/leaflet.css') }}">
@endpush

@section('content')

    <div class="container-fluid px-4"> <!-- full-width container -->
        <div class="row">
            @php
                $masterDetails = [];
            @endphp


            @if (is_district_officer())
                <p>District: {{ master_details()->name }}</p>
            @endif

            @if (is_si_officer())
                <p>Circle: {{ master_details() }}</p>
            @endif


            @if (is_hoi_pe_user())
                @php
                    $masterDetails = master_details();
                @endphp
            @endif


            <div class="col-lg-8 mb-5">
                <div class="card">
                    <div class="d-flex align-items-center row">
                        <div class="col-sm-7">
                            <div class="card-body pb-5 institution-details">
                                <p class="text-secondary heading_dashboard mb-2 d-block">
                                    <strong>Head of Institution</strong>
                                </p>
                                @if ($masterDetails)
                                    <h4 class="text-primary mb-2">
                                        <strong>{{ strtoupper($masterDetails->school_name) }}</strong>
                                    </h4>
                                    <p class="mb-0 fw-semibold text-dark">
                                        DISE CODE {{ $masterDetails->dise_code ?? $masterDetails->schcd }}
                                    </p>
                                    <p class="mb-0 text-muted">
                                        Established: {{ $masterDetails->establishment_year ?? 'N/A' }}
                                    </p>
                                    <p class="mb-0 text-muted">
                                        School Code: {{ $masterDetails->school_unique_code ?? 'N/A' }}
                                    </p>
                                @else
                                    <h4 class="text-primary mb-2"><strong>ADHATA HIGH SCHOOL (H.S)</strong></h4>
                                    <p class="mb-0 fw-semibold text-dark">DISE CODE 19110101614</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="px-md-4">
                                <img class="img-fluid" src="{{ asset('images/student.png') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 mb-5">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between mb-4">
                            <div class="avatar-md lt-green">
                                <i class="bx bxs-school text-success"></i>
                            </div>
                        </div>
                        <h3 class="card-title mb-2 fw-semibold">SCHOOL INFO</h3>
                        <a href="javascript:;" class="btn btn-sm btn-outline-primary mt-2">View More</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 mb-5">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between mb-4">
                            <div class="avatar-md lt-blue ">
                                <i class="bx bx-desktop text-primary"></i>
                            </div>
                        </div>
                        <h3 class="card-title mb-2 fw-semibold">SMS</h3>
                        <a href="javascript:;" class="btn btn-sm btn-outline-primary mt-2">View More</a>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-12 col-md-12 col-xxl-4">
                <h5 class="fw-bold mb-3">Student Details</h5>
                <div class="row">
                    <div class="col-6 col-md-3 col-xxl-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar lt-green">
                                        <i class="bx bx-food-menu text-success"></i>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt3"
                                            data-bs-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="">
                                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-show"></i>
                                                View More</a>
                                        </div>
                                    </div>
                                </div>
                                <h3 class="card-title mb-2 fw-semibold">Total Enrolment</h3>
                                <h2 class="fw-bold text-success">791</h2>
                                <div class="progress card-progress">
                                    <div class="progress-bar bg-success" role="progressbar" style="width:75%"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3 col-xxl-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar lt-blue">
                                        <i class="bx bx-group text-primary"></i>
                                    </div>
                                    <!--<a href="javascript:;" class="btn btn-sm btn-outline-primary">View</a>-->
                                    <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt3"
                                            data-bs-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="">
                                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-show"></i>
                                                View More</a>
                                        </div>
                                    </div>
                                </div>
                                <h3 class="card-title mb-2 fw-semibold">Boys</h3>
                                <h2 class="fw-bold text-primary">90</h2>
                                <div class="progress card-progress">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width:50%"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3 col-xxl-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar lt-red">
                                        <i class="bx bx-group text-danger"></i>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt3"
                                            data-bs-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="">
                                            <a class="dropdown-item" href="javascript:void(0);"><i
                                                    class="bx bx-show"></i> View More</a>
                                        </div>
                                    </div>
                                </div>
                                <h3 class="card-title mb-2 fw-semibold">Girls</h3>
                                <h2 class="fw-bold text-danger">30</h2>
                                <div class="progress card-progress">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width:30%"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3 col-xxl-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar lt-purple">
                                        <i class="bx bx-group text-purple"></i>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt3"
                                            data-bs-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="">
                                            <a class="dropdown-item" href="javascript:void(0);"><i
                                                    class="bx bx-show"></i> View More</a>
                                        </div>
                                    </div>
                                </div>
                                <h3 class="card-title mb-2 fw-semibold">Transgenders</h3>
                                <h2 class="fw-bold text-purple">0</h2>
                                <div class="progress card-progress">
                                    <div class="progress-bar bg-purple" role="progressbar" style="width:0"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-12 col-xxl-4">
                <h5 class="fw-bold mb-3">Teacher Details</h5>
                <div class="row">
                    <div class="col-6 col-md-3 col-xxl-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar lt-green">
                                        <i class="bx bx-user text-success"></i>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt3"
                                            data-bs-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="">
                                            <a class="dropdown-item" href="javascript:void(0);"><i
                                                    class="bx bx-show"></i> View More</a>
                                        </div>
                                    </div>
                                </div>
                                <h3 class="card-title mb-2 fw-semibold">Total Teacher</h3>
                                <h2 class="fw-bold text-success">53</h2>
                                <div class="progress card-progress">
                                    <div class="progress-bar bg-success" role="progressbar" style="width:50%"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3 col-xxl-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar lt-yellow">
                                        <i class="bx bx-group text-warning"></i>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt3"
                                            data-bs-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="">
                                            <a class="dropdown-item" href="javascript:void(0);"><i
                                                    class="bx bx-show"></i> View More</a>
                                        </div>
                                    </div>
                                </div>
                                <h3 class="card-title mb-2 fw-semibold">Teaching Staff</h3>
                                <h2 class="fw-bold text-warning">45</h2>
                                <div class="progress card-progress">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width:45%"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3 col-xxl-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar lt-red">
                                        <i class="bx bx-group text-danger"></i>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt3"
                                            data-bs-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="">
                                            <a class="dropdown-item" href="javascript:void(0);"><i
                                                    class="bx bx-show"></i> View More</a>
                                        </div>
                                    </div>
                                </div>
                                <h3 class="card-title mb-2 fw-semibold">Para Teacher</h3>
                                <h2 class="fw-bold text-danger">10</h2>
                                <div class="progress card-progress">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width:10%"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3 col-xxl-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar lt-seegreen">
                                        <i class="bx bx-group text-info"></i>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt3"
                                            data-bs-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="">
                                            <a class="dropdown-item" href="javascript:void(0);"><i
                                                    class="bx bx-show"></i> View More</a>
                                        </div>
                                    </div>
                                </div>
                                <h3 class="card-title mb-2 fw-semibold">Nonteaching Staff</h3>
                                <h2 class="fw-bold text-info">4</h2>
                                <div class="progress card-progress">
                                    <div class="progress-bar bg-info" role="progressbar" style="width:4%"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-12 col-md-12 col-xl-4">
                <h5 class="fw-bold mb-3">Location</h5>
                <div class="card">
                    <div class="card-body">
                        <div id="map" class="map"></div>
                        <!-- DMS INPUTS -->
                        <div class="col-12 mt-3">
                            <label class="form-label fw-semibold text-uppercase small">Latitude (DMS)</label>
                            <div class="input-group mb-3">
                                <input type="text" id="lat_deg" class="form-control" placeholder="°"
                                    value="22">
                                <input type="text" id="lat_min" class="form-control" placeholder="′"
                                    value="31">
                                <input type="text" id="lat_sec" class="form-control" placeholder="″"
                                    value="12.00">
                                <select id="lat_dir" class="form-select input-group-text">
                                    <option value="N" selected>N</option>
                                    <option value="S">S</option>
                                </select>
                            </div>

                            <label class="form-label fw-semibold text-uppercase small">Longitude (DMS)</label>
                            <div class="input-group mb-3">
                                <input type="text" id="lon_deg" class="form-control" placeholder="°"
                                    value="88">
                                <input type="text" id="lon_min" class="form-control" placeholder="′"
                                    value="18">
                                <input type="text" id="lon_sec" class="form-control" placeholder="″"
                                    value="36.00">
                                <select id="lon_dir" class="form-select input-group-text">
                                    <option value="E" selected>E</option>
                                    <option value="W">W</option>
                                </select>
                            </div>


                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script src="{{ asset('Leaflet_map/leaflet.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Force Leaflet to use local marker icons
            L.Icon.Default.mergeOptions({
                iconUrl: '{{ asset('Leaflet_map/images/marker-icon.png') }}',
                iconRetinaUrl: '{{ asset('Leaflet_map/images/marker-icon-2x.png') }}',
                shadowUrl: '{{ asset('Leaflet_map/images/marker-shadow.png') }}'
            });

            // helper: convert DMS to decimal degrees
            function dmsToDecimal(deg, min, sec, dir) {
                deg = parseFloat(deg) || 0;
                min = parseFloat(min) || 0;
                sec = parseFloat(sec) || 0;
                var dec = Math.abs(deg) + (min / 60) + (sec / 3600);
                if (dir === 'S' || dir === 'W') dec = -dec;
                return dec;
            }

            // read DMS inputs and return [lat, lng]
            function readDmsInputs() {
                var lat_deg = document.getElementById('lat_deg').value;
                var lat_min = document.getElementById('lat_min').value;
                var lat_sec = document.getElementById('lat_sec').value;
                var lat_dir = document.getElementById('lat_dir').value;

                var lon_deg = document.getElementById('lon_deg').value;
                var lon_min = document.getElementById('lon_min').value;
                var lon_sec = document.getElementById('lon_sec').value;
                var lon_dir = document.getElementById('lon_dir').value;

                var lat = dmsToDecimal(lat_deg, lat_min, lat_sec, lat_dir);
                var lng = dmsToDecimal(lon_deg, lon_min, lon_sec, lon_dir);

                return [lat, lng];
            }

            // Show decimal in the dec-output div
            function showDecimal(lat, lng) {
                var out = document.getElementById('dec-output');
                // out.innerHTML = 'Decimal: <strong>' + lat.toFixed(6) + ', ' + lng.toFixed(6) + '</strong>';
            }

            // try to get initial coordinates from DMS inputs (fallback to 0,0)
            var coords = readDmsInputs();
            var initialLat = isFinite(coords[0]) ? coords[0] : 0;
            var initialLng = isFinite(coords[1]) ? coords[1] : 0;

            // Initialize map (no zoom buttons or attribution)
            var map = L.map('map', {
                zoomControl: false,
                attributionControl: false
            }).setView([initialLat, initialLng], 13);

            // Add map tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: ''
            }).addTo(map);

            // explicit icon
            var myIcon = L.icon({
                iconUrl: '{{ asset('Leaflet_map/images/marker-icon.png') }}',
                iconRetinaUrl: '{{ asset('Leaflet_map/images/marker-icon-2x.png') }}',
                shadowUrl: '{{ asset('Leaflet_map/images/marker-shadow.png') }}',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            // add marker
            var marker = L.marker([initialLat, initialLng], {
                icon: myIcon
            }).addTo(map);

            // initial decimal output
            showDecimal(initialLat, initialLng);

            // updateMap reads the DMS fields and moves marker + center
            function updateMap() {
                var newCoords = readDmsInputs();
                var newLat = newCoords[0];
                var newLng = newCoords[1];

                if (!isFinite(newLat) || !isFinite(newLng)) {
                    alert('Please enter valid DMS coordinates.');
                    return;
                }
                marker.setLatLng([newLat, newLng]);
                map.setView([newLat, newLng], 13);
                showDecimal(newLat, newLng);
            }

            // wire inputs to update automatically on change
            var inputs = ['lat_deg', 'lat_min', 'lat_sec', 'lat_dir', 'lon_deg', 'lon_min', 'lon_sec', 'lon_dir'];
            inputs.forEach(function(id) {
                var el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', updateMap);
                    el.addEventListener('input', function() {
                        /*update while typing if desired*/
                    });
                }
            });

            // optional: expose updateMap globally if you want a button to call it elsewhere
            window.updateMap = updateMap;
        });
    </script>
@endpush
