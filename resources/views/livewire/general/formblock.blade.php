<div>
    <div class="lg:w-1/2 md:w-2/3 mx-auto" x-data="{
        back_fields: {{ json_encode($form->fields) }},
        front_fields: [],
        init() {
            
            this.back_fields.map(field => {
                this.front_fields.push(JSON.parse(JSON.stringify({...field, value: ''})));
                return field;
            })
            console.table(this.front_fields)

        },
        submit() {
            $wire.submit(JSON.stringify(this.front_fields))
        }
    }">
        <form @submit.prevent="submit" class="flex flex-wrap -m-2">
            <template x-for="(field, key) in front_fields">
                <div class="p-2 w-full">
                    <label for="name" class="leading-7 text-sm text-gray-600 dark:text-gray-200" x-text="field.type"></label>
                    <template x-if="field.type == 'text'">
                        <input type="text" x-model="field.value" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                    </template>
                    <template x-if="field.type == 'textarea'">
                        <textarea x-model="field.value" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out"></textarea>
                    </template>
                </div>
            </template>
            <button class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">Button</button>
        </form>
    </div>

</div>
