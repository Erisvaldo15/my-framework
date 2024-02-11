<?php

use app\database\model\{Post, User, Comment};

require_once '../vendor/autoload.php';

try {

    $post = new Post;

    dd($post->addRelations(
        [User::class, 'belongsTo'], [Comment::class, 'hasMany', 'comments']
    )->find(10)->comments);

} catch (\Throwable $th) {
    dd("{$th->getMessage()} in line {$th->getLine()} from file {$th->getFile()}");
}