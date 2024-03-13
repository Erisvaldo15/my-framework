<?php

namespace app\controllers\admin;

use app\traits\JsonResponse;

class HomeController
{
    use JsonResponse;

    public function index()
    {
    }

    public function show($user, int $id)
    {
        return $this->success(200, 
            "success"
        );
    }

    public function users()
    {
        return $this->success(200, 
            "users"
        );
    }

    public function store()
    {
        dd("store");
    }
}
