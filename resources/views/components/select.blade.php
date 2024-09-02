@props([
    'name',
    'options' => [],
    'selected' => [],
    'disabled' => false,
    'multiple' => false,
    'placeholder' => '',
])

<select
    name="{{ $name }}{{ $multiple ? '[]' : '' }}"
    {!! $disabled ? 'disabled' : '' !!}
    {!! $attributes->merge([
        'class' => 'form-control border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full',
        'multiple' => $multiple ? 'multiple' : null,
        'placeholder' => $placeholder
    ]) !!}
>
    @if (!$multiple)
        <option value="" disabled selected>{{ $placeholder }}</option>
    @endif
    @foreach ($options as $value => $label)
        <option value="{{ $value }}" {{ in_array($value, (array)$selected) ? 'selected' : '' }}>
            {{ $label }}
        </option>
    @endforeach
</select>