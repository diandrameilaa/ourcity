@extends('layouts.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5>Create Project</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ route('projects.store') }}" method="POST">
                            @csrf
                            <div class="form-group row">
                                <label for="name" class="col-sm-3 col-form-label text-end">Project Name</label>
                                <div class="col-xl-5 col-sm-9">
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="description" class="col-sm-3 col-form-label text-end">Description</label>
                                <div class="col-xl-5 col-sm-9">
                                    <textarea class="form-control" id="description" name="description" required></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="location" class="col-sm-3 col-form-label text-end">Location</label>
                                <div class="col-xl-5 col-sm-9">
                                    <input type="text" class="form-control" id="location" name="location" required>
                                    <div id="map" style="height: 400px; width: 100%; margin-top: 20px;"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="status" class="col-sm-3 col-form-label text-end">Status</label>
                                <div class="col-xl-5 col-sm-9">
                                    <select class="form-control" id="status" name="status">
                                        <option value="planned">Planned</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="start_date" class="col-sm-3 col-form-label text-end">Start Date</label>
                                <div class="col-xl-5 col-sm-9">
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="end_date" class="col-sm-3 col-form-label text-end">End Date</label>
                                <div class="col-xl-5 col-sm-9">
                                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="longitude" class="col-sm-3 col-form-label text-end">Longitude</label>
                                <div class="col-xl-5 col-sm-9">
                                    <input type="text" class="form-control" id="longitude" name="longitude" readonly required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="latitude" class="col-sm-3 col-form-label text-end">Latitude</label>
                                <div class="col-xl-5 col-sm-9">
                                    <input type="text" class="form-control" id="latitude" name="latitude" readonly required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Project</button>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- Container-fluid ends-->

    <!-- Leaflet.js and OpenStreetMap script -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

    <script>
        let map;
        let marker;

        function initMap() {
            // Set default location (Surabaya coordinates)
            const defaultLocation = [-7.250445, 112.768845]; // Surabaya coordinates

            // Initialize the map
            map = L.map('map').setView(defaultLocation, 12); // zoom level 12

            // Set OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add a draggable marker
            marker = L.marker(defaultLocation, { draggable: true }).addTo(map);

            // Update latitude and longitude when marker is dragged
            marker.on('dragend', function (event) {
                const position = marker.getLatLng();
                document.getElementById('latitude').value = position.lat;
                document.getElementById('longitude').value = position.lng;
            });

            // Initialize geocoding and set coordinates when an address is selected
            const input = document.getElementById('location');
            const geocoder = new L.Control.Geocoder.Nominatim();

            input.addEventListener('input', function() {
                const query = input.value;
                if (query.length > 3) {
                    geocoder.geocode(query, function(results) {
                        if (results.length > 0) {
                            const latlng = results[0].center;
                            map.setView(latlng, 12); // Move the map to the location
                            marker.setLatLng(latlng); // Move the marker to the new location
                            
                            // Set the latitude and longitude fields
                            document.getElementById('latitude').value = latlng.lat;
                            document.getElementById('longitude').value = latlng.lng;
                        }
                    });
                }
            });
        }

        // Initialize the map when the window is loaded
        window.onload = initMap;
    </script>
@endsection
