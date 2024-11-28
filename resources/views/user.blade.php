@extends('layouts.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>User Table</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table id="userTable" class="table">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Action</th>
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

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addForm">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="warga">Warga</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="edit_password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="edit_password_confirmation" name="password_confirmation">
                    </div>
                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Role</label>
                        <select class="form-control" id="edit_role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="warga">Warga</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        let table = $('#userTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('user.data') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'role', name: 'role' },
                { data: 'status', name: 'status'},
                {
                    data: 'id',
                    render: function (data) {
                        return '<button type="button" class="btn btn-primary edit-btn" data-id="' + data + '">Edit</button>';
                    }
                }
            ],
            dom: 'Bfrtip',
            buttons: [
                {
                    text: '+ Tambah Data',
                    action: function (e, dt, node, config) {
                        $('#addModal').modal('show');
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'print',
                    title: '',
                    messageTop: '',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4],
                    }
                }
            ]
        });

    
                // Handle edit button click
        $('#userTable').on('click', '.edit-btn', function () {
            var id = $(this).data('id');
            $.get(`{{ url('user') }}/${id}/edit`, function (data) {
                $('#editModal').modal('show');
                    $('#editForm')[0].reset();
                    $('#editForm').attr('action', `{{ url('user') }}/${id}`);
                    $('#edit_name').val(data.name);
                    $('#edit_email').val(data.email);
                    $('#edit_role').val(data.role);
            });
        });

        $('#addForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '{{ route('user.store') }}',
                data: $(this).serialize(),
                success: function(response) {
                    $('#addModal').modal('hide');
                    table.ajax.reload();
                    $('<div class="alert alert-success alert-dismissible fade show" role="alert">Data added successfully<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>').appendTo('.card-body').delay(3000).fadeOut();
                },
                error: function(error) {
                    console.error(error);
                    alert('Error adding user.');
                }
            });
        });

        $('#editForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'PUT',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function(response) {
                    $('#editModal').modal('hide');
                    table.ajax.reload();
                    $('.alert-success').show().delay(3000).fadeOut();
                },
                error: function(error) {
                    console.error(error);
                    alert('Error updating user.');
                }
            });
        });

        $('#userTable').on('click', '.toggle-status', function() {
            let id = $(this).data('id');
            let status = $(this).hasClass('btn-success') ? 'inactive' : 'active';
            $.ajax({
                url: `{{ url('user/status') }}`,
                method: 'POST',
                data: {
                    id: id,
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function() {
                    table.ajax.reload();
                },
                error: function() {
                    alert('Error updating status');
                }
            });
        });
    });
</script>
@endpush
@endsection
