@props(['color' => 'stone'])
<span {{ $attributes->merge(['class' => 'badge badge-'.$color]) }}>{{ $slot }}</span>
