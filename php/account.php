<?php
    include_once(__DIR__ . "/components/html_head.php");
    include_once(__DIR__ . "/components/header.php");
    include_once(__DIR__ . "/components/footer.php");
    include_once(__DIR__ . "/components/functions.php");
    include_once(__DIR__ . "/components/page_transition_animation.php");
    use \Functions\Database as db;
    \Functions\Util::startSession();
    $errorMsg = "";
    $successMsg = "";
    if(isset($_SESSION["userinfo"]["teacherinfo"])) $selectedWindow = $_GET["window"] ?? "calendar";
    else $selectedWindow = $_GET["window"] ?? "settings";



    // region post
    if(isset($_POST['submitpassword'])) {
        $hashedPassword = \Functions\Database::encryptPassword($_POST["input"]);
    } else if(isset($_POST["save"])) {

        db::reauthUser();
        $conn = db::getDatabase();


        //kontrola povinných polí
        if(
                isset($_SESSION["userinfo"]["teacherinfo"]) && (
                    (
                     $_POST["first_name"] == "" ||
                     $_POST["first_name"] == null
                    ) ||
                    (
                     $_POST["last_name"] == "" ||
                     $_POST["last_name"] == null
                    )
                )
        ) {
            $errorMsg .= "Učitelský účet musí mít jméno a příjmení!<br>";
        } else if(\Functions\Util::stringContainsSpecialCharacters($_POST["first_name"]) || \Functions\Util::stringContainsSpecialCharacters($_POST["last_name"])) {
            $errorMsg .= "Jméno ani příjmení nesmí obsahovat speciální znaky!<br>";
        } else if((isset($_POST["title_before"]) && \Functions\Util::stringContainsSpecialCharacters($_POST["title_before"], '/[<>!@#$%^&*(),+?":{}_-]/')) || (isset($_POST["title_after"]) && \Functions\Util::stringContainsSpecialCharacters($_POST["title_after"], '/[<>!@#$%^&*(),+?":{}_-]/'))) {
            $errorMsg .= "Tituly nesmí obsahovat speciální znaky!<br>";
        } else { // pokud nejsou žádné errory
            $post = array_map('htmlspecialchars', $_POST);
            if(isset($post["tags"])) {
                $post["tags"] = implode(",", array_map('trim', explode(",", $post["tags"]))); // string se splitne na array pomocí , a všechny elementy v tom arrayi se trimnou, pak se to zpátky převede na string
                $newTagsArray = [];

                $i = 0;
                foreach (explode(",", $post["tags"]) as $tagname) {
                    $newTagsArray[$i] = [ "name" => $tagname, "uuid" => vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4))];
                    $i++;
                }

                $post["tags"] = json_encode($newTagsArray);
            }


            // změna učitelských polí
            if(isset($_SESSION["userinfo"]["teacherinfo"])) {
                $query = "
                    UPDATE `lecturers` 
                            SET 
                               `title_before` = ?,
                               `middle_name` = ?, 
                               `title_after` = ?, 
                               `bio` = ?, 
                               `location` = ?, 
                               `claim` = ?, 
                               `price_per_hour` = ?,
                               `tags` = ?,
                               `emails` = ?
                            WHERE `id` = ?
                ";

                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssssssissi",
                      $post["title_before"],
                    $post["middle_name"],
                    $post["title_after"],
                    $post["bio"],
                    $post["location"],
                    $post["claim"],
                    $post["price_per_hour"],
                    $post["tags"],
                    $post["emails"],

                    $_SESSION["userinfo"]["teacherid"])
                ;

                $stmt->execute();
                $stmt->close();
            }



            // změna uživatelských polí
            $birthday = $_POST["birthday"] != "" ? $_POST["birthday"] : null;
            $gender = !empty($_POST["genders"]) ? $_POST["genders"] : null;
            if($gender == "NULL") $gender = null;

            $query = "
                UPDATE `users` 
                        SET 
                           `first_name` = ?,
                           `last_name` = ?, 
                           `birthday` = ?, 
                           `gender` = ?, 
                           `picture_url` = ?,
                           `mobilenumbers` = ?
                        WHERE `username` = ? AND `password` = ?
            ";


            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssssss",
                $post["first_name"],
                $post["last_name"],
                $birthday,
                $gender,
                $post["picture_url"],
                $post["mobilenumbers"],

                $_SESSION["userinfo"]["username"],
                $_SESSION["userinfo"]["password"])
            ;

            $stmt->execute();

            $stmt->close();
            $conn->close();

            $successMsg = "Úspěšně uloženo!";
            $selectedWindow = "settings";
        }
    }
    //endregion


    if(!db::isUserLoggedIn()) {
        header("Location: /login?rt=/account");
        exit;
    }
