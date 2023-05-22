@php
$values = (array)$values;
@endphp
@if (sizeof($values) > 1) <div class="mt-checkbox-list"> @endif
    @foreach ($values as $value)
    @php
        $name = $value[0] ?? '';
        $currentValue = $value[1] ?? '';
        $label = $value[2] ?? '';
        $selected = $value[3] ?? false;
        $disabled = $value[4] ?? false;
    @endphp
    <label class="mb-2">
        <input type="checkbox"
               value="{{ $currentValue }}"
               {{ $selected ? 'checked' : '' }}
               name="{{ $name }}" {{ $disabled ? 'disabled' : '' }}>
        {!! BaseHelper::clean($label) !!}
    </label>
@endforeach
@if (sizeof($values) > 1) </div> @endif
