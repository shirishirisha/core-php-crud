<?php $u = current_user(); $flash = flash(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? 'EstateDesk') ?> · EstateDesk</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body class="<?= $u ? 'app' : 'guest' ?>">
<?php if ($u): ?>
<header class="topbar">
  <a class="brand" href="<?= BASE_URL ?>dashboard.php"><span class="mark"></span>EstateDesk</a>
  <nav>
    <a href="<?= BASE_URL ?>dashboard.php">Properties</a>
    <?php if (is_admin()): ?><a href="<?= BASE_URL ?>admin/users.php">Users</a><?php endif; ?>
  </nav>
  <div class="who">
    <span><?= e($u['name']) ?></span><span class="role role-<?= e($u['role']) ?>"><?= e($u['role']) ?></span>
    <a class="btn btn-ghost" href="<?= BASE_URL ?>logout.php">Log out</a>
  </div>
</header>
<?php endif; ?>
<?php if ($flash): ?><div class="toast toast-<?= e($flash[1]) ?>" role="status"><?= e($flash[0]) ?></div><?php endif; ?>
