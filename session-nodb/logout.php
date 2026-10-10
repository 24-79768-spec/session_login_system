<?php
session_start();
unset($_SESSION['user']);      // log out but keep demo-registered accounts
session_regenerate_id(true);
header('Location: index.php?out=1');
exit;
