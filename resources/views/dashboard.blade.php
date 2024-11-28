@extends('layouts.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="row">
            <!-- Statistik Laporan -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Statistik Laporan</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="reportChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Statistik Proyek -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Statistik Proyek</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="projectChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <!-- Analitik Aktivitas Pengguna -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Analitik Aktivitas Pengguna</h5>
                    </div>
                    <div class="card-body">
                        <p>Total Laporan: {{ $userActivityStats['total_reports'] }}</p>
                        <p>Laporan Mingguan: {{ $userActivityStats['weekly_reports'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Laporan Aktivitas -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Laporan Aktivitas</h5>
                    </div>
                    <div class="card-body">
                        <p>Laporan Mingguan: {{ $activityReports['weekly_reports'] }}</p>
                        <p>Proyek Baru Mingguan: {{ $activityReports['weekly_projects'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <!-- Peta dengan Markers untuk Laporan dan Proyek -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Peta Lokasi Laporan dan Proyek</h5>
                    </div>
                    <div class="card-body">
                        <div id="map" style="height: 400px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    // Statistik Laporan
    const reportStats = @json($reportStats);
    const reportChart = new Chart(document.getElementById('reportChart'), {
        type: 'doughnut',
        data: {
            labels: ['Diajukan', 'Diproses', 'Selesai'],
            datasets: [{
                data: [reportStats.diajukan, reportStats.diproses, reportStats.selesai],
                backgroundColor: ['#FF6384', '#36A2EB', '#4BC0C0'],
            }]
        },
    });

    // Statistik Proyek
    const projectStats = @json($projectStats);
    const projectChart = new Chart(document.getElementById('projectChart'), {
        type: 'bar',
        data: {
            labels: ['Planned', 'In Progress', 'Completed'],
            datasets: [{
                label: 'Jumlah Proyek',
                data: [projectStats.planned, projectStats.in_progress, projectStats.completed],
                backgroundColor: ['#FFCE56', '#FF6384', '#36A2EB'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Statistik Proyek' }
            }
        },
    });


// Inisialisasi Peta Leaflet
var map = L.map('map').setView([-6.200000, 106.816666], 8); // Default ke Indonesia

// Tambahkan Tiles dari OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Fetch Data Proyek untuk Ditampilkan di Peta
fetch('{{ route("projects.data") }}')
    .then(response => response.json())
    .then(projectData => {
        console.log("Project Data:", projectData); // Debugging
        projectData.data.forEach(project => {
            if (project.latitude && project.longitude) {
                // Tambahkan Marker Biru untuk Proyek
                L.marker([parseFloat(project.latitude), parseFloat(project.longitude)]).addTo(map)
                        .bindPopup(`
                            <strong>${project.name}</strong><br>
                            ${project.description}<br>
                            <em>Location:</em> ${project.location}
                        `);
            } else {
                console.warn(`Missing location for project: ${project.name}`);
            }
        });
    })
    .catch(error => console.error("Error fetching project data:", error));

// Fetch Data Laporan untuk Ditampilkan di Peta
fetch('{{ route("reports.data") }}')
    .then(response => response.json())
    .then(reportData => {
        console.log("Report Data:", reportData); // Debugging
        reportData.data.forEach(report => {
            if (report.latitude && report.longitude) {
                // Tambahkan Marker Merah untuk Laporan
                L.marker([parseFloat(report.latitude), parseFloat(report.longitude)])
                        .addTo(map)
                        .bindPopup(`
                            <strong>${report.user_name}</strong><br>
                            ${report.description}<br>
                            <em>Location:</em> ${report.location}<br>
                            <em>Status:</em> ${report.status}
                        `);
            } else {
                console.warn(`Missing location for report: ${report.name}`);
            }
        });
    })
    .catch(error => console.error("Error fetching report data:", error));
</script>
@endsection
