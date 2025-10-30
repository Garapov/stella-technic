<div>
    @if (count($this->products) > 0)
        <section class="xl:px-[100px] px-[20px]">
            <x-products-slider :variations="$this->products" title="Популярные товары" />
        </section>
    @endif
</div>