@aware(['page'])

@forelse($seo as $seo_tag)
    @foreach($seo_tag['data'] as $key => $tag)
        
        @if ($key == 'image')
            @seo(['image' => Storage::disk(config('filesystems.default'))->url($tag)])
        @else
            @seo([$key => $tag])
        @endif
    @endforeach
    
@empty
    {{--@seo(['title' => $name])--}}
@endforelse
