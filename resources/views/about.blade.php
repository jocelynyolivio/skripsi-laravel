@extends('layouts.main')

@section('container')
    <h3>Welcome to {{$title}}</h3>
    <!-- <h4><?php echo $name; ?></h4> -->
     <h3>{{ $name }}</h3>
    <h4><?php echo $email; ?></h4>
    <img src="assets/<?php echo $image; ?>" alt="" width="200">
@endsection