<?php

namespace app\controllers\admin;

class HomeController
{

    public function index()
    {
        http_response_code(404);
        echo json_encode([
            "status" => "200",
            "goll" => "example",
        ]);
    }

    public function show()
    {
        dd("show");
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
