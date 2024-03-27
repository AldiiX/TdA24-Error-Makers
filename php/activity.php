<?php
    include_once(__DIR__ . "/components/html_head.php");
    include_once(__DIR__ . "/components/header.php");
    include_once(__DIR__ . "/components/footer.php");
    include_once(__DIR__ . "/components/functions.php");
    include_once(__DIR__ . "/components/page_transition_animation.php");


    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);


    \Functions\Util::startSession();
    $query = $_GET["q"] ?? null;

    $conn = \Functions\Database::getDatabase();
    $stmt = $conn->prepare("SELECT * FROM activities WHERE uuid = ?");
    $stmt->bind_param("s", $query);
    $stmt->execute();
    $result = $stmt->get_result();
    $activity = $result->fetch_assoc();

    if($activity == null) {
        header("Location: /activities");
        exit;
    }

    $data = $activity ?? null;
    $tools = json_decode($data["tools"], true);
    $homePreparation = json_decode($data["homePreparation"], true);
    $objectives = json_decode($data["objectives"], true);
    $edLevel = json_decode($data["edLevel"], true);
    $instructions= json_decode($data["instructions"], true);
    $agenda = json_decode($data["agenda"], true);
    $links = json_decode($data["links"], true);
    $gallery = json_decode($data["gallery"], true);


?>





<!doctype html>
<html lang="en">
    <head>
        <?php echo generateHead() ?>
        <title>Aktivita • Teacher Digital Agency</title>
    </head>
    <body id="ACTIVITY">


    <?php insertTransitionAnimation(); ?>
    <?php insertHeader() ?>



    <section class="main pb">
        <h1 class="mainh1">Aktivita</h1>

        <div id="activity">
            <div class="head">
                <h1><?php echo $data["activityName"] ?></h1>
            </div>
            <div class="content"> <!-- udělej tohle flex -->
                <div class="nazvy">
                    <!-- menu s názvy blokama -->
                    <a onclick="Web.selectActivityBlock('objectives')">Cíl</a>
                    <a onclick="Web.selectActivityBlock('pocet')">Počet lidí</a>
<!--                    <a onclick="Web.selectActivityBlock('classStructure')">Class structure</a>-->
                    <a onclick="Web.selectActivityBlock('description')">Popis</a>
                    <a onclick="Web.selectActivityBlock('edLevel')">Edukační level</a>
                    <a onclick="Web.selectActivityBlock('tools')">Nástroje</a>
                    <a onclick="Web.selectActivityBlock('homePreparation')"">Domácí příprava</a>
                    <a onclick="Web.selectActivityBlock('instructions')">Instrukce</a>
                    <a onclick="Web.selectActivityBlock('agenda')">Agenda</a>
                    <a onclick="Web.selectActivityBlock('links')">Odkazy</a>
                    <a onclick="Web.selectActivityBlock('gallery')">Galerie</a>
                </div>
                <div class="bloky">
                    <!-- obsah bloků -->
                    <div id="objectives">
                        <h2>Cíl</h2>
                        <p><?php echo $data["objectives"] ?></p>
                    </div>
                    <div id="pocet">
                        <h2>Počet lidí</h2>
                        <p><?php echo $data["lengthMin"]?> - <?php echo $data["lengthMax"]?></p>
                    </div>
<!--                    <div id="structure">-->
<!--                        <h2>Class structure</h2>-->
<!--                        <p>--><?php //echo $data["classStructure"]?><!--</p>-->
<!--                    </div>-->
                    <div id="description">
                        <h2>Popis</h2>
                        <p><?php echo $data["description"]?></p>
                    </div>
                    <div id="edLevel">
                        <h2>Edukační level</h2>
                        <p><?php echo $data["edLevel"] ?></p>
                    </div>
                    <div id="tools">
                        <h2>Nástroje</h2>
                        <p><?php echo implode(", ", json_decode($data["tools"], true)) ?></p>
                    </div>
                    <div id="homePreparation">
                        <h2>Domácí příprava</h2>
                        <p>
                            <?php
//                            foreach ($homePreparation as $preparation) {
//                                foreach ($preparation as $key => $value) {
//                                    echo $key . ": " . $value . "<br>";
//                                }
//                                echo "<br>"; // odděluje jednotlivé přípravy
//                            }
                            echo $data["homePreparation"];
                            ?>
                        </p>
                    </div>
                    <div id="instructions">
                        <h2>Instrukce</h2>
                        <p><?php echo $data["instructions"]?></p>
                    </div>
                    <div id="agenda">
                        <h2>Agenda</h2>
                        <p><?php echo $data["agenda"]?></p>
                    </div>
                    <div id="links">
                        <h2>Odkazy</h2>
                        <p><?php echo $data["links"]?></p>
                    </div>
                    <div id="gallery">
                        <h2>Galerie</h2>
                        <p><?php echo $data["gallery"][0] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <?php insertFooter() ?>

    <script>
        if (window.history.replaceState) window.history.replaceState( null, null, window.location.href );
    </script>
    <script src="/scripts/script.js"></script>
    </body>
</html>