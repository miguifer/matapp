<?php

function formatDate($date, $format = 'Y-m-d')
{
    $datetime = new DateTime($date);
    return $datetime->format($format);
}
