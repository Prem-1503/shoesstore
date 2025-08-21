<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to public index page
header("Location: ../public/index.php");
exit;
?>
