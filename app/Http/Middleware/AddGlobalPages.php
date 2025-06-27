<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

use App\Models\Page;
use Illuminate\Support\Facades\View;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;

class AddGlobalPages
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $pages = new Collection();

        // $pages->politics = Page::find(setting("politics"));
        // if ($pages->politics) {
        //     $pages->politics->url = FilamentFabricator::getPageUrlFromId(
        //         $pages->politics->id
        //     );
        // }

        // $pages->cookies = Page::find(setting("cookies"));
        // if ($pages->cookies) {
        //     $pages->cookies->url = FilamentFabricator::getPageUrlFromId(
        //         $pages->cookies->id
        //     );
        // }

        // Добавление переменной в глобальное пространство имен Blade
        View::share("globalPages", $pages);

        // dd($pages);

        return $next($request);
    }
}
