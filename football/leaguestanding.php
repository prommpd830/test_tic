<?php
header('Content-Type: application/json; charset=utf-8');

function leaguestanding()
{
    include 'connDB.php';

    $sql = "SELECT clubname, points FROM football";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $data = [];
        // output data of each row
        while($row = $result->fetch_assoc()) {
            array_push($data, $row);
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

// call leaguestanding
$leaguestanding = leaguestanding();
// create response
$response = json_encode($leaguestanding);
echo $response;
die;

?>