<?php

require_once __DIR__ . '/../hexlet_tree_lib/index.php';

// подсчет всех файлов и папок в дереве

$tree = mkdir2('/', [
  mkdir2('etc', [
    mkfile('bashrc'),
    mkfile('consul.cfg'),
  ]),
  mkfile('hexletrc'),
  mkdir2('bin', [
    mkfile('ls'),
    mkfile('cat'),
  ]),
]);

// В реализации используем рекурсивный процесс,
// чтобы добраться до самого дна дерева.
function getNodesCount($tree)
{
    if (isFile($tree)) {
      // Возвращаем 1, для учета текущего файла
        return 1;
    }

  // Если узел — директория, получаем его детей
    $children = getChildren($tree);
  // Самая сложная часть
  // Считаем количество потомков, для каждого из детей,
  // вызывая рекурсивно нашу функцию getNodesCount
    $descendantsCount = array_map(
        // fn($child) => getNodesCount($child),
        function ($child) {
            return getNodesCount($child);
        },
        $children
    );
  // Возвращаем 1 (текущая директория) + общее количество потомков
    return 1 + array_sum($descendantsCount);
}

print_r(getNodesCount($tree)); // 8
