@extends('layouts.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5>Edit Project</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ route('projects.update', $project->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Name Field -->
                            <div class="form-group row">
                                <label for="name" class="col-sm-3 col-form-label text-end">Project Name</label>
                                <div class="col-xl-5 col-sm-9">
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $project->name) }}" required>
                                </div>
                            </div>

                            <!-- Description Field -->
                            <div class="form-group row">
                                <label for="description" class="col-sm-3 col-form-label text-end">Description</label>
                                <div class="col-xl-5 col-sm-9">
                                    <textarea class="form-control" id="description" name="description" required>{{ old('description', $project->description) }}</textarea>
                                </div>
                            </div>

                            <!-- Status Field -->
                            <div class="form-group row">
                                <label for="status" class="col-sm-3 col-form-label text-end">Status</label>
                                <div class="col-xl-5 col-sm-9">
                                    <select class="form-control" id="status" name="status">
                                        <option value="planned" {{ $project->status == 'planned' ? 'selected' : '' }}>Planned</option>
                                        <option value="in_progress" {{ $project->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ $project->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Start Date Field -->
                            <div class="form-group row">
                                <label for="start_date" class="col-sm-3 col-form-label text-end">Start Date</label>
                                <div class="col-xl-5 col-sm-9">
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', $project->start_date) }}" required>
                                </div>
                            </div>

                            <!-- End Date Field -->
                            <div class="form-group row">
                                <label for="end_date" class="col-sm-3 col-form-label text-end">End Date</label>
                                <div class="col-xl-5 col-sm-9">
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', $project->end_date) }}" required>
                                </div>
                            </div>

                            <!-- Location (View Only) -->
                            <div class="form-group row">
                                <label for="location" class="col-sm-3 col-form-label text-end">Location</label>
                                <div class="col-xl-5 col-sm-9">
                                    <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $project->location) }}" readonly>
                                </div>
                            </div>

                            <!-- Longitude (View Only) -->
                            <div class="form-group row">
                                <label for="longitude" class="col-sm-3 col-form-label text-end">Longitude</label>
                                <div class="col-xl-5 col-sm-9">
                                    <input type="text" class="form-control" id="longitude" name="longitude" value="{{ old('longitude', $project->longitude) }}" readonly>
                                </div>
                            </div>

                            <!-- Latitude (View Only) -->
                            <div class="form-group row">
                                <label for="latitude" class="col-sm-3 col-form-label text-end">Latitude</label>
                                <div class="col-xl-5 col-sm-9">
                                    <input type="text" class="form-control" id="latitude" name="latitude" value="{{ old('latitude', $project->latitude) }}" readonly>
                                </div>
                            </div>

                            <!-- Map View -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-end">Map View</label>
                                <div class="col-xl-5 col-sm-9">
                                    <div id="map" style="height: 400px;"></div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success">Update Project</button>
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
    var map = L.map('map').setView([{{ $project->latitude }}, {{ $project->longitude }}], 13);

    // Set OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add a marker for the project's location
    L.marker([{{ $project->latitude }}, {{ $project->longitude }}]).addTo(map)
        .bindPopup('<b>{{ $project->location }}</b>')
        .openPopup();
</script>
@endsection
