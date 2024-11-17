<x-mail::message>
# Новая заявка с формы "{{ $name }}"

@foreach (json_decode($results) as $result)
**{{ $result->label }}** {{ $result->value }}  
@endforeach

</x-mail::message>
