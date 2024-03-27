<?php


namespace Functions {

    use Cassandra\Date;
    use JetBrains\PhpStorm\NoReturn;



    // statické classy
    class Util {

        public static function getNestingLevel(): int {
            $currentPath = $_SERVER['REQUEST_URI'];
            $pathSegments = explode('/', trim($currentPath, '/'));
            $pathSegments = array_filter($pathSegments);
            return count($pathSegments);
        }

        public static function getNestingLevelDir(): string {
            $level = self::getNestingLevel();
            $output = "";

            if($level < 1) return "./";
            if($level == 1) return "../";
            for($i = 1; $i < $level; $i++) $output .= "../";

            return $output;
        }

        public static function getCurrentPage(): string {
            return (parse_url($_SERVER['REQUEST_URI']))['path'];
        }

        public static function startSession(): void {
            if(session_status() == PHP_SESSION_ACTIVE) return;

            //$currentDomain = preg_replace('/^(www|beta|old)\./i', '', $_SERVER['HTTP_HOST']);
            //session_set_cookie_params(0, '/', '.' . $currentDomain);
            session_start();
        }

        #[NoReturn]
        public static function sendError(string $msg = "", int $code = 500): void {
            ob_clean();
            Database::logoutUser();
            header("Location: " . "/error.php?" . ($msg ? "msg=" . $msg . "&" : "") . ($code ? "c=" . $code : ""));
            exit();
        }

        public static function stringContainsSpecialCharacters(string $string, string $regex = '/[<>!@#$%^&*(),.+?":{}|_-]/'): bool {
            return preg_match($regex, $string) == 1;
        }

        /*public static function getConfig(): array {
            return json_decode('
                {
                    
                }
            ', true);
        }*/

        public static function generateUUID(): string {
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
        }
    }





    class Pages {
        public static function getAllPages(): mixed {
            $json = file_get_contents(__DIR__ . "/pages.json");
            return json_decode($json, true);
        }
    }





    class LecturerPage {
        public static function getDescTags(string $tags): string {
            $output = "";
            $tags = json_decode($tags);

            foreach ($tags as $i) {
                $output .= '
                        <div>
                            <p>' . $i->name . '</p>
                        </div>
                ' . "\n";
            }

            return $output;
        }

        public static function renderContacts(string $contacts): string {
            $contactsarray = explode(',', $contacts);

            if (!$contactsarray) {
                return '<p>' . $contacts . '</p>';
            }

            $o = "";
            foreach ($contactsarray as $c) {
                $o .= "<p>{$c}</p>\n";
            }

            return $o;
        }

        public static function getLinks(): string {
            $output = "";

            return $output;
        }
    }





    class IndexPage {
        public static function getStatistics(): array {
            $arr = [];
            $conn = Database::getDatabase();



            // query lecturers
            $query = "SELECT * FROM lecturers";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $arr["lecturers"] = $stmt->get_result()->num_rows;

            // query users
            $query = "SELECT * FROM users";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $arr["users"] = $stmt->get_result()->num_rows;



            // formátování
            if($arr["lecturers"] > 10) $arr["lecturers"] = floor($arr["lecturers"] / 5) * 5 . "+";
            if($arr["users"] > 10) $arr["users"] = floor($arr["users"] / 5) * 5 . "+";



            return $arr;
        }
    }





    class LecturersPage {


        private static function getLecturersArray(): \mysqli_result|null {
            $conn = Database::getDatabase();

            $query = "SELECT * FROM lecturers";
            $stmt = $conn->prepare($query);

            // Vykonání dotazu
            $stmt->execute();

            // Získání výsledků dotazu
            $result = $stmt->get_result();

            if($result->num_rows == 0) return null;
            return $result;
        }



        public static function renderLecturers(array $queryTags = null, int $minPrice = 0, int $maxPrice = 10000, string $queryCity = null): void {

            function renderLocationAndPrice(string $location, string $price): string {
                $output = "";

                if($location != "") $output .= '
                    <div class="locpr">
                        <p>' . $location . '</p>
                    </div>
                ';

                if($price != "") $output .= '
                    <div class="locpr">
                        <p>' . $price . " Kč/h". '</p>
                    </div>
                ';

                return $output;
            }



            $lecturers = self::getLecturersArray();
            $output = "";
            if(!$lecturers) return;

            while ($row = $lecturers->fetch_assoc()) {

                // pokud jsou omezený tagy
                if ($queryTags) {
                    $tags = $row["tags"];

                    $tagsArray = is_array($tags) ? $tags : json_decode($tags, true);

                    $containsAllTags = true;
                    foreach ($queryTags as $queryTag) {
                        $tagFound = false;

                        foreach ($tagsArray as $tag) {
                            if (strcasecmp($tag["name"], $queryTag["name"]) === 0) {
                                $tagFound = true;
                                break;
                            }
                        }

                        if (!$tagFound) {
                            $containsAllTags = false;
                            break;
                        }
                    }

                    if (!$containsAllTags) {
                        continue;
                    }
                }

                // pokud jsou omezený peníze
                if(!($row["price_per_hour"] >= $minPrice && $row["price_per_hour"] <= $maxPrice)) continue;

                // pokud je omezení na město
                if($queryCity) {
                    if(strtolower($row["location"]) != strtolower($queryCity)) continue;
                }



                $title_before = $row['title_before'] != "" ? $row['title_before'] . "&nbsp;" : "";
                $firstname = $row['first_name'];
                $middlename = $row['middle_name'];
                $surname = $row['last_name'];
                $title_after = $row["title_after"] != "" ? ", " . $row['title_after'] : "";
                $avatarurl = $row['picture_url'];
                $claim = $row['claim'];
                $bio = $row['bio'];
                $tags = $row['tags'] ?? null;
                $location = $row['location'] ?? "";
                $price = $row['price_per_hour'] ?? "";
                $uuidOrId = $row['uuid'] ?? $row['id'] ?? null;



                $output .= "
                    <a class='vizitka' href='/lecturer/$uuidOrId'>
                        <div " . ($avatarurl != '' ? ('style=\'background-image: url(' . $avatarurl . ')\'') : '') . " id='pfp'></div>
                        <div class='description'>
                            <h1>
                                <span class='title t1'>$title_before</span>
                                <span class='name'>$firstname $middlename $surname</span>
                                <span class='title t2'>$title_after</span>
                            </h1>
        
                            <p id='claim'>$claim</p>
        
                            
        
                            " . ($tags ? ('
                            <div id="tags">
                                ' . LecturerPage::getDescTags($tags) . '
                            </div>') : "") . "
        
        
        
                            " . ($location != "" || $price != "" ? ('
                            <div id="tags">
                                ' . renderLocationAndPrice($location, $price) . '
                            </div>') : "") . "
                        </div>
                    </a>
                ";
            }

            echo $output;
        }
    }





