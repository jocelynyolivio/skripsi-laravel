@php
    // Default breadcrumbs if not overridden
    $breadcrumbs = [
        ['url' => route('dashboard'), 'text' => 'Dashboard'],
    ];
    
    if (isset($customBreadcrumbs)) {
        $breadcrumbs = array_merge($breadcrumbs, $customBreadcrumbs);
    }
@endphp

@foreach ($breadcrumbs as $breadcrumb)
    <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
        @if (!$loop->last && isset($breadcrumb['url']))
            <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['text'] }}</a>
        @else
            {{ $breadcrumb['text'] }}
        @endif
    </li>
@endforeach