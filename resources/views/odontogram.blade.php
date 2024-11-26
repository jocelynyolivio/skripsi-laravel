@extends('layouts.main')

@section('content')
<div class="container mt-5">
    <h3>Odontogram</h3>
    <!-- Konten Odontogram -->
    <div class="odontogram">
        <!-- Gigi Atas -->
        <div class="row">
            @for ($i = 1; $i <= 8; $i++)
                <div class="col-3">
                    <div class="tooth healthy" data-id="{{ $i }}">
                        Gigi Atas {{ $i }}
                    </div>
                </div>
            @endfor
        </div>

        <!-- Gigi Bawah -->
        <div class="row mt-4">
            @for ($i = 9; $i <= 16; $i++)
                <div class="col-3">
                    <div class="tooth healthy" data-id="{{ $i }}">
                        Gigi Bawah {{ $i }}
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .odontogram {
        text-align: center;
    }

    .tooth {
        background-color: #fff;
        border: 2px solid #000;
        border-radius: 50%;
        padding: 20px;
        margin: 10px;
        cursor: pointer;
        font-size: 14px;
        font-weight: bold;
    }

    .healthy {
        background-color: green;
        color: white;
    }

    .cavity {
        background-color: red;
    }

    .extracted {
        background-color: gray;
    }

    .row {
        display: flex;
        justify-content: center;
    }

    .col-3 {
        display: flex;
        justify-content: center;
    }
</style>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.tooth').forEach(function (tooth) {
        tooth.addEventListener('click', function () {
            let status = prompt('Enter status for this tooth (healthy, cavity, extracted):');
            if (status) {
                tooth.classList.remove('healthy', 'cavity', 'extracted');
                tooth.classList.add(status);
            }
        });
    });
</script>
@endsection
