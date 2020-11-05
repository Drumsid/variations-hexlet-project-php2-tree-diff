<?php

require_once __DIR__ . '/../hexlet_tree_lib/index.php';
// use function Php\Immutable\Fs\Trees\trees\mkdir;
// use function Php\Immutable\Fs\Trees\trees\mkfile;
// use function Php\Immutable\Fs\Trees\trees\isFile;
// use function Php\Immutable\Fs\Trees\trees\getChildren;
// use function Php\Immutable\Fs\Trees\trees\getName;
// use function Php\Immutable\Fs\Trees\trees\getMeta;

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

print_r($tree);
