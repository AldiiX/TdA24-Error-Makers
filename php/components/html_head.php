<?php


function generateHead(): string {
    return '
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="../styles/main.css">
        <link rel="stylesheet" href="../styles/util.css">
        <link rel="stylesheet" href="../styles/fontstyles.css">
        <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">
    ';
}