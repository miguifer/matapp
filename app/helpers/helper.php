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