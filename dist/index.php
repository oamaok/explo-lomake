<?php

require_once "inc/init.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Teemu Pääkkönen">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Palanquin" rel="stylesheet" type="text/css">
    <link href="css/main.css" rel="stylesheet" />
    <title></title>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="img/explo.png" />
        </div>
        <h1>Aktiviteetteihin ilmoittautuminen</h1>
        <div class="form">
            <h2>Perustiedot</h2>
            <div class="field">
                <label for="first-name">Etunimi</label>
                <input type="text" name="first-name" id="first-name">
            </div>
            <div class="field">
                <label for="last-name">Sukunimi</label>
                <input type="text" name="last-name" id="last-name">
            </div>
        </div>
        <div id="app">
        </div>
        <!-- 
        <div class="package">
            <h2>ExploTASKU 1: Haaveile</h2>
            <div class="activity">
                <div class="wrapper">
                    <div class="toggle">
                        <div class="button">&#xE836;</div>
                    </div>
                    <div class="info">
                        <div class="name">HipHop-workshop</div>
                        <div class="slots">Vielä <b>60</b> paikkaa vapaana!</div>
                    </div>
                </div>
            </div>
            <div class="activity selected">
                <div class="wrapper">
                    <div class="toggle">
                        <div class="button">&#xE837;</div>
                    </div>
                    <div class="info">
                        <div class="name">HipHop-workshop</div>
                        <div class="slots">Osallistut tähän aktiviteettiin!</div>
                    </div>
                </div>
            </div>
            <div class="activity">
                <div class="wrapper">
                    <div class="toggle">
                        <div class="button">&#xE836;</div>
                    </div>
                    <div class="info">
                        <div class="name">HipHop-workshop</div>
                        <div class="slots">Vielä <b>60</b> paikkaa vapaana!</div>
                    </div>
                </div>
            </div>
            <div class="activity full">
                <div class="wrapper">
                    <div class="toggle">
                        <div class="button"></div>
                    </div>
                    <div class="info">
                        <div class="name">HipHop-workshop</div>
                        <div class="slots">Aktiviteetti on täynnä.</div>
                    </div>
                </div>
            </div>
        </div>
        -->
    </div>
    <script src="js/jquery.min.js"></script>
    <script src="js/react.js"></script>
    <script src="js/app.min.js"></script>
</body>
</html>