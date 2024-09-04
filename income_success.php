<?php

session_start();

if (!isset($_SESSION['income_success'])) {
    header('Location: income.php');
    exit();
}

if (isset($_SESSION['fr_value'])) unset($_SESSION['fr_value']);
if (isset($_SESSION['fr_date'])) unset($_SESSION['fr_date']);
if (isset($_SESSION['fr_category'])) unset($_SESSION['fr_category']);
if (isset($_SESSION['fr_description'])) unset($_SESSION['fr_description']);

if (isset($_SESSION['e_value'])) unset($_SESSION['e_value']);
if (isset($_SESSION['e_date'])) unset($_SESSION['e_date']);
if (isset($_SESSION['e_category'])) unset($_SESSION['e_category']);
if (isset($_SESSION['e_description'])) unset($_SESSION['e_description']);

$_SESSION['income_added'] = true;
header('Location: income.php');
