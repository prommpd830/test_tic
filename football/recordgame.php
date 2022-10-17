<?php
header('Content-Type: application/json; charset=utf-8');

function checkscore($clubhomename, $clubawayname, $scorehome, $scoreaway)
{
    if ($scorehome > $scoreaway) {
        // condition if scorehome win
        $response = [
            'status' => 'winner',
            'clubname' => $clubhomename
        ];
    } elseif ($scorehome < $scoreaway) {
        // condition if scorehome lose
        $response = [
            'status' => 'winner',
            'clubname' => $clubawayname
        ];
    } elseif ($scorehome == $scoreaway) {
        // condition if scorehome draw
        $response = [
            'status' => 'draw',
            'clubhomename' => $clubhomename,
            'clubawayname' => $clubawayname
        ];
    }

    return $response;
}

function insertClub($data)
{
    include 'connDB.php';

    $sql = "INSERT INTO football (clubname, points) VALUES (\"{$data['clubname']}\", \"{$data['points']}\")";

    if ($conn->query($sql) === TRUE) {
        $response = [
            'success' => true,
            'message' => 'New record created successfully'
        ];
    } else {
        $response = [
            'status' => false,
            'message' => 'Error inserting: '. $conn->error
        ];
    }
    $conn->close();

    return $response;
}

function updateClub($data)
{
    include 'connDB.php';

    $sql = "UPDATE football SET points = \"{$data['points']}\" WHERE id = \"{$data['id']}\"";

    if ($conn->query($sql) === TRUE) {
        $response = [
            'success' => true,
            'message' => 'Record updated successfully'
        ];
    } else {
        $response = [
            'status' => false,
            'message' => 'Error updating record: '. $conn->error
        ];
    }
    $conn->close();

    return $response;
}

function getClub($clubname)
{
    include 'connDB.php';

    $sql = "SELECT * FROM football WHERE clubname = \"{$clubname}\"";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $data = [];
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $data = [
                'id' => $row['id'],
                'clubname' => $row['clubname'],
                'points' => $row['points']
            ];
        }

        $response = [
            'success' => true,
            'data' => $data
        ];
    } else {
        $response = [
            'success' => false,
            'message' => $clubname.' not found'
        ];
    }
    $conn->close();

    return $response;
}

function recordgame($clubhomename, $clubawayname, $score)
{
    // explode score
    $explode = explode(':', $score);
    $scorehome = (int)trim($explode[0], ' ');
    $scoreaway = (int)trim($explode[1], ' ');

    // check score who is win
    $checkscore = checkscore($clubhomename, $clubawayname, $scorehome, $scoreaway);

    // store points
    if($checkscore['status'] == 'winner') {
        // check club if exists
        $getClub = getClub($checkscore['clubname']);
        if($getClub['success'] == true) {
            // create new data
            $dataClub = $getClub['data'];
            $data = [
                'id' => $dataClub['id'],
                'clubname' => $dataClub['clubname'],
                'points' => (int)$dataClub['points'] + 3
            ];

            // updated data
            $updateClub = updateClub($data);

            if($updateClub['success'] == false) {
                // return response if something error
                return json_encode($updateClub);
            }
        } else {
            // create new data
            $data = [
                'clubname' => $checkscore['clubname'],
                'points' => 3
            ];

            // inserted data
            $insertClub = insertClub($data);

            if($insertClub['success'] == false) {
                // return response if something error
                return json_encode($insertClub);
            }
        }
    } elseif ($checkscore['status'] == 'draw') {
        // check clubhome if exists
        $getClubHome = getClub($checkscore['clubhomename']);
        if($getClubHome['success'] == true) {
            // create new data
            $dataClub = $getClubHome['data'];
            $data = [
                'id' => $dataClub['id'],
                'clubname' => $dataClub['clubname'],
                'points' => (int)$dataClub['points'] + 1
            ];

            // updated data
            $updateClub = updateClub($data);

            if($updateClub['success'] == false) {
                // return response if something error
                return json_encode($updateClub);
            }
        } else {
            // create new data
            $data = [
                'clubname' => $checkscore['clubhomename'],
                'points' => 1
            ];
            
            // inserted data
            $insertClub = insertClub($data);

            if($insertClub['success'] == false) {
                // return response if something error
                return json_encode($insertClub);
            }
        }

        // check clubaway if exists
        $getClubAway = getClub($checkscore['clubawayname']);
        if($getClubAway['success'] == true) {
            // create new data
            $dataClub = $getClubAway['data'];
            $data = [
                'id' => $dataClub['id'],
                'clubname' => $dataClub['clubname'],
                'points' => (int)$dataClub['points'] + 1
            ];

            // updated data
            $updateClub = updateClub($data);

            if($updateClub['success'] == false) {
                // return response if something error
                return json_encode($updateClub);
            }
        } else {
            // create new data
            $data = [
                'clubname' => $checkscore['clubawayname'],
                'points' => 1
            ];
            
            // inserted data
            $insertClub = insertClub($data);

            if($insertClub['success'] == false) {
                // return response if something error
                return json_encode($insertClub);
            }
        }
    }


    // get data parameters
    $data = [
        'clubhomename' => $clubhomename,
        'clubawayname' => $clubawayname,
        'score' => $score,
        'result' => $checkscore
    ];

    // parse to json
    $response = json_encode($data);
    
    // return response
    return $response;
}


$params = json_decode(file_get_contents("php://input"), true);

// conditions if clubhomename exists
if (array_key_exists('clubhomename', $params)) {
    // conditions if clubawayname exists
    if (array_key_exists('clubawayname', $params)) {
        // conditions if score exists
        if (array_key_exists('score', $params)) {
            // condition all input if exists
            if ($params['clubhomename'] && $params['clubawayname'] && $params['score']) {
                // get input post
                $clubhomename = $params['clubhomename'];
                $clubawayname = $params['clubawayname'];
                $score = $params['score'];

                // call function recordgame
                $response = recordgame($clubhomename, $clubawayname, $score);

                // return response
                echo $response;
                die;
            }
        } else {
            // create response message error
            $response = json_encode([
                'success' => false,
                'message' => 'score must be not null'
            ]);

            echo $response;
            die;
        }
    } else {
        // create response message error
        $response = json_encode([
            'success' => false,
            'message' => 'clubawayname must be not null'
        ]);

        echo $response;
        die;
    }
} else {
    // create response message error
    $response = json_encode([
        'success' => false,
        'message' => 'clubhomename must be not null'
    ]);

    echo $response;
    die;
}


?>