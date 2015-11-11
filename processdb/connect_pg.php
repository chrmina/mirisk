<?php

// This file is for configuration of connection to PostgreSQL DB.

function connect_pg($dbname)
{
  $connect_string = "host=localhost port=5432 user=pgsql password=miriskdb dbname=";
  $connect_string .= $dbname;

  return(pg_connect($connect_string));
}

?> 
