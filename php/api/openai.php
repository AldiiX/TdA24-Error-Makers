<?php

include_once("../components/ChatGPT.php");

switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET": {

    } break;

    case "POST": {
        echo "hello world - POST";
    } break;
}