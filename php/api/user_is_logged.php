<?php

include_once "../components/functions.php";

\Functions\Util::startSession();
$conn = \Functions\Database::getDatabase();
$returnData = [];
//$requestBody = json_decode(file_get_contents('php://input'), true);
header('Content-type: application/json; charset=utf-8');


if(\Functions\Database::isUserLoggedIn()) $returnData = true;
else $returnData = false;


$conn->close();
http_response_code(200);
echo json_encode($returnData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);