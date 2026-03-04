<?php

session_start();
session_destroy();
header('Location: ../table.php');
exit;