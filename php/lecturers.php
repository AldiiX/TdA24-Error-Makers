<?php
    /*include("hello.php");
    exit;*/


    include_once(__DIR__ . "/components/html_head.php");
    include_once(__DIR__ . "/components/header.php");
    include_once(__DIR__ . "/components/footer.php");
    include_once(__DIR__ . "/components/functions.php");
    include_once(__DIR__ . "/components/page_transition_animation.php");
    use \Functions\Pages;
    \Functions\Util::startSession();
    $queryTags = null;
    $searchValueTags = "";
    $searchValueCity = null;
    $searchMinPrice = null;
    $searchMaxPrice = null;

    if(isset($_POST['search'])) {
        $queryTags = null;
        $searchValueTags = $_POST["tags"];
        $searchValueCity = $_POST["city"];
        $searchMinPrice = $_POST["minprice"];
        $searchMaxPrice = $_POST["maxprice"];
        $queryTags = explode(",", implode(",", array_map('trim', explode(",", $_POST["tags"]))));

        if($_POST["tags"] == "") {
            $queryTags = null;
        } else {
            $arr = [];
            for ($i = 0; $i < count($queryTags); $i++) {
                $arr[$i] = [ "name" => $queryTags[$i] ];
            }

            $queryTags = $arr;
        }



        // kontrola jestli je min a max price integery
        if (is_numeric($searchMinPrice)) $searchMinPrice = intval($searchMinPrice);
        else $searchMinPrice = null;

        if (is_numeric($searchMaxPrice)) $searchMaxPrice = intval($searchMaxPrice);
        else $searchMaxPrice = null;
    }
?>

<!doctype html>
<html lang="en">
    <head>
        <?php echo generateHead() ?>
        <title>Učitelé • Teacher Digital Agency</title>
    </head>
    <body id="LECTURERS">


        <?php insertTransitionAnimation(); ?>
        <?php insertHeader() ?>



        <section class="main pb">
            <h1 class="mainh1">Učitelé</h1>

            <form method="post">
                <div class="searchbar">
                    <div>
                        <p class="text">Filtrovat učitele</p>
                        <input type="text" class="tags" name="tags" placeholder="Tagy (oddělujte čárkou)" value="<?php echo $searchValueTags ?>">
                    </div>
                    <input type="text" class="city" name="city" placeholder="Město" value="<?php echo $searchValueCity ?>">
                    <input type="number" min="0" max="10000" class="minprice" name="minprice" placeholder="Min cena" value="<?php echo $searchMinPrice ?>">
                    <input type="number" min="0" max="10000" class="maxprice" name="maxprice" placeholder="Max cena" value="<?php echo $searchMaxPrice ?>">
                    <div class="submitdiv">
                        <div></div>
                        <input type="submit" class="search button-primary" name="search" value=" ">
                    </div>
                </div>
            </form>

            <div class="lecturers">
                <?php
                    \Functions\LecturersPage::renderLecturers($queryTags, $searchMinPrice != "" && $searchMinPrice != null ? $searchMinPrice : 0, $searchMaxPrice != "" && $searchMaxPrice != null ? $searchMaxPrice : 10000, $searchValueCity);
                ?>
            </div>
        </section>



        <?php insertFooter() ?>

        <script>
            if (window.history.replaceState) window.history.replaceState( null, null, window.location.href );
        </script>
        <script src="scripts/script.js"></script>
    </body>
</html>