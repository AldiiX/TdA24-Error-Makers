<?php
    include_once __DIR__ . "/components/html_head.php";
    include_once __DIR__ . "/components/page_transition_animation.php";
    include_once __DIR__ . "/components/header.php";
    include_once __DIR__ . "/components/footer.php";
    include_once __DIR__ . "/components/functions.php";
    $errorMessage = "";
    $redirectTo = $_GET["rt"] ?? null;
    use Functions\Database as db;

    \Functions\Util::startSession();
    db::reauthUser();


    if(db::isUserLoggedIn()) {
        header("Location: /account");
        exit;
    }

    // Zpracování odeslaného formuláře
    if (isset($_POST['submit'])) {

        $username = $_POST['username'];
        $email = htmlspecialchars($_POST['email']);
        $password = db::encryptPassword($_POST["password"]);
        $passwordconfirm = db::encryptPassword($_POST['passwordconfirm']);
        $error = false;

        if(!filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
            $errorMessage .= "Špatný formát emailu!<br>";
            $error = true;
        }

        if($password != $passwordconfirm) {
            $errorMessage .= "Hesla se neshodují!<br>";
            $error = true;
        } else if(strlen($_POST["password"]) < 6) {
            $errorMessage .= "Heslo musí mít minimálně 6 znaků!<br>";
            $error = true;
        }

        if(db::isUsernameTaken($username)) {
            $errorMessage .= "Účet s tímto uživatelským jménem již existuje!<br>";
            $error = true;
        } else if(\Functions\Util::stringContainsSpecialCharacters($username, '/[<>!@#$%^&*(),?":{}|-]/')) {
            $errorMessage .= "Jméno nesmí obsahovat speciální znaky!<br>";
            $error = true;
        }

        if(db::isEmailTaken($email)) {
            $errorMessage .= "Účet s tímto emailem již existuje!<br>";
            $error = true;
        }

        if(!$error) {
            $username = htmlspecialchars($username);
            $success = db::createUser($username, $email, $password);
            if($success) {
                db::authUser($username, $password);
                if($redirectTo) header("Location: " . $redirectTo);
                else header("Location: /account");
                exit;
            }
        }
    }

?>

<!doctype html>
<html lang="en">
<head>
    <title>Register</title>
    <?php echo generateHead() ?>
</head>
<body id="REGISTER">
    <?php insertTransitionAnimation(); insertHeader(); ?>

    <section class="main" style="min-height: calc(100vh + 100px); margin-top: 18vh">
        <div class="parent" style="text-align: center">
            <h1 class="mainh1">Registrace</h1>
            <p class="p1">Vítejte na TdA! Registrujte se, abyste mohl/a využívat naší služby.</p>
            <p class="p2">Máte účet? <a href="/login" style="color: var(--color-main);text-decoration: none;font-weight: bolder">Tak se přihlašte!</a></p>
        </div>

        <form method="post">
            <div class="login-box">
                <input type="text" placeholder="Uživatelské jméno" name="username" class="input-buttons"/>
                <input type="text" placeholder="Email" name="email" class="input-buttons"/>
                <input type="password" placeholder="Heslo" name="password" class="input-buttons"/>
                <input type="password" placeholder="Heslo znovu" name="passwordconfirm" class="input-buttons"/>
                <p class="errormsg"><?php echo $errorMessage ?></p>
                <input class="button-primary" name="submit" type="submit" value="Registrovat se">
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