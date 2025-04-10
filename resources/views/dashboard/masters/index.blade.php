@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Master Users</h3>
        <a href="{{ route('dashboard.masters.create') }}" class="btn btn-primary mb-3">Add New Users</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <!-- Dropdown Filter -->
    <div class="mb-3">
        <form action="{{ route('dashboard.masters.index') }}" method="GET">
            <label for="roleFilter" class="form-label">Filter by Role</label>
            <select id="roleFilter" name="role" class="form-select" onchange="this.form.submit()">
                <option value="">All Roles</option>
                <option value="1" {{ request('role') == '1' ? 'selected' : '' }}>Admin</option>
                <option value="2" {{ request('role') == '2' ? 'selected' : '' }}>Doctor</option>
                <option value="3" {{ request('role') == '3' ? 'selected' : '' }}>Manager</option>
            </select>
        </form>
    </div>

    <table id="usersTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if ($user->role_id == 1)
                    Admin
                    @elseif ($user->role_id == 2)
                    Doctor
                    @elseif ($user->role_id == 3)
                    Manager
                    @endif
                </td>
                <td>
                    <!-- Tombol Edit -->
                    <a href="{{ route('dashboard.masters.edit', $user->id) }}" class="btn btn-sm btn-warning">Edit</a>

                    <!-- Tombol Delete -->
                    <form action="{{ route('dashboard.masters.destroy', $user->id) }}" method="POST" style="display:inline;" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger delete-button">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            "columnDefs": [{
                    "orderable": false,
                    "targets": 4
                } // Kolom ke-4 adalah kolom Actions
            ]
        });

        // Event delegation for SweetAlert confirmation
        $('#usersTable').on('click', '.delete-button', function(e) {
            e.preventDefault();
            console.log('Delete button clicked');
            var form = $(this).closest('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection