<?php /** @noinspection DuplicatedCode */

use JetBrains\PhpStorm\NoReturn;

include_once "../components/functions.php";

\Functions\Util::startSession();
$conn = \Functions\Database::getDatabase();
$returnData = [];
$requestBody = json_decode(file_get_contents('php://input'), true);
$q = $_GET["q"] ?? null;
if(!$q) $q = $_SESSION["userinfo"]["teacherinfo"]["uuid"] ?? null;

header('Content-type: application/json; charset=utf-8');



#[NoReturn] function sendAccessDenied(): void {
    http_response_code(403);
    die('{ "error": "Access Denied" }');
}

#[NoReturn] function send404(string $msg = "Not Found"): void {
    http_response_code(404);
    die("{ \"error\": \"$msg\" }");
}





// kontrola, zda požadavek pochází z localhostu
//$allowedIPs = ['127.0.0.1', '::1'];
//if ($_SERVER['REMOTE_ADDR'] !== 'localhost' && !in_array($_SERVER['REMOTE_ADDR'], $allowedIPs)) sendAccessDenied();



// kontrola přihlášení uživatele
if(!\Functions\Database::isUserLoggedIn() && empty($requestBody)) {

}



// kontrola requestu
if(/*$_SERVER["REQUEST_METHOD"] !== "GET" || */!$q) {
    http_response_code(406);
    exit;
}




if($q == "my") {
    $query = "SELECT `reservations` FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $_SESSION["userinfo"]["username"]);

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if(!$result || $result->num_rows == 0) send404();

    $returnData = json_decode($result->fetch_assoc()["reservations"] ?? "[]", true);
} else {
    switch ($_SERVER["REQUEST_METHOD"]) {
        case "GET": {
            $query = "SELECT `reservations` FROM lecturers WHERE uuid = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $q);

            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if(!$result || $result->num_rows == 0) send404("Učitel nebyl nalezen.");

            $reservations = json_decode($result->fetch_assoc()["reservations"] ?? "[]", true);

            if(!isset($_SESSION["userinfo"]["teacherinfo"]["uuid"]) || (!empty($_SESSION["userinfo"]["teacherinfo"]["uuid"]) && $q != $_SESSION["userinfo"]["teacherinfo"]["uuid"])) for($i = 0; $i < count($reservations); $i++) {
                $returnData[$i]["date"] = $reservations[$i]["date"];
                $returnData[$i]["uuid"] = $reservations[$i]["uuid"];
                if(empty($reservations[$i]["user"])) $returnData[$i]["user"] = null;
                else if(!empty($_SESSION["userinfo"]["username"]) && $reservations[$i]["user"] == $_SESSION["userinfo"]["username"]) $returnData[$i]["user"] = "You";
                else $returnData[$i]["user"] = "Not you";
            } else $returnData = $reservations;
        } break;

        case "PUT": {
            $query = "SELECT `reservations` FROM lecturers WHERE uuid = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $q);

            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if(!$result || $result->num_rows == 0) send404("Učitel nebyl nalezen.");

            $reservations = json_decode($result->fetch_assoc()["reservations"] ?? "[]", true);

            for ($i = 0; $i < count($reservations); $i++) {
                if(empty($reservations[$i]["uuid"]) || $reservations[$i]["uuid"] != $requestBody["uuid"]) continue;
                if($reservations[$i]["user"] != null) continue;


                $reservations[$i]["user"] = $_SESSION["userinfo"]["username"] ?? "-";
                $reservations[$i]["user_first_name"] = $_SESSION["userinfo"]["first_name"] ?? $requestBody["first_name"];
                $reservations[$i]["user_last_name"] = $_SESSION["userinfo"]["last_name"] ?? $requestBody["last_name"] ;
                $reservations[$i]["user_email"] = $_SESSION["userinfo"]["email"] ?? $requestBody["email"];
                $reservations[$i]["user_mobilenumbers"] = !empty($_SESSION["userinfo"]["mobilenumbers"]) ? explode(",", $_SESSION["userinfo"]["mobilenumbers"]) : $requestBody["mobile_number"];
            }

            $reservations = json_encode($reservations);

            $query = "UPDATE lecturers SET reservations = ? WHERE uuid = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $reservations, $q);
            $stmt->execute();
            $stmt->close();
        } break;

        case "DELETE": {
            $query = "SELECT `reservations` FROM lecturers WHERE uuid = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $q);

            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if(!$result || $result->num_rows == 0) send404("Učitel nebyl nalezen.");

            $reservations = json_decode($result->fetch_assoc()["reservations"] ?? "[]", true);

            for ($i = 0; $i < count($reservations); $i++) {
                if(empty($reservations[$i]["uuid"]) || $reservations[$i]["uuid"] != $requestBody["uuid"]) continue;
                if($reservations[$i]["user"] != $_SESSION["userinfo"]["username"]) continue;

                $uuid = $reservations[$i]["uuid"];
                $date = $reservations[$i]["date"];
                $reservations[$i] = [];

                $reservations[$i]["user"] = null;
                $reservations[$i]["date"] = $date;
                $reservations[$i]["uuid"] = $uuid;
            }

            $reservations = json_encode($reservations);

            $query = "UPDATE lecturers SET reservations = ? WHERE uuid = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $reservations, $q);
            $stmt->execute();
            $stmt->close();
        } break;

        case "POST": {
            $query = "SELECT `reservations` FROM lecturers WHERE uuid = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $q);

            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if(!$result || $result->num_rows == 0) send404("Učitel nebyl nalezen.");

            $reservations = json_decode($result->fetch_assoc()["reservations"] ?? "[]", true);

            // kontrola obsazenosti termínu
            for ($i = 0; $i < count($reservations); $i++) if($reservations[$i]["date"] == (new DateTime($requestBody["date"]))->format("Y-m-d H:i:s")) send404("Tento termín je již obsazen.");

            $reservations[] = [
                "uuid" => \Functions\Util::generateUUID(),
                "date" => (new DateTime($requestBody["date"]))->format("Y-m-d H:i:s"),
                "user" => null
            ];

            $reservations = json_encode($reservations);

            $query = "UPDATE lecturers SET reservations = ? WHERE uuid = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $reservations, $q);
            $stmt->execute();
            $stmt->close();
        } break;

        case "COMPLETEDELETE": {
            $query = "SELECT `reservations` FROM lecturers WHERE uuid = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $q);

            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if(!$result || $result->num_rows == 0) send404("Učitel nebyl nalezen.");

            $reservations = json_decode($result->fetch_assoc()["reservations"] ?? "[]", true);

            for ($i = 0; $i < count($reservations); $i++) {
                if(!isset($reservations[$i]["uuid"]) || $reservations[$i]["uuid"] != $requestBody["uuid"]) continue;
                if(!isset($_SESSION["userinfo"]["teacherinfo"]["uuid"]) || $_SESSION["userinfo"]["teacherinfo"]["uuid"] != $q) continue;

                array_splice($reservations, $i, 1);
            }

            $reservations = json_encode($reservations);

            $query = "UPDATE lecturers SET reservations = ? WHERE uuid = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $reservations, $q);
            $stmt->execute();
            $stmt->close();
        } break;
    }
}




$conn->close();
http_response_code(200);
echo json_encode($returnData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);