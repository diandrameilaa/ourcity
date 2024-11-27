@extends('layouts.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Report List</h5>
                            <a href="{{ route('reports.create') }}" class="btn btn-success">Add Report</a>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        
                        <!-- Peta untuk Menampilkan Titik Lokasi Laporan -->
                        <div id="map" style="height: 400px; margin-bottom: 20px;"></div>
                        
                        <div class="table-responsive">
                            <table id="reportsTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Pelapor</th>
                                        <th>Deskripsi</th>
                                        <th>Foto</th>
                                        <th>Lokasi</th>
                                        <th>Status</th>
                                        <th>Tanggal Lapor</th>
                                        @if(auth()->check() && auth()->user()->role === 'admin')
                                            <th>Aksi</th>
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
                Are you sure you want to delete this report?
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
    $(document).ready(function() {
        // Mendapatkan peran pengguna dari server
        const userRole = "{{ auth()->check() ? auth()->user()->role : '' }}";

        // Inisialisasi Peta Leaflet
        var map = L.map('map').setView([-6.200000, 106.816666], 8); // Default ke Indonesia

        // Tambahkan Tiles dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Fetch Data Laporan untuk Titik Lokasi
        fetch('{{ route("reports.data") }}')
            .then(response => response.json())
            .then(data => {
                data.data.forEach(report => {
                    if (report.latitude && report.longitude) {
                        // Tambahkan Marker untuk Setiap Laporan di Peta
                        L.marker([parseFloat(report.latitude), parseFloat(report.longitude)])
                            .addTo(map)
                            .bindPopup(`
                                <strong>${report.user_name}</strong><br>
                                ${report.description}<br>
                                <em>Location:</em> ${report.location}<br>
                                <em>Status:</em> ${report.status}
                            `);
                    }
                });
            })
            .catch(error => console.error("Error fetching report data:", error));

        // Konfigurasi kolom DataTable
        var columns = [
            { data: 'id', name: 'id' },
            { data: 'user_name', name: 'user_name' },
            { data: 'description', name: 'description' },
            { data: 'photo', name: 'photo', orderable: false, searchable: false },
            { data: 'location', name: 'location' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at' }
        ];

        // Tambahkan kolom "Aksi" jika peran adalah admin
        if (userRole === 'admin') {
            columns.push({
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `
                        <div class="d-flex justify-content-start gap-2">
                            <a href="${window.location.origin}/reports/${row.id}/edit" class="btn btn-warning btn-sm">Edit</a>
                            <button class="btn btn-danger btn-sm" onclick="showDeleteModal(${row.id})">Delete</button>
                        </div>
                    `;
                }
            });
        }

        // Inisialisasi DataTable
        $('#reportsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("reports.data") }}',
            columns: columns
        });

        // Fungsi untuk menampilkan modal konfirmasi hapus
        window.showDeleteModal = function(reportId) {
            $('#deleteModal').modal('show');
            $('#confirmDeleteBtn').off('click').on('click', function() {
                $.ajax({
                    url: '{{ route("reports.destroy", ":id") }}'.replace(':id', reportId),
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire('Success', 'Report deleted successfully.', 'success');
                        $('#reportsTable').DataTable().ajax.reload();
                        $('#deleteModal').modal('hide');
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to delete report.', 'error');
                    }
                });
            });
        };
    });
</script>
@endpush
