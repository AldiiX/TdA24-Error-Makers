<?php

include_once __DIR__ . "/../components/API.php";
include_once __DIR__ . "/../components/functions.php";
$conn = \Functions\Database::getDatabase();
$returnArray = [];
header('Content-type: application/json; charset=utf-8');





switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET": {
        $returnArray = Api::activitiesGET();
    } break;

    case "POST": {
        $input = json_decode(file_get_contents("php://input"), true);

        $input["tools"] = json_encode($input["tools"]);
        $input["homePreparation"] = json_encode($input["homePreparation"]);
        $input["objectives"] = json_encode($input["objectives"]);
        $input["edLevel"] = json_encode($input["edLevel"]);
        $input["instructions"] = json_encode($input["instructions"]);
        $input["agenda"] = json_encode($input["agenda"]);
        $input["links"] = json_encode($input["links"]);
        $input["gallery"] = json_encode($input["gallery"]);


        $stmt = $conn->prepare("INSERT INTO activities (uuid, activityName, description, tools, homePreparation, objectives, edLevel, instructions, agenda, links, gallery) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $input["uuid"], $input["activityName"], $input["description"], $input["tools"], $input["homePreparation"], $input["objectives"], $input["edLevel"], $input["instructions"], $input["agenda"], $input["links"], $input["gallery"]);
        $stmt->execute();
        $returnArray = ["status" => "success"];
    } break;
}

$conn->close();
http_response_code(200);
echo json_encode($returnArray, JSON_PRETTY_PRINT);