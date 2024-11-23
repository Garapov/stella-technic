@props(['id'])

@php
    $category = \App\Models\ProductCategory::where('id', $id)->first();
@endphp
<div>

    <li>
        <label class="flex items-center">
            <input checked id="checked-checkbox" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
            <span class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300 whitespace-nowrap">{{ $category->name }}</span>
        </label>
        @if (count($category->categories) > 0)
        
            <ul class="p-2 text-sm text-gray-700 dark:text-gray-200 border-l border-grey-100 ">
                @foreach ($category->categories as $subcategory)
                    <x-category-list-item  id="{{ $subcategory->id }}" />
                @endforeach
                
            </ul>
        @endif
        {{-- {{ $category }} --}}
    </li>
    {{-- {{ $id }} --}}
    <!-- You must be the change you wish to see in the world. - Mahatma Gandhi -->
</div>