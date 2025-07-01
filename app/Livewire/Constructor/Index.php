<?php

namespace App\Livewire\Constructor;

use App\Models\Product;
use App\Models\ProductParamItem;
use App\Models\ProductVariant;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Livewire\Attributes\Url;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Log;

class Index extends Component implements HasForms
{
    use InteractsWithForms;

    #[Url()]
    public $variation_id;
    public $variation;
    public $added_rows = [];
    public $parent_product_id = null;
    public $selected_params = [];
    public $embeded = false;
    public $selectedWidth = 'slim';
    public $selectedHeight = 'low';
    public $selectedDeskType = 'Односторонняя';
    public $selectedPosition = 'on_floor';
    public ?array $data = [];

    public function mount()
    {
        $this->form->fill();
        if ($this->variation_id) {
            $this->variation = ProductVariant::where('id', $this->variation_id)->first();
            if ($this->variation && $this->variation->is_constructable && $this->variation->constructor_type == 'deck' && $this->variation->rows) {
                $this->added_rows = $this->variation->rows;
                $this->parent_product_id = $this->variation->product->id;


                if ($this->variation->selected_width) $this->selectedWidth = $this->variation->selected_width;
                if ($this->variation->selected_height) $this->selectedHeight = $this->variation->selected_height;
                if ($this->variation->selected_desk_type) $this->selectedDeskType = $this->variation->selected_desk_type;
                if ($this->variation->selected_position) $this->selectedPosition = $this->variation->selected_position;
            }
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make("product_id")
                    ->searchable()
                    ->preload()
                    ->label('Родительский товар')
                    ->required()
                    ->default(1841)
                    ->options(
                        fn() => Product::all()->pluck("name", "id")
                    ),
                TextInput::make("name")
                    ->required()
                    ->default('Стойка 735х1500 с ящиками Стелла-техник')
                    ->label("Название"),
                TextInput::make("price")
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->postfix("₽")
                    ->label("Цена"),
                TextInput::make("sku")
                    ->required()
                    ->default('А1-00-00-00')
                    ->label(
                        "Артикул"
                    ),
                TextInput::make("count")
                    ->required()
                    ->numeric()
                    ->default(1000)
                    ->label("Остаток"),
                Repeater::make("row")
                    ->label(false)
                    ->reorderable(false)
                    ->addActionLabel("Добавить связь")
                    ->grid(1)
                    ->defaultItems(4)
                    ->minItems(2)
                    ->maxItems(6)
                    ->label("Ключевые параметры")
                    ->required()
                    ->simple(
                        Select::make(
                            "parametrs"
                        )
                            ->distinct()
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->options(function () {
                                return ProductParamItem::query()
                                    ->with("productParam")
                                    ->get()
                                    ->mapWithKeys(function (
                                        $item
                                    ) {
                                        $paramName = $item->productParam
                                            ? $item
                                                ->productParam
                                                ->name
                                            : "Unknown";
                                        $name = "$paramName: $item->title";

                                        return [
                                            $item->id => $name,
                                        ];
                                    });
                            })
                    ),
                Select::make("parametrs")
                    ->multiple()
                    ->preload()
                    ->label('Второстепенные параметры')
                    ->required()
                    ->options(function () {
                        return ProductParamItem::query()
                            ->with("productParam")
                            ->get()
                            ->mapWithKeys(function ($item) {
                                $paramName = $item->productParam
                                    ? $item->productParam->name
                                    : "Unknown";
                                $name = "$paramName: $item->title";
                                return [$item->id => $name];
                            });
                    })
                    ->columnSpanFull(),
                Hidden::make('constructor_type')->default('deck')
            ])
            ->statePath('data');
    }

    public function createVariation(): void
    {


        $parentProduct = Product::find($this->data['product_id']);

        if (!$parentProduct) return;

        

        // Check if the link already exists
        $linkExists = false;
        $row = [];
        $links_field = "";

        foreach($this->data['row'] as $key => $rowItem) {
            $row[] = intval($this->data['row']["$key"]['parametrs']);
            $links_field .= intval($this->data['row']["$key"]['parametrs']);
        }


        // dd($parent_product->links);
        foreach ($parentProduct->links as $link) {
            if (
                isset($link["row"]) &&
                $link["row"] === $row
            ) {
                $linkExists = true;
                break;
            }
        }


        if ($linkExists) return;

        if (!$linkExists) {
            $newLink = [
                "row" => $row,
            ];
            $links = $parentProduct->links;
            $links[] = $newLink;

            $parentProduct->links = $links;
            // dd($parent_product->links);
            $parentProduct->save();
        }

        

        unset($this->data['row']);

        

        $this->data['gallery'][] = '/assets/placeholder.svg';
        $this->data['is_constructable'] = true;
        $this->data['rows'] = $this->added_rows;
        $this->data['selected_width'] = $this->selectedWidth;
        $this->data['selected_height'] = $this->selectedHeight;
        $this->data['selected_desk_type'] = $this->selectedDeskType;
        $this->data['selected_position'] = $this->selectedPosition;
        $this->data['links'] = $links_field;


        $variation = ProductVariant::create($this->data);
        

        $variation->paramItems()->sync($row);
        $variation->parametrs()->sync($this->data['parametrs']);



        $this->redirect(route('client.catalog', $variation->urlChain()));
    }
    

    public function render()
    {
        return view("livewire.constructor.index", [
            'added_rows' => $this->added_rows,
            'embeded' => $this->embeded,
            'products' => Product::all(),
            'param_items' => ProductParamItem::all()
        ]);
    }
}