?>

<!doctype html>
<html lang="en">
<head>
    <?php echo generateHead() ?>
    <title>Můj účet • Teacher Digital Agency</title>
</head>
<body id="ACCOUNT">


    <?php insertTransitionAnimation(); ?>
    <?php insertHeader() ?>



    <section class="main pb">
        <h1 class="mainh1">Můj účet</h1>
        <div class="logout-button" onclick="location.href='/logout'">
            <div></div>
            <p>Odhlásit se</p>
        </div>

        <?php if(isset($_SESSION["userinfo"]["teacherinfo"])) { ?>
        <div class="window-selection">
            <p class="window c" onclick="Web.accountSelectWindow('calendar')">Kalendář</p>
            <p class="window s" onclick="Web.accountSelectWindow('settings')">Nastavení</p>
        </div>

        <div id="lecturer-calendar" <?php if(!empty($_SESSION["userinfo"]["teacherinfo"]) && $selectedWindow == "settings") echo 'style="display:none"' ?>>
            <div class="bottom">
                <div class="month-controls">
                    <div class="back" onclick="Web.LecturerCalendarInAccount.setMonth('previous')"></div>
                    <p class="maintext" id="lecturercalendar-monthcontrols-maintext" oninput="Web.LecturerCalendarInAccount.render()">
                        <?php

                        $months = [
                            "January" => "Leden",
                            "February" => "Únor",
                            "March" => "Březen",
                            "April" => "Duben",
                            "May" => "Květen",
                            "June" => "Červen",
                            "July" => "Červenec",
                            "August" => "Srpen",
                            "September" => "Září",
                            "October" => "Říjen",
                            "November" => "Listopad",
                            "December" => "Prosinec"
                        ];

                        $currentMonth = date("F");
                        $localizedMonth = $months[$currentMonth];
                        $currentYear = date("Y");

                        echo "$localizedMonth $currentYear";
                        ?>
                    </p>
                    <div class="forward" onclick="Web.LecturerCalendarInAccount.setMonth('coming')"></div>
                </div>

                <div class="flex">
                    <div id="calendar">
                        <table style="display: none">
                            <thead>
                            <tr>
                                <th>Po</th>
                                <th>Út</th>
                                <th>St</th>
                                <th>Čt</th>
                                <th>Pá</th>
                                <th>So</th>
                                <th>Ne</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr></tr>
                            <tr></tr>
                            <tr></tr>
                            <tr></tr>
                            <tr></tr>
                            <tr></tr>
                            </tbody>
                        </table>

                        <div class="loadingdiv"></div>
                    </div>
                    <div id="dayprops">
                        <p class="h">Vybraný den</p>

                        <div class="days-parent">

                        </div>
                    </div>
                </div>

                <div class="confirmation">
                    <div class="center">

                    </div>
                </div>
            </div>
        </div>

        <p class="exportcal" onclick="Web.LecturerCalendarInAccount.downloadCalendar()" <?php if(!empty($_SESSION["userinfo"]["teacherinfo"]) && $selectedWindow == "settings") echo 'style="display:none"' ?>>Exportovat kalendář</p>
        <?php } ?>

        <div class="userinfointerface" <?php if((isset($_SESSION["userinfo"]["teacherinfo"]) && $selectedWindow != "settings") || $selectedWindow == "calendar") echo 'style="display:none"' ?>>
            <div class="left">
                <?php if($_SESSION["userinfo"]["picture_url"] != null) { ?>
                    <div class="avatar" style="background-image: url('<?php echo $_SESSION["userinfo"]["picture_url"] ?>')"></div>
                <?php } else { ?>
                    <div class="avatar">
                        <p class="page-center" style="text-align: center;color:black;text-transform: uppercase;font-size:80px;margin-top:-2px"><?php echo $_SESSION["userinfo"]["username"][0] ?></p>
                    </div>
                <?php } ?>

                <?php if($_SESSION["userinfo"]["adminaccount"] == 1) { ?>
                    <p style="text-align: center; margin-bottom: 25px; color: var(--color-main)">(Admin account)</p>
                <?php } ?>

                <div class="info">
                    <div class="pair">
                        <p>Uživatelské jméno</p>
                        <p><?php echo $_SESSION["userinfo"]["username"] ?></p>
                    </div>

                    <div class="pair">
                        <p>Celé jméno</p>
                        <p><?php if($_SESSION["userinfo"]["first_name"] == "" || $_SESSION["userinfo"]["last_name"] == "") echo "Nezadáno"; else echo $_SESSION["userinfo"]["first_name"] . " " . $_SESSION["userinfo"]["last_name"] ?></p>
                    </div>

                    <div class="pair">
                        <p>Email</p>
                        <p><?php echo $_SESSION["userinfo"]["email"] ?></p>
                    </div>

                    <div class="pair">
                        <p>Datum narození</p>
                        <p><?php if($_SESSION["userinfo"]["birthday"] == "") echo "Neurčeno"; else echo date("d. m. Y", strtotime($_SESSION["userinfo"]["birthday"])) ?></p>
                    </div>

                    <div class="pair">
                        <p>Pohlaví</p>
                        <p><?php
                            echo match ($_SESSION["userinfo"]["gender"]) {
                                "MALE" => "Muž",
                                "FEMALE" => "Žena",
                                "OTHER" => "Jiné",
                                default => "Neurčeno",
                            }; ?>
                   </div>
                </div>

                <p class="membersincetxt">&lt; členem od <?php echo date("d. m. Y", strtotime($_SESSION["userinfo"]["membersince"])) ?> &gt;</p>
            </div>

            <div>
                <div class="right">
                    <p class="nadpis">Změnit údaje</p>
                    <p class="errorp"><?php echo $errorMsg ?></p>
                    <p class="successp"><?php echo $successMsg ?></p>


                    <form method="post" class="accountsettings" id="accountform">

                        <!-- Tituly -->
                        <?php if(isset($_SESSION["userinfo"]["teacherinfo"])) { ?>
                            <div style="display: flex; gap: 0 20px; flex-wrap: wrap">
                                <div class="pair">
                                    <p>Titul před jménem</p>
                                    <input type="text" name="title_before" value="<?php echo $_SESSION["userinfo"]["teacherinfo"]["title_before"]?>">
                                </div>
                                <div class="pair">
                                    <p>Titul za jménem</p>
                                    <input type="text" name="title_after" value="<?php echo $_SESSION["userinfo"]["teacherinfo"]["title_after"]?>">
                                </div>
                            </div>
                        <?php } ?>



                        <!-- Jméno a příjmení -->
                        <div style="display: flex; gap: 0 20px; flex-wrap: wrap">

                            <div class="pair">
                                <p>Jméno</p>
                                <input style="width: 100px" type="text" name="first_name" value="<?php echo $_SESSION["userinfo"]["first_name"]?>">
                            </div>

                            <?php if(isset($_SESSION["userinfo"]["teacherinfo"])) { ?>
                                <div class="pair">
                                    <p>2. jméno</p>
                                    <input style="width: 100px" type="text" name="middle_name" value="<?php echo $_SESSION["userinfo"]["teacherinfo"]["middle_name"]?>">
                                </div>
                            <?php } ?>

                            <div class="pair">
                                <p>Příjmení</p>
                                <input type="text" name="last_name" value="<?php echo $_SESSION["userinfo"]["last_name"]?>">
                            </div>
                        </div>



                        <!-- Tel. čísla -->
                        <?php if(!isset($_SESSION["userinfo"]["teacherinfo"])) { ?>
                        <div class="pair" style="width: 250px">
                            <p>Tel. čísla (oddělujte čárkou)</p>
                            <input style="width: 100%" name="mobilenumbers" placeholder="+420 123 456 789,+420 987 654 321" type="text" value="<?php echo $_SESSION["userinfo"]["mobilenumbers"]?>">
                        </div>
                        <?php } ?>



                        <!-- Datum narození + Lokace -->
                        <div style="display: flex; gap: 0 20px; flex-wrap: wrap">
                            <div class="pair">
                                <p>Datum narození</p>
                                <input type="date" min="1900-01-01" max="2100-01-01" name="birthday" value="<?php echo $_SESSION["userinfo"]["birthday"]?>">
                            </div>

                            <?php if(isset($_SESSION["userinfo"]["teacherinfo"])) { ?>
                                <div class="pair">
                                    <p>Lokace</p>
                                    <input type="text" name="location" value="<?php echo $_SESSION["userinfo"]["teacherinfo"]["location"]?>">
                                </div>
                            <?php } ?>
                        </div>



                        <!-- Avatar a pohlaví -->
                        <div style="display: flex; gap: 0 20px; flex-wrap: wrap">
                            <!-- Pohlaví -->
                            <div class="pair">
                                <p><label for="genders">Pohlaví:</label></p>
                                <select name="genders" id="genders">
                                    <option <?php if($_SESSION["userinfo"]["gender"] == "MALE") echo "selected" ?> value="MALE">Muž</option>
                                    <option <?php if($_SESSION["userinfo"]["gender"] == "FEMALE") echo "selected" ?> value="FEMALE">Žena</option>
                                    <!--                            <option value="MALE">Transexuální Muž</option>-->
                                    <!--                            <option value="FEMALE">Transexuální Žena</option>-->
                                    <!--                            <option value="MALE">Metrosexuální muž</option>-->
                                    <!--                            <option value="FEMALE">Metrosexuální žena</option>-->
                                    <!--                            <option value="FEMALE">Majonéza</option>-->
                                    <!--                            <option value="MALE">Muž, který se zajímá o to, jaký je to být žena</option>-->
                                    <!--                            <option value="FEMALE">Žena, která se zajímá o to, jaký je to být muž</option>-->
                                    <!--                            <option value="MALE">Muž, ale je tlustý, takže má prsa</option>-->
                                    <!--                            <option value="MALE">Taška z Wallmartu</option>-->
                                    <!--                            <option value="MALE">Hermafrodit, ale převažují mužské vlastnosti</option>-->
                                    <!--                            <option value="FEMALE">Hermafrodit, ale převažují ženské vlastnosti</option>-->
                                    <!--                            <option value="OTHER">Hermafrodit bez genderových vlastností</option>-->
                                    <!--                            <option value="MALE">Měsíc</option>-->
                                    <!--                            <option value="MALE">Siamské dvojče - Muž</option>-->
                                    <!--                            <option value="FEMALE">Siamské dvojče - Žena</option>-->
                                    <!--                            <option value="MALE">Pes</option>-->
                                    <!--                            <option value="MALE">Narozen bez přirození - Idenfikuje se jako muž</option>-->
                                    <!--                            <option value="FEMALE">Narozen bez přirození - Idenfikuje se jako žena</option>-->
                                    <!--                            <option value="FEMALE">Narozen bez přirození - Je na to pyšné</option>-->
                                    <!--                            <option value="MALE">Anděl</option>-->
                                    <!--                            <option value="OTHER">Umělá inteligence bez genderu</option>-->
                                    <!--                            <option value="MALE">Umělá inteligence - Muž</option>-->
                                    <!--                            <option value="FEMALE">Umělá inteligence - Žena</option>-->
                                    <!--                            <option value="FEMALE">Helikoptéra</option>-->
                                    <!--                            <option value="NULL">Žádné</option>-->
                                    <option <?php if($_SESSION["userinfo"]["gender"] == "OTHER") echo "selected" ?> value="OTHER">Jiné</option>
                                    <option <?php if($_SESSION["userinfo"]["gender"] == "NULL" || $_SESSION["userinfo"]["gender"] == "") echo "selected" ?> value="NULL">Neurčeno</option>
                                </select>
                            </div>



                            <!-- Avatar -->
                            <?php if(isset($_SESSION["userinfo"]["teacherinfo"]) || $_SESSION["userinfo"]["adminaccount"] == 1 || isset($_SESSION["userinfo"]["picture_url"])) { ?>
                                <div class="pair">
                                    <p>Avatar URL</p>
                                    <input type="text" name="picture_url" value="<?php echo $_SESSION["userinfo"]["picture_url"]?>">
                                </div>
                            <?php } ?>
                        </div>



                        <!-- Pro učitele -->
                        <?php if(isset($_SESSION["userinfo"]["teacherinfo"])) { ?>

                            <!-- BIO -->
                            <div class="pair">
                                <p>BIO</p>
                                <textarea class="biotextarea" onclick="Web.setElHeightToScrollHeight(this)" oninput="setElHeightToScrollHeight(this)" name="bio" form="accountform" placeholder="Nějaké pěkné informace :)" maxlength="1024"><?php echo $_SESSION["userinfo"]["teacherinfo"]["bio"]?></textarea>
                            </div>



                            <!-- Tagy -->
                            <div class="pair">
                                <p>Tagy (oddělujte čárkou)</p>
                                <input style="width: 100%" name="tags" placeholder="Java,C++,C#,MySQL,PHP" type="text" value="<?php echo \Functions\AccountPage::getTagsValue(json_decode($_SESSION["userinfo"]["teacherinfo"]["tags"], true)) ?>">
                            </div>



                            <div style="display: flex; gap: 0 20px; flex-wrap: wrap">
                                <!-- Emaily -->
                                <div class="pair" style="width: 250px">
                                    <p>Emaily (oddělujte čárkou)</p>
                                    <input style="width: 100%" name="emails" placeholder="<?php echo strtolower($_SESSION["userinfo"]["last_name"]) . "@gmail.com," . strtolower($_SESSION["userinfo"]["first_name"])[0] . strtolower($_SESSION["userinfo"]["last_name"]) . "@email.cz" ?>" type="text" value="<?php echo $_SESSION["userinfo"]["teacherinfo"]["emails"]?>">
                                </div>

                                <!-- Tel. čísla -->
                                <div class="pair" style="width: 250px">
                                    <p>Tel. čísla (oddělujte čárkou)</p>
                                    <input style="width: 100%" name="mobilenumbers" placeholder="+420 123 456 789,+420 987 654 321" type="text" value="<?php echo $_SESSION["userinfo"]["teacherinfo"]["mobilenumbers"]?>">
                                </div>
                            </div>



                            <!-- Cena za hodinu + Claim -->
                            <div style="display: flex; gap: 0 20px; flex-wrap: wrap">
                                <div class="pair">
                                    <p>Cena za hodinu</p>
                                    <input type="number" min="0" name="price_per_hour" value="<?php echo $_SESSION["userinfo"]["teacherinfo"]["price_per_hour"]?>">
                                </div>

                                <div class="pair">
                                    <p>Claim</p>
                                    <input style="width:220px" type="text" name="claim" value="<?php echo $_SESSION["userinfo"]["teacherinfo"]["claim"]?>">
                                </div>
                            </div>
                        <?php } ?>



                        <input type="submit" name="save" class="button-primary" value="Uložit">
                    </form>

                    <?php /* ZAŠIFROVÁNÍ HESLA */if(isset($_SESSION["userinfo"]["adminaccount"]) && $_SESSION["userinfo"]["adminaccount"] == 1) { ?>
                        <form method="post" style="margin-top: 40px;">
                            <p class="nadpis" style="margin-bottom: 40px">Admin Powers</p>
                            <p>Vytvořit zašifrované heslo</p>
                            <input type="text" name="input">
                            <input type="submit" name="submitpassword">
                        </form>
                        <p><i><?php echo $hashedPassword ?? ""?></i></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>



    <?php insertFooter() ?>

    <script src="<?php echo \Functions\Util::getNestingLevelDir() . 'scripts/script.js'?>"></script>
    <script>
        if (window.history.replaceState) window.history.replaceState( null, null, window.location.href );
    </script>
</body>
</html>