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
    } else {
        $path = $name . "/";
    }

    // Фильтруем файлы, они нас не интересуют
    $dirNames = array_filter($children, function ($child) {


        if (!isFile($child)) {
            return $child;
        }
    });
    $renamePathDir = array_map(function ($item) use ($path) {


          $path .= getName($item);
        $newChild = mkdir2($path, getChildren($item), getMeta($item));
        return $newChild;
    }, $dirNames);
    // Ищем пустые директории внутри текущей
    $emptyDirNames = array_map(function ($dir) {


            return findEmptyDirPaths($dir);
    }, $renamePathDir);
    // array_flatten выправляет массив, так что он остается плоским
    return array_flatten($emptyDirNames);
}

// В выводе указана только конечная директория
// Подумайте, как надо изменить функцию, чтобы видеть полный путь
// print_r($tree);
$emptyDirs = findEmptyDirPaths($tree);
// ['apache', 'data', 'logs']
// print_r($emptyDirs);


// =========================================================================================
// =========================================================================================
// =========================================================================================

// Реализуйте функцию findFilesByName(), которая принимает на вход файловое дерево и подстроку,
//  а возвращает список файлов, имена которых содержат эту подстроку.

$anowerTree = mkdir2('/', [
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
function findFilesByName($tree, $str)
{
    $allFiles = findAllFiles($tree);
    $findFiles = array_filter(
        $allFiles,
        function ($file) use ($str) {
            if (findSubStr($file['name'], $str)) {
                return $file;
            }
        }
    );

    $res = array_map(
        function ($item) {
            return $item['name'];
        },
        $findFiles
    );
    return array_values($res);
}

// вернет все файлы с именами от корня
function findAllFiles($tree)
{
    $rutName = getName($tree);

    $children = getChildren($tree);
    $res = array_reduce($children, function ($acc, $node) use ($rutName) {
        $name = getName($node);
        if (isDirectory($node)) {
            $new = mkdir2($rutName . $name . "/", getChildren($node), getMeta($node));
            $acc = array_merge($acc, findAllFiles($new));
        }
        if (isFile($node)) {
            $acc[] = mkfile($rutName . $name, getMeta($node));
        }
            return $acc;
    }, []);
    return $res;
}

function findSubStr($name, $find)
{
    $arr = explode('/', $name);
    $str = array_pop($arr);
    $res = strpos($str, $find);
    if ($res || $res === 0) {
        return true;
    }
    return false;
}

$result = findFilesByName($anowerTree, 'co');
print_r($result);


// hexlet solution
// BEGIN
function iter($node, $subStr, $ancestry, $acc)
{
    $name = getName($node);
    $newAncestry = ($name === '/') ? '' : "$ancestry/$name";
    if (isFile($node)) {
        if (strpos($name, $subStr) === false) {
            return $acc;
        }
        $acc[] = $newAncestry;
        return $acc;
    }

    return array_reduce(
        getChildren($node),
        function ($newAcc, $child) use ($subStr, $newAncestry) {
            return iter($child, $subStr, $newAncestry, $newAcc);
        },
        $acc
    );
}


function findFilesByName2($root, $subStr)
{
    return iter($root, $subStr, '', []);
}
// END
