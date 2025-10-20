<div>
    @if (count($products) > 0)
        <section class="xl:px-[100px] px-[20px]">
            <x-products-slider :variations="$products" title="Популярные товары" />
        </section>
        
    @endif
</div>