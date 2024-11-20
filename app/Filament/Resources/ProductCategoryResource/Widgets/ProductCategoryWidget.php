<?php

namespace App\Filament\Resources\ProductCategoryResource\Widgets;

use App\Models\ProductCategory;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Guava\FilamentIconPicker\Forms\IconPicker;
use SolutionForest\FilamentTree\Actions\Action;
use SolutionForest\FilamentTree\Actions\ActionGroup;
use SolutionForest\FilamentTree\Actions\DeleteAction;
use SolutionForest\FilamentTree\Actions\EditAction;
use SolutionForest\FilamentTree\Actions\ViewAction;
use SolutionForest\FilamentTree\Widgets\Tree as BaseWidget;

class ProductCategoryWidget extends BaseWidget
{
    protected static string $model = ProductCategory::class;

    protected static int $maxDepth = 10;

    protected ?string $treeTitle = 'ProductCategoryWidget';

    protected bool $enableTreeTitle = true;

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('title')
                ->label('Заголовок')
                ->required(),
            Toggle::make('is_visible')
                ->inline(false)
                ->label('Видимость'),
            Textarea::make('description')
                ->label('Описание')
                ->required(),
            IconPicker::make('icon')
                ->required(),
        ];
    }

    // INFOLIST, CAN DELETE
    public function getViewFormSchema(): array {
        return [
            //
        ];
    }

    // CUSTOMIZE ICON OF EACH RECORD, CAN DELETE
    // public function getTreeRecordIcon(?\Illuminate\Database\Eloquent\Model $record = null): ?string
    // {
    //     return null;
    // }

    // CUSTOMIZE ACTION OF EACH RECORD, CAN DELETE 
    // protected function getTreeActions(): array
    // {
    //     return [
            // Action::make('helloWorld')
            //     ->action(function () {
            //         Notification::make()->success()->title('Hello World')->send();
            //     }),
            // ViewAction::make(),
            // EditAction::make(),
    //         ActionGroup::make([
                
    //             ViewAction::make(),
    //             EditAction::make(),
    //         ]),
    //         DeleteAction::make(),
    //     ];
    // }
    // OR OVERRIDE FOLLOWING METHODS
    protected function hasDeleteAction(): bool
    {
       return true;
    }
    protected function hasEditAction(): bool
    {
       return true;
    }
    // protected function hasViewAction(): bool
    // {
    //    return true;
    // }
}