<?php

namespace App\Service;

use App\Jobs\ThumbnailImageJob;
use App\Jobs\WatermarkImageJob;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class StorageService {
    /**
     * Сохраням файл в storage
     * @param $file
     * @param $folder
     * @return string
     */
    public static function saveMd5($file, $folder) {
        $fullName = $file->getClientOriginalName();
        $filename = self::getFileName($fullName);
        $filename = md5(pathinfo($filename, PATHINFO_FILENAME) . time());
        $extension = self::getFileExtension($fullName);
        $filename = $filename.'.'.$extension;

        $foldername = substr($filename, 0, 2);

        return $file->storeAs(
            $folder.'/'.$foldername, $filename, 'public'
        );
    }


    /**
     * Сохранение файла под своим оригинальным именем
     * @param $file
     * @param $folder
     * @return string
     */
    public static function saveOriginName($file, $folder) {
        $fileName = $file->getClientOriginalName();
        return $file->storeAs($folder, $fileName, 'public');
    }

    /**
     * Создаем миниатюру для существующей картинки
     * @param $path
     * @param $size
     * @return string
     */
    public static function thumbnail($path, $size) {
        $pathFull = Storage::disk('public')->path($path);
        $pathDirectory = dirname($path);
        $filename = pathinfo($pathFull, PATHINFO_FILENAME);
        $extension = pathinfo($pathFull, PATHINFO_EXTENSION);

        $pathResized = $pathDirectory . '/' . $filename . '_thumb_'. $size . '.' . $extension;
        Storage::disk('public')->copy($path, $pathResized);
        $pathResizedFull = Storage::disk('public')->path($pathResized);

        ThumbnailImageJob::dispatch($pathResizedFull, $size)->onQueue('thumbnail');

        return $pathResized;
    }

    /**
     * Добавление водяного знака на изображение
     * @param $path
     */
    public static function watermark($path) {
        $path = Storage::disk('public')->path($path);
        WatermarkImageJob::dispatch($path)->onQueue('watermark');
    }


    /**
     * Функция принимает параметры request в виде массива и если есть фалы сохраняет и возвращает массив с путями для сохранения
     * @param array $data
     * @param string $folder
     * @return array
     */
    public static function autoFill(array $data, $folder) {
        $request = app()->request;

        foreach ($data as $key => $item) {
            if ($request->has($key) && $request->hasFile($key)) {
                $file = $request->file($key);
                // Если передаем multiple
                if (is_array($file)) {
                    $data[$key] = [];
                    foreach ($file as $i => $value) {
                        $data[$key][$i] = self::saveMd5($value, $folder);
                    }
                } else {
                    $data[$key] = self::saveMd5($file, $folder);
                }
            }
        }

        return $data;
    }

    /**
     * Список файлов в папке
     * @param $path
     * @return array
     */
    public static function getFilesInDir($path){
        $result = [];
        if(is_dir($path)){
            $files = scandir($path);
            foreach ($files as $key => $value){
                if(!in_array($value,['.', '..'])){
                    $result[] = $value;
                }
            }
        }
        return $result;
    }


    /**
     * @param $filePath
     * @param $storePath
     * @return bool
     */
    public static function unzip($filePath, $storePath){
        $zip = new ZipArchive;
        $res = $zip->open($filePath);

        if ($res === TRUE) {
            $zip->extractTo($storePath);
            $zip->close();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Возвращаем содержимое архива
     * @param $file
     * @return array
     */
    public static function getZipFiles($file){
        $files = [];
        $zip = zip_open($file);
        if ($zip) {
            while ($zipEntry = zip_read($zip)) {
                $files[] = zip_entry_name($zipEntry);
            }
            zip_close($zip);
        }
        return $files;
    }

    /**
     * Получить количество файлов в архиве
     * @param $file
     * @return int
     */
    public static function getCountFileZip($file){
        $result = [];
        $zip = zip_open($file);
        if ($zip) {
            while ($zipEntry = zip_read($zip)) {
                $result[] = zip_entry_name($zipEntry);
            }
            zip_close($zip);
        }
        return count($result);
    }

    /**
     * @param $filename
     * @return string
     */
    public static function getFileExtension($filename) {
        $fileInfo = pathinfo($filename);
        return $fileInfo['extension'];
    }

    public static function getFileName($filename){
        $fileInfo = pathinfo($filename);
        $extension = $fileInfo['extension'];
        return basename($filename, '.'.$extension);
    }

    /**
     * Возвращаем папку в которой файл
     * @param $path
     * @return mixed
     */
    public static function getEndFolder($path){
        $path = pathinfo($path)['dirname'];
        $path = explode('/', $path);
        $folder = end($path);

        return $folder;
    }
}
