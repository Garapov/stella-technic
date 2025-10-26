<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class AddLastModified
{
    public function handle(Request $request, Closure $next): Response
    {
        // dd($request);
        // 1) Вычисляем метку последнего изменения для этого запроса
        $lastModified = $this->resolveLastModified($request);

        // 2) Если клиент прислал If-Modified-Since и ресурс не менялся — сразу 304
        if ($lastModified) {
            $ifModifiedSince = $request->headers->get('If-Modified-Since');

            if ($ifModifiedSince) {
                // Пробуем распарсить дату клиента
                $ims = \DateTimeImmutable::createFromFormat('D, d M Y H:i:s T', $ifModifiedSince)
                    ?: (function ($v) { try { return new \DateTimeImmutable($v); } catch (\Throwable) { return null; } })($ifModifiedSince);

                if ($ims && $ims >= $lastModified) {
                    return response('', 304)
                        ->setLastModified($lastModified)
                        ->setPublic();
                }
            }
        }

        // 3) Получаем обычный ответ (возможно — из кэша Spatie)
        $response = $next($request);

        // 4) Только для HTML-страниц выставляем заголовок
        if ($lastModified && str_contains($response->headers->get('Content-Type', ''), 'text/html')) {
            $response->setLastModified($lastModified);
            $response->setPublic(); // Разрешаем кэш браузеру/CDN
        }

        return $response;
    }

    /**
     * Определяет дату последнего изменения для текущего запроса.
     * Возвращает \DateTimeInterface|null
     */
    protected function resolveLastModified(Request $request): ?\DateTimeInterface
    {
        // Ограничим методами, которые реально кэшируем
        if (!$request->isMethodCacheable()) {
            return null;
        }

        // Пропустим админку/панели (например, Filament)
        $path = $request->path();
        if (str_starts_with($path, 'admin') || str_starts_with($path, 'filament')) {
            return null;
        }

        $route = $request->route();
        $name  = $route?->getName() ?? '';

        // === Примеры — подстрой под свои имена роутов/моделей ===

        // Страница товара: route('products.show', product)
        if ($name === 'products.show') {
            $product = $route?->parameter('product'); // модель из implicit binding
            if ($product?->updated_at) {
                // Carbon реализует DateTimeInterface — можно вернуть как есть
                return $product->updated_at->toDateTimeImmutable();
            }
        }

        // Список товаров: route('products.index')
        if ($name === 'products.index') {
            // Избегаем тяжёлого max() на каждый реквест — кешируем на минуту
            return Cache::remember('lm:products.index', 60, function () {
                $ts = \App\Models\Product::query()->max('updated_at');
                return $ts ? (new \DateTimeImmutable($ts)) : $this->defaultDeployTime();
            });
        }

        // Новости: show / index — примеры
        if ($name === 'news.show') {
            $post = $route?->parameter('post');
            if ($post?->updated_at) {
                return $post->updated_at->toDateTimeImmutable();
            }
        }
        if ($name === 'news.index') {
            return Cache::remember('lm:news.index', 60, function () {
                $ts = \App\Models\Post::query()->max('updated_at');
                return $ts ? (new \DateTimeImmutable($ts)) : $this->defaultDeployTime();
            });
        }

        // Всё остальное — время деплоя
        return $this->defaultDeployTime();
    }

    /**
     * Универсальная «прошивка» на случай отсутствия специальных правил.
     * Хороший кандидат — время изменения composer.lock
     * (меняется при обновлении зависимостей/деплое).
     */
    protected function defaultDeployTime(): \DateTimeInterface
    {
        $file = base_path('composer.lock');
        $time = @filemtime($file) ?: time();

        // Last-Modified должен быть в UTC (GMT)
        return (new \DateTimeImmutable())->setTimestamp($time)->setTimezone(new \DateTimeZone('UTC'));
    }
}
