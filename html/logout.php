<?php

session_start();

session_unset();

session_destroy();

// Controllare session name se è ancora valido dopo session_destroy
setcookie(name: session_name(), value: '',   expires_or_options: time() - 3600);

header('Location: home.php');
    
exit();
?>