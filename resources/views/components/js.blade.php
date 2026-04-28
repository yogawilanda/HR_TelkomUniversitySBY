@props([
    'id_button' => null,
    'class' => null,
])

@if ($class === 'save_form_pop_up' || $class === 'save-form-pop-up')
    <x-js.save_form_pop_up :id_button="$id_button" />
@endif
