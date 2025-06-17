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
    public $addedRows = [];
    public $parent_product_id = null;
    public $selected_params = [];
    public ?array $data = [];

    public function mount()
    {
        $this->form->fill();
        if ($this->variation_id) {
            $this->variation = ProductVariant::where('id', $this->variation_id)->first();
            if ($this->variation && $this->variation->is_constructable && $this->variation->constructor_type == 'deck' && $this->variation->rows) {
                $this->addedRows = $this->variation->rows;
                $this->parent_product_id = $this->variation->product->id;
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
                    ->options(
                        fn() => Product::all()->pluck("name", "id")
                    ),
                TextInput::make("name")
                    ->required()
                    ->label("Название"),
                TextInput::make("price")
                    ->required()
                    ->numeric()
                    ->postfix("₽")
                    ->label("Цена"),
                TextInput::make("sku")->label(
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
                    ->defaultItems(2)
                    ->minItems(2)
                    ->maxItems(6)
                    ->label("Ключевые параметры")
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

        // dd($parent_product->links);
        foreach ($parentProduct->links as $link) {
            if (
                isset($link["row"]) &&
                $link["row"] === $this->data['row']
            ) {
                $linkExists = true;
                break;
            }
        }

        if ($linkExists) return;

        if (!$linkExists) {
            $newLink = [
                "row" => $this->data['row'],
            ];
            $links = $parentProduct->links;
            $links[] = $newLink;

            $parentProduct->links = $links;
            // dd($parent_product->links);
            $parentProduct->save();
        }

        unset($this->data['row']);

        $this->data['gallery'][] = '/assets/placeholder.svg';


        $variation = ProductVariant::create($this->data);


        Log::info(['Создана вариация в конструкторе', $variation]);

        $this->redirect(route('client.product_detail', $variation->slug));
    }
    

    public function render()
    {
        return view("livewire.constructor.index", [
            'added_rows' => $this->addedRows,
            'embeded' => false,
            'products' => Product::all(),
            'param_items' => ProductParamItem::all()
        ]);
    }
}
