<?php



function insertFooter() : void {

    function insertPagesInfo(): string {

        $output = "";

        foreach (\Functions\Pages::getAllPages() as $page) {

            $currentPage = (parse_url($_SERVER['REQUEST_URI']))['path'];
            $pageIsCurrentPage = $currentPage == $page["href"];

            $output .= '
                <p style="margin-bottom: 10px">
                    <a style="' . ($pageIsCurrentPage ? ';color: var(--color-main);cursor:default;pointer-events:none;' : ';cursor:pointer;') . '" href="' . ($pageIsCurrentPage ? "" : $page["href"]) . ' ">' . $page["name"] . '</a>
                </p>
            ';
        }

        return $output;
    }

    echo '
        <footer id="FOOTER">
            <div class="divider"></div>
            <div class="content">
                <div class="content-flex">
                    <div class="c c1">
                        <p id="navigace">Navigace</p>
                        <div></div>
                        ' . insertPagesInfo() .'
                    </div>
                    <div class="c c3">
                        <p id="ostatni">Ostatní</p>
                        <div></div>
                        <p style="margin-bottom: 10px"><a href="/gdpr">GDPR</a></p>
                        <p style="margin-bottom: 10px"><a href="/cookies">Cookies</a></p>
                    </div>
                    <div class="c c2">
                        <p id="team">Tým</p>
                        <p style="margin-bottom:10px">Fullstack:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="p">AldiiX</span></p>
                        <p style="margin-bottom:10px">Frontend:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="p">sodak786</span></p>
                        <p style="margin-bottom:10px">Mentální pomoc:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="p">WeebDoge</span></p>
                    </div>
                </div>
                <div id="line"></div>
                <div class="logo"></div>
                <p class="copyright copyr"><span style="color: var(--color-main)">&copy</span> Teacher Digital Agency <span style="color: var(--color-main)">2024</span></p>
                <p class="copyright">Vytvořil tým <a>Error Makers</a> ze školy <a class="c" href="https://www.educhem.cz" target="_blank">Střední škola EDUCHEM, a.s.</a> pro soutěž <a class="c" href="https://tourdeapp.cz" target="_blank">Tour de App</a>.</p>
            </div>
        </footer>
    ';
}