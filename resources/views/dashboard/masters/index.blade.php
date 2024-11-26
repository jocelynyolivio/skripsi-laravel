@extends('dashboard.layouts.main')

@section('container')
<div class="container">
    <h1 class="my-4">
        Master 
        @if ($role_id == 1)
            Admin
        @elseif ($role_id == 2)
            Doctor
        @elseif ($role_id == 3)
            Manager
        @elseif ($role_id == 4)
            Pasien
        @endif
    </h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role ID</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role_id }}</td>
                <td>
                        <!-- Tombol Edit -->
                        <a href="" class="btn btn-sm btn-warning">Edit</a>
                        <!-- Tombol Delete -->
                        <form action="" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">Delete</button>
                        </form>
                    </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