    class AccountPage {
        public static function getTagsValue(array $tags): string {
            $arr = [];

            $i = 0;
            foreach ($tags as $tag) {
                $arr[$i] = $tag["name"];
                $i++;
            }

            return implode(",", $arr);
        }
    }





    class Database {
        public static function getDatabase(): ?\mysqli {
            ini_set('mysqli.connect_timeout', 10);





            $host = "127.0.0.1:3306";
            $dbname = "db";
            $username = "admin";
            $password = "password";



            try {
                $conn = new \mysqli($host, $username, $password, $dbname);
            } catch (\Exception $e) {
                //die($e);
                Util::sendError("Databáze nedokázala odpovědět na request.", 503);
            }


            return $conn;
        }

        public static function logoutUser(): void {
            Util::startSession();
            $_SESSION["userinfo"] = null;
        }

        public static function authUser($username, $password): bool {
            $conn = self::getDatabase();
            if(!$conn) return false;

            $query = "SELECT * FROM users WHERE BINARY (username = ? OR email = ?) AND BINARY password = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $username, $username, $password);

            // Vykonání dotazu
            $stmt->execute();

            // Získání výsledků dotazu
            $result = $stmt->get_result();


            // Kontrola počtu řádků výsledku
            if ($result->num_rows === 1) {
                // Získání řádku výsledku
                $row = $result->fetch_assoc();

                Util::startSession();

                // nastaví se do session proměnných položky z databáze
                $_SESSION["userinfo"] = array();
                foreach ($row as $column => $value) {
                    $_SESSION["userinfo"][$column] = $value;
                }

                // nastaví se do teacherinfo položky z databáze, pokud uživatel je spojený s teacher databází
                if($row["teacherid"] != "") {
                    $tquery = "SELECT * FROM lecturers WHERE id = ?";
                    $tstmt = $conn->prepare($tquery);
                    $tstmt->bind_param("i", $row["teacherid"]);
                    $tstmt->execute();
                    $trow = $tstmt->get_result()->fetch_assoc();

                    $_SESSION["userinfo"]["teacherinfo"] = array();
                    foreach ($trow as $column => $value) {
                        $_SESSION["userinfo"]["teacherinfo"][$column] = $value;
                    }
                }

                return true; // Přihlašovací údaje jsou platné
            }



            return false; // Přihlašovací údaje jsou neplatné
        }

        public static function reauthUser(): ?bool {
            Util::startSession();

            if(!isset($_SESSION["userinfo"])) return null;

            if(self::authUser($_SESSION["userinfo"]["username"], $_SESSION["userinfo"]["password"])) return true;
            else { self::logoutUser(); return false; }
        }

        public static function isUserLoggedIn(): bool {
            if(self::reauthUser()) return true;
            else return false;
        }

        public static function encryptPassword(string $password): string {
            if(!$password) return "";
            return hash("sha512", $password) . hash("md5", $password[0] . $password[2]);
        }

        public static function isUsernameTaken($username): bool {
            $conn = self::getDatabase();
            $query = "SELECT * FROM users WHERE username = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $username);


            $stmt->execute();
            $userExists = $stmt->get_result()->num_rows > 0;

            $stmt->close();
            $conn->close();

            return $userExists;
        }

        public static function isEmailTaken($email): bool {
            return self::isUsernameTaken($email);
        }

        public static function createUser(string $username, string $email, string $password): bool {
            $conn = self::getDatabase();
            $membersince = \date("Y-m-d");
            $command = "INSERT INTO users (username, email, password, membersince) VALUE(?,?,?,?)";
            $stmt = $conn->prepare($command);
            $stmt->bind_param("ssss", $username, $email, $password, $membersince);

            // Vykonání dotazu
            $success = $stmt->execute();

            $conn->close();
            $stmt->close();

            return $success;
        }
    }
}
