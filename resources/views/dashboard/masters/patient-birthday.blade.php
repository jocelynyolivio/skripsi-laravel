@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Patient Birthdays']
        ]
    ])
@endsection
@section('container')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Patient Birthdays</h3>
    </div>

    <table id="birthdayTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Action</th>
                <th>Voucher</th>
            </tr>
        </thead>
        <tbody>
            @foreach($patients as $patient)
            <tr>
                <td>{{ $patient->name }}</td>
                <td>
                    <a href="{{ route('dashboard.masters.patients.birthday.generateVoucherBirthday', $patient->id) }}" class="btn btn-warning btn-sm">Generate Voucher Birthday</a>
                    <a href="{{ route('dashboard.masters.patients.birthday.sendVoucherBirthday', $patient->id) }}" class="btn btn-success btn-sm">Send Voucher Birthday via Whatsapp</a>
                </td>
                <td>{{$patient->birthday_voucher_code}} || {{$patient->birthday_voucher_expired_at}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function() {
        $('#birthdayTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
        });
    });

    // Event delegation for SweetAlert confirmation
    $('#holidaysTable').on('click', '.delete-button', function(e) {
        e.preventDefault();
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
</script>
@endsection
