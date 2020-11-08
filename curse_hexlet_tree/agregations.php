<?php

require_once __DIR__ . '/../hexlet_tree_lib/index.php';

// Реализуйте функцию getHiddenFilesCount(), которая считает количество скрытых файлов в директории и
// всех поддиректориях. Скрытым файлом в Linux системах считается файл, название которого начинается с точки.

$tree = mkdir2('/', [
    mkdir2('etc', [
    mkdir2('apache', []),
    mkdir2('nginx', [
        mkfile('.nginx.conf', ['size' => 800]),
    ]),
    mkdir2('.consul', [
        mkfile('.config.json', ['size' => 1200]),
        mkfile('data', ['size' => 8200]),
        mkfile('raft', ['size' => 80]),
    ]),
    ]),
    mkfile('.hosts', ['size' => 3500]),
    mkfile('resolve', ['size' => 1000]),
]);

$tree2 = mkdir2('/', [
    mkdir2('etc', [
        mkdir2('apache', []),
        mkdir2('nginx', [
            mkfile('.nginx.conf', ['size' => 800]),
        ]),
        mkdir2('.consul', [
            mkfile('.config.json', ['size' => 1200]),
            mkfile('data', ['size' => 8200]),
            mkfile('raft', ['size' => 80]),
        ]),
    ]),
    mkfile('.hosts', ['size' => 3500]),
    mkfile('resolve', ['size' => 1000]),
]);

$tree3 = mkdir2('/', [
    mkdir2('.etc', [
        mkdir2('.apache', []),
        mkdir2('nginx', [
            mkfile('nginx.conf', ['size' => 800]),
        ]),
    ]),
    mkdir2('.consul', [
        mkfile('config.json', ['size' => 1200]),
        mkfile('.raft', ['size' => 80]),
    ]),
    mkfile('hosts', ['size' => 3500]),
    mkfile('resolve', ['size' => 1000]),
]);

$easyTree = mkdir2('/', [
    mkFile('1'),
    mkFile('2'),
    mkdir2('dir', [
        mkFile('3')
    ]),
    mkFile('4'),
]);


function findAllFiles($tree)
{

    $children = getChildren($tree);
    $res = array_reduce(
        $children,
        function ($acc, $node) {
            if (isDirectory($node)) {
                $acc = array_merge($acc, findAllFiles($node));
            }
            if (isFile($node)) {
                $acc[] = $node;
            }
            return $acc;
        },
        []
    );
    return $res;
}

function findInFiles($tree)
{
    $res = array_filter(
        $tree,
        function ($node) {
            if (substr(getName($node), 0, 1) == '.') {
                return $node;
            }
        }
    );
    return $res;
}

// my solution
function getHiddenFilesCount($tree)
{
    $filesTree = findAllFiles($tree);
    $result = findInFiles($filesTree);
    return count($result);
}

// print_r(getHiddenFilesCount($tree3));


// hexlet solution
function getHiddenFilesCount2($node)
{
    $name = getName($node);
    if (isFile($node)) {
        $firstSymbol = substr($name, 0, 1);
        return $firstSymbol === "." ? 1 : 0;
    }

    $children = getChildren($node);

    return array_reduce($children, fn($acc, $child) => $acc + getHiddenFilesCount($child));
}
