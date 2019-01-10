<?php
  function validateDate($date,$format)
  {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
  }

