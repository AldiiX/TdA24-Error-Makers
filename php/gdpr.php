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
        <title>GDPR • Teacher Digital Agency</title>
    </head>
    <body id="GDPR">
        <?php insertTransitionAnimation(); insertHeader();?>
        <section class="main pb" style="min-height: 70vh">
            <h1 class="mainh1">GDPR</h1>
            <p style="width: 80vw; text-align: center; margin: auto;">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias esse illum minima, nulla optio possimus provident quia quibusdam reprehenderit sit suscipit ut velit, voluptas! A commodi error excepturi exercitationem facere laborum magni nam, non numquam quis rerum saepe soluta vitae. Eum laborum quis voluptatem! Alias aspernatur aut consectetur distinctio excepturi, impedit laboriosam maiores mollitia natus possimus repellat repellendus rerum sed tempore voluptate. Aut, beatae, culpa dicta distinctio dolor eligendi est fuga iusto magnam molestiae nam non quaerat quidem quos, rem saepe sapiente sunt voluptates. Accusamus assumenda atque cupiditate deserunt dolore explicabo, necessitatibus nulla officia perferendis, provident ratione reiciendis, voluptas voluptatem.</p>

        </section>

        <?php insertFooter() ?>

        <script src="scripts/script.js"></script>
    </body>
</html>