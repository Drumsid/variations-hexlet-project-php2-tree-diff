<?php

require_once __DIR__ . '/../hexlet_tree_lib/index.php';
// use function Php\Immutable\Fs\Trees\trees\mkdir;
// use function Php\Immutable\Fs\Trees\trees\mkfile;
// use function Php\Immutable\Fs\Trees\trees\isFile;
// use function Php\Immutable\Fs\Trees\trees\getChildren;
// use function Php\Immutable\Fs\Trees\trees\getName;
// use function Php\Immutable\Fs\Trees\trees\getMeta;

// Реализуйте функцию compressImages(), которая принимает на вход директорию, находит внутри нее
// картинки и "сжимает" их. Под сжиманием понимается уменьшение свойства size в метаданных в два раза.
// Функция должна вернуть обновленную директорию со сжатыми картинками и всеми остальными данными, которые
// были внутри этой директории.

// Картинками считаются все файлы заканчивающиеся на .jpg.


$tree = mkdir2(
    'my documents',
    [
        mkfile('avatar.jpg', ['size' => 100]),
        mkfile('passport.jpg', ['size' => 200]),
        mkfile('family.jpg', ['size' => 150]),
        mkfile('addresses', ['size' => 125]),
        mkdir2('presentations')
    ]
);

// print_r($tree);

function compressImages($tree)
{
    $name = getName($tree);
    $children = getChildren($tree);
    $resizeImage = array_map(
        function ($child) {
            if (isFile($child) && substr(getName($child), -4) == '.jpg') {
                $newSize = getMeta($child);
                $newSize['size'] = $newSize['size'] / 2;
                return mkFile(getName($child), $newSize);
            } elseif (isFile($child)) {
                return mkFile(getName($child), getMeta($child));
            }
            return mkdir2(getName($child), getChildren($child), getMeta($child));
        },
        $children
    );

    return mkdir2($name, $resizeImage, getMeta($tree));
}

print_r(compressImages($tree));
