@if (auth()->user() && auth()->user()->hasRole('super_admin'))
    <div class="absolute top-0 left-0 w-full h-full bg-white dark:bg-gray-700 p-4 transition-transform" x-bind:class="!panelOpened ? 'translate-y-full' : 'translate-y-0'">
        <div class="absolute top-4 right-4  cursor-pointer" @click="closePanel">
            <x-carbon-close-large class="w-6 h-6" />
        </div>

        <form class="flex flex-col gap-4">
            <div>
                <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Родительский товар:</label>
                <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model.live="parent_product_id">
                    <option selected>Выберите родительский товар</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Связи параметров:</label>
                @foreach($selected_params as $key=>$selected_param)
                    {{ $selected_params[$key] }}
                @foreach
            </div>
        </form>
        <!-- Live as if you were to die tomorrow. Learn as if you were to live forever. - Mahatma Gandhi -->
    </div>
@endif