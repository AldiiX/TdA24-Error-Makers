<?php

    include_once(__DIR__ . "/components/html_head.php");
    include_once(__DIR__ . "/components/functions.php");
    include_once(__DIR__ . "/components/header.php");
    include_once(__DIR__ . "/components/footer.php");
    include_once(__DIR__ . "/components/page_transition_animation.php");
    \Functions\Util::startSession();


    $code = $_GET["c"] ?? "";
    if(intval($code) == 0) $code = "";

    $msg = $_GET["msg"] ?? "";
?>

<!doctype html>
<html lang="en">
<head>
    <?php echo generateHead() ?>
    <title>Error<?php echo " " . $code ?> • Teacher Digital Agency</title>
</head>
<body id="ERROR">

    <?php insertTransitionAnimation(); ?>
    <?php insertHeader() ?>



    <section class="main">
        <h1 class="mainh1">ERROR<?php if($code != "") echo " " . $code ?></h1>
        <p style="text-align: center"><?php if($msg != "") echo " " . $msg ?></p>
        <!--<div style="position: relative; width: 10vw; height: 10vw; background-image: url('<?php echo \Functions\Util::getNestingLevelDir() . "images/icons/broken_search.svg" ?>')"></div>-->
    </section>



    <?php insertFooter() ?>

    <script src="<?php echo \Functions\Util::getNestingLevelDir() . 'scripts/script.js'?>"></script>
</body>
</html>