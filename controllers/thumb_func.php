<?php
// Функция для создания миниатюры
function createThumbnail($src, $dest, $width, $height, $mimeType) {
    // Проверка на существование исходного файла
    if (!file_exists($src)) {
        return false;
    }

    switch ($mimeType) {
        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($src);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($src);
            break;
        default:
            return false;
    }

    // Проверка на ошибки создания изображения
    if (!$sourceImage) {
        return false;
    }

    // Получение размеров исходного изображения
    $originalWidth = imagesx($sourceImage);
    $originalHeight = imagesy($sourceImage);

    // Создание пустого изображения для миниатюры
    $thumbnailImage = imagecreatetruecolor($width, $height);

    // Сохраняем прозрачность для PNG
    if ($mimeType === 'image/png') {
        imagealphablending($thumbnailImage, false);
        imagesavealpha($thumbnailImage, true);
    }

    // Пропорциональное изменение размера исходного изображения в миниатюру
    imagecopyresampled($thumbnailImage, $sourceImage, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);

    // Сохранение миниатюры в файл
    $result = false;
    switch ($mimeType) {
        case 'image/jpeg':
            $result = imagejpeg($thumbnailImage, $dest);
            break;
        case 'image/png':
            $result = imagepng($thumbnailImage, $dest);
            break;
    }

    // Очистка памяти
    imagedestroy($sourceImage);
    imagedestroy($thumbnailImage);

    return $result;
}
