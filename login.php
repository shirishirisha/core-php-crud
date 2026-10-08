<?php
require 'includes/auth.php';
if (current_user()) redirect('dashboard.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $stmt = db()->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([trim($_POST['email'] ?? '')]);
    $user = $stmt->fetch();
    if ($user && password_verify($_POST['password'] ?? '', $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $user['id'], 'name' => $user['name'], 'role' => $user['role']];
        redirect('dashboard.php');
    }
    $error = 'Email or password is incorrect.';
}
$pageTitle = 'Log in';
include 'includes/header.php';
?>
<main class="auth-split">
  <section class="auth-art">
    <h1>Every listing,<br>one clear desk.</h1>
    <p>Track apartments, villas, plots and commercial space from first listing to sale.</p>
    <ul class="art-lines"><li>Agents manage their own listings</li><li>Admins see the whole portfolio</li></ul>
  </section>
  <section class="auth-panel">
    <form method="post" class="auth-form" novalidate>
      <h2>Log in</h2>
      <?php if ($error): ?><p class="alert"><?= e($error) ?></p><?php endif; ?>
      <?= csrf_field() ?>
      <label>Email<input type="email" name="email" required autofocus value="<?= e($_POST['email'] ?? '') ?>"></label>
      <label>Password
        <span class="pw"><input type="password" name="password" required><button type="button" class="pw-toggle" aria-label="Show password">Show</button></span>
      </label>
      <button class="btn btn-primary" type="submit">Log in</button>
      <p class="alt">New agent? <a href="register.php">Create an account</a></p>
    </form>
  </section>
</main>
<?php include 'includes/footer.php'; ?>
