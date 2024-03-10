<?php

namespace app\controllers\admin;

use app\database\model\User;

class HomeController
{

    public function index()
    {
        echo json_encode([
            "status" => "200",
            "goll" => "example",
        ]);
    }

    public function show($user, int $id)
    {
        dd($user);
    }

    public function users()
    {
        dd("users");
    }

    public function store()
    {
        dd("store");
    }
}
