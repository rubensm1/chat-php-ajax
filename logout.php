<?php
// Include session file
require_once 'includes/session.php';

// End user session
endUserSession();

// Redirect to login page
header('Location: login.php');
exit;
?>
