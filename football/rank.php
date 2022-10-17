<?php
header('Content-Type: application/json; charset=utf-8');

function leaguestanding($clubname)
{
    include 'connDB.php';

    $sql = "SELECT clubname, points FROM football ORDER BY points DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $data = [];
        $standing = 1;
        // output data of each row
        while($row = $result->fetch_assoc()) {
            if ($row['clubname'] == $clubname) {
                array_push($data, [
                    'clubname' => $row['clubname'],
                    'standing' => $standing++
                ]);
            } else {
                $standing++;
            }
        }

        $response = $data;
    } else {
        $response = [
            'success' => false,
            'message' => 'Data football not found'
        ];
    }
    $conn->close();

    return $response;
}

if(isset($_GET['clubname'])) {
    $clubname = $_GET['clubname'];
    // call leaguestanding
    $leaguestanding = leaguestanding($clubname);
    if(empty($leaguestanding)) {
        // create response
        $response = json_encode([
            'success' => false,
            'message' => $clubname. ' not found'
        ]);
    } else {
        // create response
        $response = json_encode($leaguestanding);
    }
} else {
    // create response
    $response = json_encode([
        'success' => false,
        'message' => 'clubname must be not null'
    ]);
}

echo $response;
die;

?>