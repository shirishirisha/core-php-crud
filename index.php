<?php require 'includes/auth.php'; redirect(current_user() ? 'dashboard.php' : 'login.php');
