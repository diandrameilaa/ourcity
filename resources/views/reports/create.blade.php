@extends('layouts.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5>Create Report</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group row">
                                <label for="description" class="col-sm-3 col-form-label text-end">Description</label>
                                <div class="col-xl-5 col-sm-9">
                                    <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="5" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="photo" class="col-sm-3 col-form-label text-end">Photo</label>
                                <div class="col-xl-5 col-sm-9">
                                    <input id="photo" type="file" class="form-control @error('photo') is-invalid @enderror" name="photo">
                                    @error('photo')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="location" class="col-sm-3 col-form-label text-end">Location</label>
                                <div class="col-xl-5 col-sm-9">
                                    <input id="location" type="text" class="form-control @error('location') is-invalid @enderror" name="location" value="{{ old('location') }}" required>
                                    @error('location')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                    <div id="map" style="height: 400px; width: 100%; margin-top: 20px;"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="longitude" class="col-sm-3 col-form-label text-end">Longitude</label>
                                <div class="col-xl-5 col-sm-9">
                                    <input id="longitude" type="text" class="form-control @error('longitude') is-invalid @enderror" name="longitude" readonly required>
                                    @error('longitude')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="latitude" class="col-sm-3 col-form-label text-end">Latitude</label>
                                <div class="col-xl-5 col-sm-9">
                                    <input id="latitude" type="text" class="form-control @error('latitude') is-invalid @enderror" name="latitude" readonly required>
                                    @error('latitude')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Create Report</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
