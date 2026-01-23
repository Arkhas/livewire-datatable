@props(['table', 'filters'])

@foreach($table->getFilters() as $filter)
    @php
        $filterType = $filter->type;
    @endphp

    @if($filterType === 'date' || $filterType === 'range')
        {{-- Date/Range Filter with Flux Date Picker --}}
        <x-livewire-datatable::partials.date-filter :filter="$filter" :filters="$filters" />
    @else
        {{-- Dropdown Filter --}}
        <x-livewire-datatable::partials.dropdown-filter :filter="$filter" :filters="$filters" />
    @endif
@endforeach
