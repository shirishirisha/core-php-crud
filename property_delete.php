<?php
require 'includes/auth.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('dashboard.php');
csrf_check();
$id = (int)($_POST['id'] ?? 0);
$u = current_user();
// Admin may delete any listing; an agent only their own.
$sql = is_admin() ? 'DELETE FROM properties WHERE id = ?' : 'DELETE FROM properties WHERE id = ? AND user_id = ?';
$st = db()->prepare($sql); $st->execute(is_admin() ? [$id] : [$id, $u['id']]);
$st->rowCount() ? flash('Property deleted.') : flash('Nothing was deleted.', 'error');
redirect('dashboard.php');
