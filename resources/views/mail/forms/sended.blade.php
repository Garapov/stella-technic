<x-mail::message>
@foreach (json_decode($results) as $result)
**{{ $result->label }}** {{ $result->value }}  
@endforeach
</x-mail::message>
