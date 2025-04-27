<?php

function formatDate($date, $format = 'Y-m-d')
{
    $datetime = new DateTime($date);
    return $datetime->format($format);
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


function validar_telefono($telefono)
{
    return preg_match('/^[0-9]{9}$/', $telefono);
}

function validar_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
