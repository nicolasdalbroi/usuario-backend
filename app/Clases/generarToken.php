<?php
namespace App\clases;

class generarToken{
    public function token(){
        $val = true;
        $token = bin2hex(openssl_random_pseudo_bytes(16,$val));
            return $token;
    }
}