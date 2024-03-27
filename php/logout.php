<?php

include_once __DIR__ . "/components/functions.php";



\Functions\Util::startSession();
\Functions\Database::logoutUser();
header("Location: /");
exit();