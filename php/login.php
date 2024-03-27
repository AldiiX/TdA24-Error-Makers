<?php
    include_once __DIR__ . "/components/functions.php";
    include_once(__DIR__ . "/components/html_head.php");
    include_once(__DIR__ . "/components/page_transition_animation.php");
    include_once(__DIR__ . "/components/header.php");
    include_once(__DIR__ . "/components/footer.php");
    $errorMessage = "";
    $redirectTo = $_GET["rt"] ?? null;

    \Functions\Util::startSession();
    if(\Functions\Database::isUserLoggedIn()) {
        header("Location: /account");
        exit;
    }



    // Zpracování odeslaného formuláře
    if (isset($_POST['submit'])) {
        $username = $_POST['username'];
        $password = \Functions\Database::encryptPassword($_POST["password"]);

        // Ověření přihlašovacích údajů
        if (Functions\Database::authUser($username, $password)) {
            $_SESSION["userinfo"]['name'] = $username;
            $_SESSION["userinfo"]['password'] = $password;

            if($redirectTo) header("Location: " . $redirectTo);
            else header("Location: /account");
            exit();
        } else {
            $errorMessage = "Neplatné přihlašovací údaje";
        }
    }

?>

<!doctype html>
<html lang="en">
<head>
    <title>Login</title>
    <?php echo generateHead() ?>
</head>
<body id="LOGIN">
    <?php insertTransitionAnimation(); insertHeader(); ?>

    <section class="main" style="min-height: 100vh; margin-top: 22.5vh">
        <div class="parent" style="text-align: center">
            <h1 class="mainh1">Login</h1>
            <p class="p1">Vítejte na TdA! Přihlašte se, abyste mohl/a využívat naší služby.</p>
            <p class="p2">Nemáte účet? <a href="/register" style="color: var(--color-main);text-decoration: none;font-weight: bolder">Tak se zaregistrujte!</a></p>
        </div>

        <form method="post">
            <div class="login-box">
                <input type="text" placeholder="Uživatelské jméno nebo email" name="username" class="input-buttons"/>
                <input type="password" placeholder="Heslo" name="password" class="input-buttons"/>
                <p class="errormsg"><?php echo $errorMessage ?></p>
                <input class="button-primary" name="submit" type="submit" value="Přihlásit se">
            </div>
        </form>
    </section>


    <?php insertFooter(); ?>
    <script src="<?php echo \Functions\Util::getNestingLevelDir() . 'scripts/script.js'?>"></script>
    <script>
        if (window.history.replaceState) window.history.replaceState( null, null, window.location.href );
    </script>
</body>
</html>