@extends('dashboard.layouts.main')

@section('container')
<div class="container">
    <h3 class="my-4">All Users</h3>

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
                    <form action="{{ route('dashboard.masters.destroy', $user->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        setTimeout(function() {
            $('#usersTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "responsive": true,
                "columnDefs": [
                    { "orderable": false, "targets": 4 } // Kolom ke-4 adalah kolom Actions
                ]
            });
        }, 100);
    });
</script>
@endsection
