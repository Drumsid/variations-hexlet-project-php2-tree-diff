<?php
require_once __DIR__ . '/../hexlet_tree_lib/index.php';

$tree = mkdir2('/', [
  mkdir2('etc', [
    mkdir2('apache'),
    mkdir2('nginx', [
      mkfile('nginx.conf'),
    ]),
    mkdir2('consul', [
      mkfile('config.json'),
      mkdir2('data'),
    ]),
  ]),
  mkdir2('logs'),
  mkfile('hosts'),
]);

function findEmptyDirPaths($tree)
{
    $name = getName($tree);
    $children = getChildren($tree);

    // Если детей нет, то добавляем директорию
    if (count($children) === 0) {
        return [$name];
    } 
    else {
        $path = $name . "/";
    }

    // Фильтруем файлы, они нас не интересуют 
    $dirNames = array_filter($children,
    function ($child) use($path) {
        if(!isFile($child)) {
            $path .= getName($child);
            $newChild = mkdir2($path, getChildren($child), getMeta($child));
            return $newChild;
        }
        
    });

    // Ищем пустые директории внутри текущей
    $emptyDirNames = array_map(
        function ($dir) {
            return findEmptyDirPaths($dir);
        },
        $dirNames);

    // array_flatten выправляет массив, так что он остается плоским
    return array_flatten($emptyDirNames);
    // return $dirNames;
}

// В выводе указана только конечная директория
// Подумайте, как надо изменить функцию, чтобы видеть полный путь
// print_r($tree);
$emptyDirs = findEmptyDirPaths($tree); // ['apache', 'data', 'logs']
print_r($emptyDirs);