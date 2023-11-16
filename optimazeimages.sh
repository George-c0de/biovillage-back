#!/bin/bash

# для работы скрипта необходимо установить tinypng-cli https://www.npmjs.com/package/tinypng-cli

# Массив токенов от tinypng
tokens=(BzMZBHGNnpM64nKmDYylNStjMBMTdkr7 BzMZBHGNnpM64nKmDYylNStjMBMTzlSd)

# Папка с картинками
readonly path=storage/app/public
# Ширина картинки для ресайза
readonly width_size=1080
# Высота картинки для ресайза
readonly height_size=1080
# Максимальный размер для поиска по сжатию
readonly max_size=100

echo "Начинаем ресайз картинок"
# Сначала проходим по директории и ищем изображения больше чем 1080х1080 если есть ресайзим их
# find . -iname "*.jpg" -type f -exec identify -format '%w %h %i\n' '{}' \; | awk '$1<1080 || $2<1080{print $3}'
# find . -iname "*.jpg" -type f | xargs -I{} identify -format '%w %h %i\n' {} | awk '$1<1080 || $2<1080{print $3}'
# shellcheck disable=SC2038
for f in $(find $path \( -iname "*.png" -o -iname "*.jpeg" -o -iname "*.jpg"  \) -type f | xargs -I{} identify -format '%w %h %i\n' {} | awk '$1>'$width_size'||$2>'$height_size'{print $3}'); do
  echo "Ресайзим картинку $f"
	convert $f -resize "$width_size"x"$height_size" $f
	if [ $? -eq 0 ]; then
     echo resize OK
  else
     echo resize FAIL
  fi
done
echo "Закончили ресайз картинок"

echo -e "\n"
echo "Начинаем сжатие картинок"
# Далее ищем изображения больше чем 100Kb если находим сжимаем
# shellcheck disable=SC2044
for f in $(find $path -type f -size +"$max_size"k \( -iname "*.png" -o -iname "*.jpeg" -o -iname "*.jpg"  \) ); do
  echo "Сжимаем картинку $f"
  # Перебираем токены и обращаемся к tinypng для сжатия картинки
  for i in "${!tokens[@]}"; do
    echo "Используем токен ${tokens[$i]}"
    COMPRESS=$(tinypng "$f" -k "${tokens[$i]}")
    # Если в выводе tinypng есть подстрока 'Compression failed' то токен исчерпан
    grep "Compression failed" <<< "$COMPRESS"
    # Если токен исчерпал себя переходим к следующему
    if [ $? -eq 0 ]; then
      # Удаляем нерабочий токен
       echo "Удаляем токен ${tokens[$i]}"
       unset "tokens[$i]"
       echo tinypng compress FAIL continue
       echo -e "\n"
       continue
    # Если токен сработал завершаем цикл перебора токенов
    else
       echo "$COMPRESS"
       echo tinypng compress OK
       echo -e "\n"
       break
    fi
  done
done
echo "Закончили сжатие картинок"