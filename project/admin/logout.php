<!-- admin/logout.php -->
<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to main index page
header("Location:project/index.php");
exit();
?>
