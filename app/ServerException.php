<?php

class ServerException extends Exception {

    public function __construct($message = "Ошибка на сервере.", $code = 500) {

        parent::__construct($message, $code);
    }

    public function handle() {

        error_log($this->getMessage());
        http_response_code(500);

        return json_encode([
            'status' => 'error',
            'message' => $this->getMessage()
        ]);
    }
}
