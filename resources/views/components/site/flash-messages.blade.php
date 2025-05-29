@if(session('success'))
    <x-site.toast type="success" message="{{ session('success') }}" />
@endif

@if(session('error'))
    <x-site.toast type="error" message="{{ session('error') }}" />
@endif

@if(session('warning'))
    <x-site.toast type="warning" message="{{ session('warning') }}" />
@endif

@if(session('info'))
    <x-site.toast type="info" message="{{ session('info') }}" />
@endif

@if($errors->any())
    @foreach($errors->all() as $error)
        <x-site.toast type="error" message="{{ $error }}" />
    @endforeach
@endif
