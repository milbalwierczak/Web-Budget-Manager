<?php

session_start();

if (!isset($_SESSION['expense_success'])) {
    header('Location: expense.php');
    exit();
}

if (isset($_SESSION['fr_value'])) unset($_SESSION['fr_value']);
if (isset($_SESSION['fr_date'])) unset($_SESSION['fr_date']);
if (isset($_SESSION['fr_category'])) unset($_SESSION['fr_category']);
if (isset($_SESSION['fr_method'])) unset($_SESSION['fr_method']);
if (isset($_SESSION['fr_description'])) unset($_SESSION['fr_description']);

//Usuwanie błędów
if (isset($_SESSION['e_value'])) unset($_SESSION['e_value']);
if (isset($_SESSION['e_date'])) unset($_SESSION['e_date']);
if (isset($_SESSION['e_description'])) unset($_SESSION['e_description']);
if (isset($_SESSION['e_category'])) unset($_SESSION['e_category']);
if (isset($_SESSION['e_method'])) unset($_SESSION['e_method']);


$_SESSION['expense_added'] = true;
header('Location: expense.php');
