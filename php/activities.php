<?php
    include_once(__DIR__ . "/components/html_head.php");
    include_once(__DIR__ . "/components/header.php");
    include_once(__DIR__ . "/components/footer.php");
    include_once(__DIR__ . "/components/functions.php");
    include_once(__DIR__ . "/components/page_transition_animation.php");
    include_once(__DIR__ . "/components/ChatGPT.php");
    include_once(__DIR__ . "/components/API.php");
    use \Functions\Pages;
    \Functions\Util::startSession();
    $input = $_POST["input"] ?? null;

    $foundArray = [];

    if(isset($_POST["submit"])) {
        if(empty($input)) {
            goto afterpost;
        }

        $chatgpt = new ChatGPT();
        $output = $chatgpt->findActivity($input, json_encode(Api::activitiesGET()));
//        echo $output["data"];
        try {
            $foundArray = json_decode($output['data'], true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
        }

//        $foundArray = [ ["activityName" => "Nějaké jméno", "description" => "lorem sdasd sadsad sassadsa sadsadsadasd sadsadsad as sdfsdfsdfdsf sdfjdsjfjdfd sdjfsdjfdsfisdj sd fisd"]];
    }

    afterpost:
?>

<!doctype html>
<html lang="en">
<head>
    <?php echo generateHead() ?>
    <title>Knihovna Aktivit • Teacher Digital Agency</title>
</head>
<body id="ACTIVITIES">


<?php insertTransitionAnimation(); ?>
<?php insertHeader() ?>



<section class="main pb" <?php if(!isset($_POST["input"])) echo 'style=\'margin-top: 30vh;\''?>>
    <h1 class="mainh1">Knihovna Aktivit</h1>

    <form method="post" onsubmit="Web.activitiesRunLoadingAnimation()">
        <div class="searchbar">

            <div class="kontank">
                <p class="text">Vyhledat aktivitu</p>
                <input type="text" class="tags" name="input" placeholder="Zábavné zpříjemnění hodiny chemie" value="<?php echo $input ?>" minlength="2" required>
            </div>
            <div class="submitdiv">
                <div></div>
                <input type="submit" value="" name="submit" class="search button-primary">
            </div>
        </div>
    </form>

    <button class="add button-primary" onclick="location.href='/activitycreate'">Přidat aktivitu</button>

    <?php if(!empty($_POST) && !empty($foundArray)) { ?>
        <div class="activities">
            <?php foreach ($foundArray as $activity) { ?>
                <a class="activity" href="/activity/<?php echo $activity['uuid'] ?? ''?>">
                    <p class="name"><?php echo $activity["activityName"] ?></p>
                    <p class="desc"><?php echo $activity["description"] ?></p>
                </a>
            <?php } ?>
        </div>
    <?php } else if(!empty($_POST) && empty($foundArray)) { ?>
        <p>Nebylo nic nalezeno</p>
    <?php } ?>
</section>

<?php //echo json_encode(Api::activitiesGET()) ?>



<?php insertFooter() ?>

<script>
    if (window.history.replaceState) window.history.replaceState( null, null, window.location.href );
</script>
<script src="scripts/script.js"></script>
</body>
</html>