<?php
    session_start();
    setcookie('redirect', 'yes', 0, "/");
    header('Location: https://www.finanse.mf.gov.pl/inne-podatki/podatek-od-gier-gry-hazardowe/komunikat');
?>