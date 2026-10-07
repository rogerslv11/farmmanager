@extends('layouts.app')
@section('title', $title ?? 'FarmManager')
@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
  <div>
    <h1 class="text-2xl font-bold text-stone-900">{{ $title ?? '' }}</h1>
    @if(!empty($subtitle))<p class="text-sm text-stone-500">{{ $subtitle }}</p>@endif
  </div>
  @if(!empty($action))<div>{!! $action !!}</div>@endif
</div>
{{ $slot }}
@endsection
