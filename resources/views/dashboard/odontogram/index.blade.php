@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h2 class="text-center">Odontogram</h2>
    <p class="text-center text-muted">Tampilan struktur gigi pasien</p>

    <style>
        .odontogram {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .odontogram .row {
            display: flex !important;
            justify-content: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .odontogram .tooth {
            text-align: center;
        }

        .odontogram .tooth-number {
            font-size: 14px;
            margin-bottom: 5px;
        }

        .odontogram .tooth-box {
            width: 40px;
            height: 40px;
            border: 1px solid #000;
            background-color: #f8f9fa;
        }
    </style>

    <div class="odontogram">
        <!-- Rahang Atas -->
        <div class="row mb-4">
            @for ($i = 18; $i >= 11; $i--)
                <div class="tooth">
                    <div class="tooth-number">{{ $i }}</div>
                    <div class="tooth-box"></div>
                </div>
            @endfor
            @for ($i = 21; $i <= 28; $i++)
                <div class="tooth">
                    <div class="tooth-number">{{ $i }}</div>
                    <div class="tooth-box"></div>
                </div>
            @endfor
        </div>

        <!-- Rahang Bawah -->
        <div class="row">
            @for ($i = 48; $i >= 41; $i--)
                <div class="tooth">
                    <div class="tooth-number">{{ $i }}</div>
                    <div class="tooth-box"></div>
                </div>
            @endfor
            @for ($i = 31; $i <= 38; $i++)
                <div class="tooth">
                    <div class="tooth-number">{{ $i }}</div>
                    <div class="tooth-box"></div>
                </div>
            @endfor
        </div>
    </div>
</div>
@endsection
