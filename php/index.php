<?php
    // kontrola jestli je uživatel na stránce /index nebo /index.php, pak ho to přesměruje na /
    if (basename($_SERVER['REQUEST_URI']) === 'index' || basename($_SERVER['REQUEST_URI']) === 'index.php') {
        header("Location: /");
        exit();
    }

    include_once(__DIR__ . "/components/html_head.php");
    include_once(__DIR__ . "/components/header.php");
    include_once(__DIR__ . "/components/footer.php");
    include_once(__DIR__ . "/components/functions.php");
    include_once(__DIR__ . "/components/page_transition_animation.php");
    use Functions\IndexPage as Core;
    \Functions\Util::startSession();


    $statistics = Core::getStatistics();
?>

<!doctype html>
<html lang="en">
<head>
    <?php echo generateHead() ?>
    <title>Domů • Teacher Digital Agency</title>
</head>
<body id="HOME">
    <?php insertTransitionAnimation(); insertHeader();?>
    <section class="main">
        <div class="blob page-center"></div>
        <div style="width: 100%; height: 100vh; position: relative;">
            <div class="page-center">
                <div class="flex-parent">
                    <div class="flex-child">
                        <h1>Teacher Digital<span>*</span></h1>
                        <h1>Agency</h1>
                        <p class="content"><span>*</span>Služba, která spojuje studenty s kvalifikovanými vzdělávacími profesionály prostřednictvím své platformy, umožňující efektivní komunikaci a snadné vyhledání vhodných učitelů pro osobní doučování, konzultace a další formy vzdělávání.</p>
                    </div>
                </div>
                <div class="statistics">
                    <div>
                        <p><?php echo $statistics["lecturers"] ?></p>
                        <p>Učitelů</p>
                    </div>

                    <div>
                        <p><?php echo $statistics["users"] ?></p>
                        <p>Uživatelů</p>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <?php insertFooter() ?>

    <script src="scripts/script.js"></script>
</body>
</html>