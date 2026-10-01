<?php
session_start();
session_destroy();
header('Location: ../HTML/Acceso.html');
exit;
?>