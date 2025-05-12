<?php

require '../vendor/autoload.php';

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;


function validateEmail($email) {
    
    $validator = new EmailValidator();

    return $validator->isValid($email, new DNSCheckValidation());
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $email = $_GET['email'];

    $isValid = validateEmail($email);

    $response = [
        'isValid' => $isValid
    ];

    header('Content-Type: application/json');

    echo json_encode($response);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}

?>
