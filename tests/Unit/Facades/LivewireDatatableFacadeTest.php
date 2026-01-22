<?php

use Arkhas\LivewireDatatable\Facades\LivewireDatatable;

test('it has facade accessor', function () {
    // The facade should return the accessor name
    $reflection = new \ReflectionClass(LivewireDatatable::class);
    $method = $reflection->getMethod('getFacadeAccessor');
    $method->setAccessible(true);
    
    $accessor = $method->invoke(null);
    
    expect($accessor)->toBe('livewire-datatable');
});
