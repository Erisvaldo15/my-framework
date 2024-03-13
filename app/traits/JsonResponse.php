<?php

namespace app\traits;

trait JsonResponse {

    public function success(int $status, mixed $data = "") {
        http_response_code($status);
        echo json_encode([
            "data" => $data,
            "status" => $status,
        ]);
        exit;
    }
    public function error(int $status, string $error) {
        http_response_code($status);
        echo json_encode([
            "status" => $status,
            "error" => $error,
        ]);
        exit;
    }
}