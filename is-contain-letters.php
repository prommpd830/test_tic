<?php
header('Content-Type: application/json; charset=utf-8');

$params = json_decode(file_get_contents("php://input"), true);

if(array_key_exists('first_word', $params)){
    if (array_key_exists('second_word', $params)) {
        // get body parameter
        $first_word = $params['first_word'];
        $second_word = $params['second_word'];

        // string to array
        $array_first = str_split($first_word);
        
        $response = '';
        for ($i=0; $i < strlen($first_word); $i++) { 
            // cek letter if has specific char
            if (stripos($second_word, $array_first[$i]) === false) {
                $response = false;
                break;
            } else {
                $response = true;
            }
        }
        echo json_encode($response);
        die;
    } else {
        // create response message error
        $response = json_encode([
            'success' => false,
            'message' => 'second_word must be not null'
        ]);

        echo $response;
        die;
    }
} else {
    // create response message error
    $response = json_encode([
        'success' => false,
        'message' => 'first_word must be not null'
    ]);

    echo $response;
    die;
}



?>