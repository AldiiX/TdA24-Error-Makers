<?php
    include_once(__DIR__ . "/components/html_head.php");
    include_once(__DIR__ . "/components/header.php");
    include_once(__DIR__ . "/components/footer.php");
    include_once(__DIR__ . "/components/functions.php");
    include_once(__DIR__ . "/components/page_transition_animation.php");


    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
?>





<!doctype html>
<html lang="en">
    <head>
        <?php echo generateHead() ?>
        <title>Vytvořit Aktivitu • Teacher Digital Agency</title>
    </head>
    <body id="ACTIVITY">


    <?php insertTransitionAnimation(); ?>
    <?php insertHeader() ?>



    <section class="main pb">
        <h1 class="mainh1">Přidat aktivitu</h1>

        <div id="activity">
            <div class="head">
                <h1>&nbsp;</h1>
            </div>
            <div class="content"> <!-- udělej tohle flex -->
                <div class="nazvy">
                    <!-- menu s názvy blokama -->
                    <a onclick="Web.selectActivityBlock('objectives')">Cíl</a>
                    <a onclick="scrollIntoView('pocet')">Počet lidí</a>
<!--                    <a onclick="scrollIntoView('classStructure')">Class structure</a>-->
                    <a onclick="scrollIntoView('description')">Popis</a>
                    <a onclick="scrollIntoView('edLevel')">Edukační level</a>
                    <a onclick="scrollIntoView('tools')">Nástroje</a>
                    <a onclick="scrollIntoView('homePreparation')"">Domácí příprava</a>
                    <a onclick="Web.selectActivityBlock('instructions')">Instrukce</a>
<!--                    <a onclick="scrollIntoView('agenda')">Agenda</a>-->
                    <a onclick="scrollIntoView('links')">Odkazy</a>
<!--                    <a onclick="scrollIntoView('gallery')">Galerie</a>-->
                </div>
                
                <form method="post" id="createactivityform">
                    <div class="bloky">
                        <!-- obsah bloků -->
                        <div id="objectives">
                            <h2>Cíl</h2>
                            <input type="text" required>
                        </div>
                        <div id="pocet">
                            <h2>Počet lidí</h2>
                            <input type="number" required>
                        </div>
    <!--                    <div id="structure">-->
    <!--                        <h2>Class structure</h2>-->
    <!--                    </div>-->
                        <div id="description">
                            <h2>Popis</h2>
                            <input type="text" required>
<!--                            <textarea form="createactivityform" name="" id="" cols="30" rows="10"></textarea>-->
                        </div>
                        <div id="edLevel">
                            <h2>Edukační level</h2>
                            <select required>
                                <option value="1">1. stupeň</option>
                                <option value="2">2. stupeň</option>
                                <option value="3">3. stupeň</option>
                            </select>
                        </div>
                        <div id="tools">
                            <h2>Nástroje</h2>
                            <input type="text" required>
                        </div>
                        <div id="homePreparation">
                            <h2>Domácí příprava</h2>
                            <input type="text" required>
                        </div>
                        <div id="instructions">
                            <h2>Instrukce</h2>
                            <input type="text" required>
                        </div>
<!--                        <div id="agenda">-->
<!--                            <input type="text">-->
<!--                            <h2>Agenda</h2>-->
<!--                        </div>-->
                        <div id="links">
                            <h2>Odkazy</h2>
                            <input type="text" required>
                        </div>
<!--                        <div id="gallery">-->
<!--                            <h2>Galerie</h2>-->
<!--                        </div>-->
                    </div>
                </form>
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