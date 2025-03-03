@extends('layouts.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Project Locations</h5>
                        @if(auth()->check() && auth()->user()->role === 'admin')
                            <a href="{{ route('projects.create') }}" class="btn btn-success">Add Project</a>
                        @endif
                    </div>

                    <div class="card-body">
                        <!-- Map Container -->
                        <div id="map" style="height: 400px; margin-bottom: 20px;"></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5>Project List</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table id="projectsTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Longitude</th>
                                        <th>Latitude</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        @if(auth()->check() && auth()->user()->role === 'admin')
                                            <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this project?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Mendapatkan peran pengguna dari server
    const userRole = "{{ auth()->check() ? auth()->user()->role : '' }}";

    // Inisialisasi Peta Leaflet
    var map = L.map('map').setView([-6.200000, 106.816666], 8);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    fetch('{{ route("projects.data") }}')
        .then(response => response.json())
        .then(data => {
            data.data.forEach(project => {
                if (project.latitude && project.longitude) {
                    L.marker([parseFloat(project.latitude), parseFloat(project.longitude)]).addTo(map)
                        .bindPopup(`
                            <strong>${project.name}</strong><br>
                            ${project.description}<br>
                            <em>Location:</em> ${project.location}
                        `);
                }
            });
        })
        .catch(error => console.error("Error fetching project data:", error));

        // memasukkan add project agar bisa ke tabel admin dan warga
    // Inisialisasi DataTable
    $(document).ready(function() {
        var columns = [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'description', name: 'description' },
            { data: 'location', name: 'location' },
            { data: 'status', name: 'status' },
            { data: 'longitude', name: 'longitude' },
            { data: 'latitude', name: 'latitude' },
            { data: 'start_date', name: 'start_date' },
            { data: 'end_date', name: 'end_date' }
        ];

        // Tambahkan kolom "Action" jika pengguna adalah admin
        if (userRole === 'admin') {
            columns.push({
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `
                        <div class="d-flex justify-content-start gap-2">
                            <a href="${window.location.origin}/projects/${row.id}/edit" class="btn btn-warning btn-sm">Edit</a>
                            <button class="btn btn-danger btn-sm" onclick="showDeleteModal(${row.id})">Delete</button>
                        </div>
                    `;
                }
            });
        }

        $('#projectsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("projects.data") }}',
            columns: columns
        });

        window.showDeleteModal = function(projectId) {
            $('#deleteModal').modal('show');
            $('#confirmDeleteBtn').off('click').on('click', function() {
                $.ajax({
                    url: '{{ route("projects.destroy", ":id") }}'.replace(':id', projectId),
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire('Success', 'Project deleted successfully.', 'success');
                        $('#projectsTable').DataTable().ajax.reload();
                        $('#deleteModal').modal('hide');
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to delete project.', 'error');
                    }
                });
            });
        }
    });
</script>
@endpush
