@extends('dashboard.layouts.main')

@section('container')
<h1>Home Contents</h1>
<!-- Display additional data above the table -->
<div class="mb-3">
    <h3>Welcome Title:</h3>
    <p>{{ $contents->first()->welcome_title ?? 'No welcome title available' }}</p>

    <h3>Welcome Message:</h3>
    <p>{{ $contents->first()->welcome_message ?? 'No welcome message available' }}</p>

    <h3>About Text:</h3>
    <p>{{ $contents->first()->about_text ?? 'No about text available' }}</p>


    <h3>Services Text:</h3>
    <p>{{ $contents->first()->services_text ?? 'No services text available' }}</p>
</div>
<a href="{{ route('dashboard.home_content.create') }}" class="btn btn-primary mb-3">Add New Content</a>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Carousel Image</th>
            <th>Carousel Text</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($contents as $content)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td><img src="{{ asset('storage/' . $content->carousel_image) }}" alt="Carousel Image" width="100"></td>
            <td>{{ $content->carousel_text }}</td>
            <td>
                <a href="{{ route('dashboard.home_content.edit', $content->id) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('dashboard.home_content.destroy', $content->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
