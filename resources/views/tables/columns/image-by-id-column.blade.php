<div class="px-3 py-4">
    @php
      $image = \App\Models\Image::find($getState())
    @endphp
    <img  src="{{ asset('/storage/' . $image->uuid . '/filament-thumbnail.' . $image->file_extension) }}" alt="" class="w-full max-w-[14rem] rounded-lg">
</div>
