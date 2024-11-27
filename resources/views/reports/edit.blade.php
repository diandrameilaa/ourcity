@extends('layouts.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5>Edit Report</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ route('reports.update', $report->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Description Field -->
                            <div class="form-group row">
                                <label for="description" class="col-sm-3 col-form-label text-end">Description</label>
                                <div class="col-xl-5 col-sm-9">
                                    <textarea class="form-control" id="description" name="description" rows="5" required>{{ old('description', $report->description) }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Photo Field -->
                            <div class="form-group row">
                                <label for="photo" class="col-sm-3 col-form-label text-end">Photo</label>
                                <div class="col-xl-5 col-sm-9">
                                    <img src="{{ asset('storage/reports/'.$report->photo) }}" alt="Current Photo" class="img-fluid mt-2" style="max-height: 150px;">
                                    @error('photo')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Location (Read-Only) -->
                            <div class="form-group row">
                                <label for="location" class="col-sm-3 col-form-label text-end">Location</label>
                                <div class="col-xl-5 col-sm-9">
                                    <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $report->location) }}" readonly>
                                </div>
                            </div>

                            <!-- Status Field -->
                            <div class="form-group row">
                                <label for="status" class="col-sm-3 col-form-label text-end">Status</label>
                                <div class="col-xl-5 col-sm-9">
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="diajukan" {{ old('status', $report->status) == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                                        <option value="diproses" {{ old('status', $report->status) == 'diproses' ? 'selected' : '' }}>Diproses</option>
                                        <option value="selesai" {{ old('status', $report->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Longitude (Read-Only) -->
                            <div class="form-group row">
                                <label for="longitude" class="col-sm-3 col-form-label text-end">Longitude</label>
                                <div class="col-xl-5 col-sm-9">
                                    <input type="text" class="form-control" id="longitude" name="longitude" value="{{ old('longitude', $report->longitude) }}" readonly>
                                </div>
                            </div>

                            <!-- Latitude (Read-Only) -->
                            <div class="form-group row">
                                <label for="latitude" class="col-sm-3 col-form-label text-end">Latitude</label>
                                <div class="col-xl-5 col-sm-9">
                                    <input type="text" class="form-control" id="latitude" name="latitude" value="{{ old('latitude', $report->latitude) }}" readonly>
                                </div>
                            </div>

                            <!-- Map View -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-end">Map View</label>
                                <div class="col-xl-5 col-sm-9">
                                    <div id="map" style="height: 400px;"></div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success">Update Report</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add these to the head section of your layout file -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<script>
    // Initialize the map with proper latitude and longitude
    var map = L.map('map').setView([{{ $report->latitude }}, {{ $report->longitude }}], 13);

    // Set OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add a marker for the report's location
    L.marker([{{ $report->latitude }}, {{ $report->longitude }}]).addTo(map)
        .bindPopup('<b>{{ $report->location }}</b>')
        .openPopup();
</script>
@endsection
