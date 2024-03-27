<?php





function insertHeader(): void {

    $insertMenuItems = function(): string {

        $output = "";

        foreach (\Functions\Pages::getAllPages() as $page) {

            $currentPage = (parse_url($_SERVER['REQUEST_URI']))['path'];
            $pageIsCurrentPage = $currentPage == $page["href"];

            $output .= '
                <a class="menu-child" style="' . ($pageIsCurrentPage ? "pointer-events:none;color:var(--color-main);" : "") . '" ' . ($pageIsCurrentPage ? "" : "href=" . $page["href"]) . '>' . $page["name"] . '</a>
            ';
        }

        return $output;
    };

    $insertLoginInSection = function(): string {
        if(!\Functions\Database::isUserLoggedIn()) return '
            <button class="login-button button-primary" onclick="location.href=`' . \Functions\Util::getNestingLevelDir() . "login" . '`">Přihlásit se</button>
        ';

        $avatar = $_SESSION["userinfo"]["picture_url"] != null ? ('
            <div class="avatar" style="background-image: url(' . $_SESSION["userinfo"]["picture_url"] . '); background-size: cover; background-position: center"></div>
        ') : '
            <div class="avatar">
                <p class="page-center" style="text-align: center;color:black;text-transform: uppercase;font-size:30px;margin-top:-2px">' . $_SESSION["userinfo"]["username"][0] . '</p>
            </div>
        ';
        $displayName = $_SESSION["userinfo"]["first_name"] != "" && $_SESSION["userinfo"]["last_name"] != "" ? $_SESSION["userinfo"]["first_name"] . " " . $_SESSION["userinfo"]["last_name"] : $_SESSION["userinfo"]["username"];

        return '
            <a class="userdiv" href="/account" ">
                <div class="flexdiv">
                    <div class="texts">
                        <p>Přihlášen/a jako</p>
                        <p>' . $displayName . '</p>
                    </div>
                    ' . $avatar . '
                </div>
            </a>
        ';
    };

    $insertMenuItemsMobile = function() use ($insertLoginInSection): string{
        $output = "";

        foreach (\Functions\Pages::getAllPages() as $page) {
            $output .= '
                <a class="a-page" href="' . $page["href"] . '">
                    <div style="background-image: url(../images/icons/' . $page["icon"] . ')"></div>
                    <p>' . $page["name"] . '</p>
                </a>
            ';
        }

        if(!isset($_SESSION["userinfo"])) $output .= '
            <a class="a-page" href="/login" style="">
                <div style=background-image:url("../images/icons/login.svg" ;></div>
                <p style="color: var(--color-main)">Přihlásit</p>
            </a>
        '; else $output .= "
            <div class='divider'></div>
            {$insertLoginInSection()}
        ";

        return $output;
    };

    $_a = \Functions\Util::getCurrentPage() == '/' ? 'pointer-events:none' : '';



    $output = <<<EOT
        <header id="HEADER">
            <div class="logo" style="$_a" onclick="location.href='/'"></div>
            <div class="menu-parent page-center">{$insertMenuItems()}</div>
            
            {$insertLoginInSection()}
            
            <div class="mobile-menu-icon" onclick="Web.toggleMenu()">
                <div class="mobile-menu">
                    {$insertMenuItemsMobile()}
                </div>
            </div>
        </header>
    EOT;

    echo $output;
}