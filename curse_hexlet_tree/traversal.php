<?php

require_once __DIR__ . '/../hexlet_tree_lib/index.php';

// Реализуйте функцию downcaseFileNames(), которая принимает на вход директорию (объект-дерево)
// и приводит имена всех файлов в этой и во всех вложенных директориях к нижнему регистру.
// Результат в виде обработанной директории возвращается наружу. Исходное дерево не изменяется.

$tree = mkdir2('/', [
    mkdir2('eTc', [
        mkdir2('NgiNx'),
        mkdir2('CONSUL', [
            mkfile('config.json'),
        ]),
    ]),
    mkfile('hOsts'),
]);


// print_r($tree);

// my solution
function downcaseFileNames($tree)
{
    $name = getName($tree);
    if (isFile($tree)) {
        $downcaseName = mb_strtolower($name);
        return mkFile($downcaseName, getMeta($tree));
    }

    $children = getChildren($tree);
    $newChildren = array_map(
        function ($child) {
            return downcaseFileNames($child);
        },
        $children
    );
    return mkdir2($name, $newChildren, getMeta($tree));
}

// print_r(downcaseFileNames($tree));

// BEGIN
// hexlet solution but working only php 7.4
function downcaseFileNames2($node)
{
    $name = getName($node);

    if (isFile($node)) {
        $newName = strtolower(getName($node));
        return mkfile($newName, getMeta($node));
    }

    $updatedChildren = array_map(fn($child) => downcaseFileNames2($child), getChildren($node));

    return mkdir($name, $updatedChildren, getMeta($node));
}
// END
