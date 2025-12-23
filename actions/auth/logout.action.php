<?php
require_once '../../config/App.php';

Auth::logout();

// Redirect to login page
header("Location: ../../pages/auth/login.php");
exit();
