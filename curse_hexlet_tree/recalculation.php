<?php

require_once __DIR__ . '/../hexlet_tree_lib/index.php';

// Реализуйте функцию du(), которая принимает на вход директорию, а возвращает список узлов вложенных,
//  (директорий и файлов) в указанную директорию на один уровень, и место, которое они занимают.
//  Размер файла задается в метаданных. Размер директории складывается из сумм всех размеров файлов,
//  находящихся внутри во всех подпапках. Сами папки размера не имеют.

$tree = mkdir2('/', [
    mkdir2('etc', [
        mkdir2('apache'),
        mkdir2('nginx', [
            mkfile('nginx.conf', ['size' => 800]),
        ]),
        mkdir2('consul', [
            mkfile('config.json', ['size' => 1200]),
            mkfile('data', ['size' => 8200]),
            mkfile('raft', ['size' => 80]),
        ]),
    ]),
    mkfile('hosts', ['size' => 3500]),
    mkfile('resolve', ['size' => 1000]),
]);

$tree2 = mkdir2('/', [
    mkdir2('etc', [
        mkdir2('apache'),
        mkdir2('nginx', [
            mkfile('nginx.conf', ['size' => 800]),
        ]),
        mkdir2('consul', [
            mkfile('config.json', ['size' => 1200]),
            mkfile('data', ['size' => 8200]),
            mkfile('raft', ['size' => 80]),
        ]),
    ]),
    mkfile('hosts', ['size' => 3500]),
    mkfile('resolve', ['size' => 1000]),
]);

// my solution
function calculateSize($tree)
{
    if (isFile($tree)) {
        $size = getMeta($tree)['size'];
        return $size;
    }

    $children = getChildren($tree);
    $sizeDir = array_reduce(
        $children,
        function ($acc, $item) {
            if (isFile($item)) {
                $acc[] = getMeta($item)['size'];
                return $acc;
            }
            return array_merge($acc, calculateSize($item));
        },
        []
    );

    return $sizeDir;
}
function countSize($tree)
{
    $res = array_reduce(
        $tree,
        function ($acc, $item) {
            $acc += $item;
            return $acc;
        },
        0
    );
    return $res;
}

function countFileSize($tree)
{
    return countSize(calculateSize($tree));
}
function du($tree)
{
    $children = getChildren($tree);
    $find = array_map(
        function ($item) {
            if (isFile($item)) {
                return [getName($item), getMeta($item)['size']];
            }
            return [getName($item), countFileSize($item)];
        },
        $children
    );
    usort($find, function ($a, $b){
        if ($a[1] == $b[1]) {
            return 0;
        }
        return ($a[1] < $b[1]) ? 1 : -1;
    });
    return $find;
}

// print_r(du($tree));
$sort = du($tree);
print_r($sort);

// hexlet solution

// BEGIN
function calculateFilesSize($node)
{
    return reduce(function ($acc, $n) {
        if (isDirectory($n)) {
            return $acc;
        }

        $meta = getMeta($n);

        return $acc + $meta['size'];
    }, $node, 0);
}

function du2($node)
{
    $result = array_map(fn($node) => [
        getName($node), calculateFilesSize($node)
    ], getChildren($node));

    usort($result, fn($arr1, $arr2) => $arr2[1] <=> $arr1[1]);

    return $result;
}
// END