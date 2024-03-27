<?php

include_once "../components/functions.php";
$onlyGetMethod = false;
$conn = \Functions\Database::getDatabase();
$q = $_GET["q"] ?? null;
header('Content-Type: application/json; charset=utf-8');



// kontrola, zda požadavek pochází z localhostu
/*$allowedIPs = ['127.0.0.1', '::1'];
if ($_SERVER['REMOTE_ADDR'] !== 'localhost' && !in_array($_SERVER['REMOTE_ADDR'], $allowedIPs)) {
    http_response_code(403);
    die("Access Denied");
}*/

if(!isset($_SERVER["PHP_AUTH_USER"]) || !isset($_SERVER['PHP_AUTH_PW'])) {
    http_response_code(401);
    die('{ "error": "Access Denied" }');
} else {
    $un = $_SERVER["PHP_AUTH_USER"];
    $pw = $_SERVER['PHP_AUTH_PW'];

    if($un != "tda" && $pw != "d8Ef6!dGG_pv") {
        http_response_code(401);
        die('{ "error": "Access Denied" }');
    }
}



switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET": {
        // spuštění dotazů
        if(!$q) { // getall
            $query = "SELECT * FROM lecturers";
            $result = $conn->query($query);

            // pokud není žádný result, tak místo 404 to hodí 200 s prázdným arrayem
            if($result->num_rows == 0) {
                http_response_code(200);
                die("[]");
            }
        } else { // query
            $query = "SELECT * FROM lecturers WHERE uuid = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $q);

            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            // pokud není žádný result, tak místo 404 to hodí 200 s prázdným arrayem
            /*if($result->num_rows == 0) {
                http_response_code(200);
                exit;
            }*/
        }




        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        } else {
            http_response_code(404);
            exit;
        }

        $conn->close();



        // vyhození 404 pokud query lecturer neexistuje
        if(count($data) == 0) {
            http_response_code(404);
            exit;
        }



        // konvertování arrayů
        for ($i = 0; $i < count($data); $i++) {
            if($data[$i]["tags"] != null && $data[$i]["tags"] != "null") {
                $data[$i]["tags"] = json_decode($data[$i]["tags"], true);
            }

            if($data[$i]["contact"] != null && $data[$i]["contact"] != "null") {
                $data[$i]["contact"] = json_decode($data[$i]["contact"], true);
            }

            if($data[$i]["price_per_hour"] !== null) $data[$i]["price_per_hour"] = intval($data[$i]["price_per_hour"]);
        }



        // vypsání
        if($q) $data = $data[0];

        $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        echo $jsonData;
    } break;



    case "POST": {
        if($onlyGetMethod) {
            http_response_code(403);
            die("Access Denied");
        }

        $post = json_decode(file_get_contents('php://input'), true);
        $post_original = $post;
        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));



        // cheknutí jestli jsou všechna povinná pole zapsaný
        if(!isset($post["first_name"]) || !$post["first_name"] || $post["first_name"] == "null" || !isset($post["last_name"]) || !$post["last_name"] || $post["last_name"] == "null") {
            http_response_code(404);
            exit;
        }



        // zabránění html injectu
        $htmlspecialcharsRecursive = function (&$array) use (&$htmlspecialcharsRecursive) {
            foreach ($array as $key => &$value) {
                if (is_array($value)) {
                    $htmlspecialcharsRecursive($value);
                } elseif (is_string($value)) {
                    $value = htmlspecialchars($value);
                }
            }
        };

        $htmlspecialcharsRecursive($post);



        $post["mobilenumbers"] = implode(",", $post["contact"]["telephone_numbers"]);
        $post["emails"] = implode(",", $post["contact"]["emails"]);
        for ($i = 0; $i < count($post_original["tags"]); $i++) {
            $post_original["tags"][$i]["uuid"] = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
        }

        $tags = json_encode($post_original["tags"]);
        $contact = json_encode($post_original["contact"]);





        // dotaz
        $query = "INSERT INTO `lecturers` (`title_before`, `first_name`, `middle_name`, `last_name`, `title_after`, `bio`, `links`, `picture_url`, `claim`, `price_per_hour`, `mobilenumbers`, `emails`, `tags`, `contact`, `location`, `membersince`, `id`, `uuid`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, current_timestamp(), NULL, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssssssissssss",
            $post["title_before"],
            $post["first_name"],
            $post["middle_name"],
            $post["last_name"],
            $post["title_after"],
            $post_original["bio"],
            $post["links"],
            $post_original["picture_url"],
            $post_original["claim"],
            $post["price_per_hour"],
            $post["mobilenumbers"],
            $post["emails"],
            $tags,
            $contact,
            $post["location"],
            $uuid,
        );

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();


        $data = $post_original;



        if(!isset($data["uuid"]) || $data["uuid"] == "" || !$data["uuid"]) {
            $data["uuid"] = $uuid;
        }

        $conn->close();
        die(json_encode($data));
    } break;



    case "DELETE": {
        if($onlyGetMethod) {
            http_response_code(403);
            die("Access Denied");
        }
        if(!$q) {
            http_response_code(404);
            exit;
        }

        // zjistit z databáze, jestli ten lecturer vůbec existuje
        $query = "SELECT * FROM `lecturers` WHERE `lecturers`.`uuid` = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $q);

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if($result->num_rows == 0) {
            http_response_code(404);
            exit;
        }



        // smazání lecturera z databáze
        $query = "DELETE FROM `lecturers` WHERE `lecturers`.`uuid` = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $q);

        $stmt->execute();
        $stmt->close();

        $conn->close();
    } break;



    case "PUT": {
        if($onlyGetMethod) {
            http_response_code(403);
            die("Access Denied");
        }
        if(!$q) {
            http_response_code(404);
            exit;
        }

        $post = json_decode(file_get_contents('php://input'), true);
        $post_original = $post;
        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));



        // zabránění html injectu
        $htmlspecialcharsRecursive = function (&$array) use (&$htmlspecialcharsRecursive) {
            foreach ($array as $key => &$value) {
                if (is_array($value)) {
                    $htmlspecialcharsRecursive($value);
                } elseif (is_string($value)) {
                    $value = htmlspecialchars($value);
                }
            }
        };

        $htmlspecialcharsRecursive($post);



        // dotaz
        $query = "UPDATE `lecturers` SET `title_before` = ?, `first_name` = ?, `middle_name` = ?, `last_name` = ?, `title_after` = ?, `bio` = ?, `links` = ?, `picture_url` = ?, `claim` = ?, `price_per_hour` = ?, `contact` = ?, `mobilenumbers` = ?, `emails` = ?, `tags` = ?, `location` = ? WHERE `lecturers`.`uuid` = ?";

        $tags = json_encode($post_original["tags"]);
        $contact = json_encode($post_original["contact"]);
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssssssissssss",
            $post["title_before"],
            $post["first_name"],
            $post["middle_name"],
            $post["last_name"],
            $post["title_after"],
            $post_original["bio"],
            $post["links"],
            $post["picture_url"],
            $post["claim"],
            $post["price_per_hour"],
            $contact,
            $post["mobilenumbers"],
            $post["emails"],
            $tags,
            $post["location"],
            $q,
        );

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $conn->close();



        // konvertování arrayů + convert do jiných datových typů
        $data = $post_original;

        /*if($data["tags"] != null && $data["tags"] != "null") {
            $data["tags"] = json_decode($data["tags"], true);
        }

        if($data["contact"] != null && $data["contact"] != "null") {
            $data["contact"] = json_decode($data["contact"], true);
        }

        if($data["price_per_hour"] !== null) $data["price_per_hour"] = intval($data["price_per_hour"]);

        if(!isset($data["uuid"]) || $data["uuid"] == "" || !$data["uuid"]) {
            $data["uuid"] = $uuid;
        }*/

        for ($i = 0; $i < count($data["tags"]); $i++) {
            $data["tags"][$i]["uuid"] = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
        }



        die(json_encode($data));
    } break;
}

http_response_code(200);
