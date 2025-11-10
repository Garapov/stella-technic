<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

use function PHPUnit\Framework\isArray;

class ConvertImagesToWebp extends Command
{
    protected $signature = 'images:convert-webp
        {--model=App\\Models\\ProductVariant : Полное имя модели}
        {--field=gallery : Поле, содержащее ссылки на изображения}
        {--delete_original=true : Маркер удаления оригиналов после конверсии}
        {--item_ids= : массив ID через запятую для обработки только указанных записей}
        {--disk=tws3 : Диск хранения (например: s3, public)}';

    protected $description = 'Конвертирует изображения в WebP с заданного диска и обновляет ссылки в указанной модели (удаляет оригиналы по умолчанию)';

    public function handle(): void
    {
        $modelClass = $this->option('model');
        $field = $this->option('field');
        $disk = $this->option('disk');
        $item_ids = $this->option('item_ids');
        $deleteOriginal = $this->option('delete_original');

        // dd($deleteOriginal);

        if (!class_exists($modelClass)) {
            $this->error("Модель {$modelClass} не существует.");
            return;
        }

        $this->info('📦 Начинаем обработку:');
        $this->line("Модель: {$modelClass}");
        $this->line("Поле: {$field}");
        $this->line("Диск: {$disk}");
        $this->line("Удалять оригиналы: " . ($deleteOriginal !== 'false' ? 'Да' : 'Нет'));

        $query = $modelClass::query();

        // Проверяем, есть ли SoftDeletes
        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($modelClass))) {
            $query->withoutTrashed();
        }
        // dd($item_ids);
        if ($item_ids) {
            $query->whereIn('id', [$item_ids]);
            $this->line("Фильтр по ID: [$item_ids]");
        }

        $items = $query->whereNotNull($field)->get();


        $this->line("Количество записей: {$items->count()}");

        Log::info("=== Запуск конверсии изображений в WebP ===", [
            'model' => $modelClass,
            'field' => $field,
            'disk' => $disk,
            'total' => $items->count(),
        ]);

        $successCount = 0;
        $skipCount = 0;
        $errorCount = 0;

        $imageManager = new ImageManager(new Driver());

        foreach ($items as $itemIndex => $item) {
            $this->line('🔹 Обработка #' . $item->id . ' (' . ($itemIndex + 1) . '/' . $items->count() . ')');

            $originalValue = $item->{$field};
            $gallery = $originalValue;

            // Если поле хранится как JSON-строка — декодируем
            if (is_string($gallery)) {
                $decoded = json_decode($gallery, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $gallery = $decoded;
                } else {
                    // Если это просто строка с одной ссылкой — оборачиваем в массив
                    $gallery = [$gallery];
                }
            }

            if (!is_array($gallery) || empty($gallery)) {
                $this->warn("⚠️ Пропущено: {$modelClass} #{$item->id} — поле '{$field}' пустое или не массив.");
                Log::warning("Поле '{$field}' пустое или не массив", ['id' => $item->id]);
                $skipCount++;
                continue;
            }

            $newGallery = [];

            foreach ($gallery as $path) {
                try {
                    $path = ltrim($path, '/');

                    // Если файл уже .webp, пропускаем
                    if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'webp') {
                        $newGallery[] = $path;
                        $this->line("   ℹ️ Уже .webp — пропускаем: {$path}");
                        $skipCount++;
                        continue;
                    }

                    // Проверка существования оригинала
                    $variants = [$path, "public/{$path}", "uploads/{$path}"];
                    $exists = false;
                    $foundWebpInstead = false;

                    foreach ($variants as $variant) {
                        if (Storage::disk($disk)->exists($variant)) {
                            $path = $variant;
                            $exists = true;
                            break;
                        }
                    }

                    // Если оригинал не найден, ищем уже существующий .webp
                    $webpVariants = [];
                    if (!$exists) {
                        foreach ($variants as $variant) {
                            $ext = pathinfo($variant, PATHINFO_EXTENSION);
                            $webpVariants[] = preg_replace('/\.' . preg_quote($ext, '/') . '$/', '.webp', $variant);
                        }

                        foreach ($webpVariants as $variant) {
                            if (Storage::disk($disk)->exists($variant)) {
                                $path = $variant;
                                $exists = true;
                                $foundWebpInstead = true; // нашли уже существующий webp
                                $this->line("   ℹ️ Оригинал не найден, но найден уже конвертированный .webp: {$path}");
                                break;
                            }
                        }
                    }

                    if (!$exists) {
                        Log::error("Файл не найден в S3", [
                            'path_checked' => array_merge($variants, $webpVariants),
                            'disk_config' => config("filesystems.disks.$disk"),
                        ]);
                        $this->error("   ❌ Ошибка при обработке {$path}: файл не найден ни по одному пути.");
                        $errorCount++;
                        $newGallery[] = $path; // оставляем путь как есть
                        continue;
                    }

                    // Если нашли уже существующий .webp вместо оригинала — не конвертируем и не удаляем
                    if ($foundWebpInstead) {
                        $newGallery[] = $path;
                        $skipCount++;
                        continue;
                    }

                    // Формируем путь для нового .webp — в ту же папку
                    $dir = pathinfo($path, PATHINFO_DIRNAME);
                    $filename = pathinfo($path, PATHINFO_FILENAME);
                    $webpPath = ($dir !== '.' ? $dir . '/' : '') . $filename . '.webp';

                    $this->line("   🔄 Конвертируем: {$path} → {$webpPath}");

                    $imageData = Storage::disk($disk)->get($path);

                    try {
                        $image = $imageManager->read($imageData);
                    } catch (\Throwable $decodeError) {
                        // Невозможно декодировать (например .svg) — пропускаем без удаления
                        $this->warn("   ⚠️ Невозможно декодировать {$path} ({$decodeError->getMessage()}) — пропущено.");
                        Log::warning("Невозможно декодировать изображение", [
                            'path' => $path,
                            'error' => $decodeError->getMessage(),
                        ]);
                        $newGallery[] = $path;
                        $skipCount++;
                        continue;
                    }

                    $webpData = $image->toWebp(60);
                    Storage::disk($disk)->put($webpPath, $webpData);

                    // Удаляем оригинал только если реально конвертировали
                    if ($deleteOriginal !== 'false') {
                        Storage::disk($disk)->delete($path);
                        $this->line("   🗑️ Оригинал удалён: {$path}");
                    }

                    $newGallery[] = $webpPath;
                    $successCount++;
                } catch (\Throwable $e) {
                    $this->error("   ❌ Ошибка при обработке {$path}: {$e->getMessage()}");
                    Log::error("Ошибка конвертации файла", [
                        'path' => $path,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    $errorCount++;
                }
            }

            // Сохраняем результат, если изначально была строка — оставляем строкой
            if (is_string($originalValue)) {
                $item->{$field} = $newGallery[0] ?? null;
            } else {
                $item->{$field} = $newGallery;
            }

            $item->save();
            $this->line("   💾 Обновлены ссылки для #{$item->id}");
        }

        $this->newLine();
        $this->info("🎯 Результаты:");
        $this->line("   ✅ Успешных: {$successCount}");
        $this->line("   ⚠️ Пропущенных: {$skipCount}");
        $this->line("   ❌ Ошибок: {$errorCount}");
        $this->newLine();
        $this->info("🚀 Все операции завершены.");

        Log::info("=== Конвертация завершена ===", [
            'success' => $successCount,
            'skipped' => $skipCount,
            'errors' => $errorCount,
        ]);
    }
}
