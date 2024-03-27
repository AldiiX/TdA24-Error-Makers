<?php
    include __DIR__ . "/components/functions.php";
    include __DIR__ . "/components/html_head.php";
    include __DIR__ . "/components/header.php";
    include __DIR__ . "/components/footer.php";
    include __DIR__ . "/components/Modals.php";
    include __DIR__ . "/components/page_transition_animation.php";
    \Functions\Util::startSession();



    $q = $_GET["uuid"] ?? null;

    if($q == null) {
        header("Location: /lecturers");
        exit;
    }



    // Získání dat z databáze podle parametru q
    use \Functions\Database as db;
    use \Functions\LecturerPage as Core;

    $conn = db::getDatabase();
    $query = "SELECT * FROM lecturers WHERE (id = ? OR uuid = ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $q, $q);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 0) {
        header("Location: /error?c=404&msg=Učitel nebyl nalezen.");
        exit;
    }

    $data[] = array();
    foreach ($result->fetch_assoc() as $column => $value) {
        $data[$column] = $value;
    }
?>

<!doctype html>
<html lang="en">
    <head>
        <?php echo generateHead() ?>
        <title><?php echo $data["first_name"] . " " . ($data["middle_name"] ? $data["middle_name"] . " ": "") . $data["last_name"] ?> • Teacher Digital Agency</title>
    </head>
    <body id="LECTURER">
        <?php insertTransitionAnimation(); ?>
        <?php insertHeader() ?>



        <?php Modals::insertModalBlurElement(); ?>

        <div id='modal-lecturercalendar' class='modal'>
            <div class='top'>
                <div class='closeicon' onclick="Web.Modals.openModal(null)"></div>
                <p>Rezervace učitele</p>
            </div>

            <div class="bottom">
                <?php if((\Functions\Database::isUserLoggedIn() && !empty($_SESSION["userinfo"]["mobilenumbers"]) && !empty($_SESSION["userinfo"]["email"]) && !empty($_SESSION["userinfo"]["first_name"]) && !empty($_SESSION["userinfo"]["last_name"])) || !\Functions\Database::isUserLoggedIn()  ) { ?>
                    <div class="month-controls">
                        <div class="back" onclick="Web.LecturerCalendar.setMonth('previous')"></div>
                        <p class="maintext" id="modal-lecturercalendar-monthcontrols-maintext" oninput="Web.LecturerCalendar.render()">
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
                        <div class="forward" onclick="Web.LecturerCalendar.setMonth('coming')"></div>
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
                <?php } else if(\Functions\Database::isUserLoggedIn() && (empty($_SESSION["userinfo"]["mobilenumbers"]) || empty($_SESSION["userinfo"]["email"]) || empty($_SESSION["userinfo"]["first_name"]) || empty($_SESSION["userinfo"]["last_name"]))) {?>
                    <p class="err">
                        Pro rezervaci učitele musíte mít na profilu vyplněné jméno, příjmení, telefonní číslo a email.
                        <a href="/account">Pojďme to napravit!</a>
                    </p>
                <?php } ?>
            </div>
        </div>



        <section class="main pb">
            <div class="table">
                <div class="left">

                    <!-- Jméno + tituly -->
                    <div>
                        <?php if($data["title_before"] != "") { ?>
                            <h1 class="mainh1 title t1"><?php echo $data["title_before"] ?></h1>
                        <?php } ?>

                        <h1 class="mainh1 name"><?php echo $data["first_name"] . " " . ($data["middle_name"] ? $data["middle_name"] . " ": "") . $data["last_name"] ?></h1>

                        <?php if($data["title_after"] != "") { ?>
                            <h1 class="mainh1 title t2"><?php echo $data["title_after"] ?></h1>
                        <?php } ?>
                    </div>




                    <!-- BIO -->
                    <p class="bio"><?php echo $data["bio"] ?></p>






                    <!-- Kontakt, informace, linky, tagy -->
                    <div class="cilt" style="">

                        <!-- Flex s kontaktem, informacemi a linky -->
                        <div class="cil" style="">

                            <!-- Kontakt -->
                            <div class="contact">
                                <p class="c">Kontakt</p>

                                <div class="child">
                                    <?php if($data["mobilenumbers"] != "") { ?>
                                        <div class="image" style="background-image: url(/images/icons/phone.svg)"></div>
                                        <div class="text">
                                            <?php echo Core::renderContacts($data["mobilenumbers"]); ?>
                                        </div>
                                    <?php } ?>
                                </div>

                                <div class="child" style="display: flex">
                                    <?php if($data["emails"] != "") { ?>
                                        <div class="image" style="background-image: url(/images/icons/email.svg)"></div>
                                        <div class="text">
                                            <?php echo Core::renderContacts($data["emails"]); ?>
                                        </div>
                                    <?php } ?>
                                </div>

                                <?php if (
                                    (
                                        ((isset($_SESSION["userinfo"]["teacherinfo"]["uuid"]) && $_SESSION["userinfo"]["teacherinfo"]["uuid"] != $data["uuid"])
                                            && (!empty($data["mobilenumbers"]) || !empty($data["emails"])))
                                        || (!isset($_SESSION["userinfo"]["teacherinfo"]["uuid"]) && (!empty($data["mobilenumbers"]) || !empty($data["emails"])))
                                    )
                                ) {
                                    ?>
                                    <div style="width: 100%; height: 2px; background-color: rgba(255, 255, 255, 0.1); margin: 10px 0"></div>
                                <?php } ?>

                                <?php if(!isset($_SESSION["userinfo"]["teacherinfo"]["uuid"]) || ($_SESSION["userinfo"]["teacherinfo"]["uuid"] != $data["uuid"])) { ?>
                                <button class="rezervacebutton button-primary" onclick="Web.Modals.openModal('lecturercalendar')">Rezerovovat</button>
                                <?php } ?>
                            </div>



                            <!-- Linky -->
                            <?php if($data["links"] != "") { ?>
                                <div class="contact">
                                    <p class="c">Odkazy</p>

                                    <div class="child">
                                        <div class="image"></div>
                                        <div class="text">

                                        </div>
                                    </div>
                                </div>
                            <?php } ?>



                            <!-- Ostatní info -->
                            <?php if($data["price_per_hour"] != "" || $data["location"] != "" || $data["membersince"] != "") { ?>
                            <div class="contact">
                                <p class="c">Informace</p>

                                <?php if($data["price_per_hour"] != "") { ?>
                                <div class="child" title="Cena za hodinu">
                                        <div class="image" style="background-image: url(/images/icons/money.svg)"></div>
                                        <div class="text">
                                            <p><?php echo $data["price_per_hour"] . " Kč/h"?></p>
                                        </div>
                                </div>
                                <?php } ?>

                                <?php if($data["location"] != "") { ?>
                                    <div class="child" title="Lokace">
                                        <div class="image" style="background-image: url(/images/icons/location.svg)"></div>
                                        <div class="text">
                                            <p><?php echo $data["location"] ?></p>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if($data["membersince"] != "") { ?>
                                    <div class="child" title="Člen od">
                                        <div class="image" style="background-image: url(/images/icons/member.svg)"></div>
                                        <div class="text">
                                            <p><?php echo date("d. m. Y", strtotime($data["membersince"])) ?></p>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php } ?>
                        </div>



                        <!-- Tagy -->
                        <?php if(false) {//if($data["tags"] != "") { ?>
                            <div class="contact tags">
                                <p class="c">Tagy</p>

                                <div class="tagsflex">
                                    <?php echo Core::getDescTags($data["tags"]);  ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="right">
                    <div class="avatar" style="<?php if($data["picture_url"]) echo 'background-image: url(' . $data["picture_url"] . ')' ?>"></div>
                </div>
            </div>
        </section>



        <?php insertFooter() ?>
    </body>

    <script src="<?php echo \Functions\Util::getNestingLevelDir() . 'scripts/script.js'?>"></script>
</html>