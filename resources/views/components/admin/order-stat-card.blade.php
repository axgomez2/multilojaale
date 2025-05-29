@props(['color', 'icon', 'label', 'value'])

<div class="rounded-xl bg-{{ $color }}-600 text-white p-4 shadow flex justify-between items-center">
    <div>
        <h5 class="text-2xl font-bold">{{ $value }}</h5>
        <div>{{ $label }}</div>
    </div>
    <div>
        <i class="fas fa-{{ $icon }} text-3xl opacity-75"></i>
    </div>
</div>
