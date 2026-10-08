<?php
require 'includes/auth.php';
if (current_user()) redirect('dashboard.php');
$errors = []; $name = $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name = trim($_POST['name'] ?? ''); $email = trim($_POST['email'] ?? ''); $pass = $_POST['password'] ?? '';
    if ($name === '') $errors[] = 'Enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($pass) < 8) $errors[] = 'Password must be at least 8 characters.';
    if (!$errors) {
        $chk = db()->prepare('SELECT id FROM users WHERE email = ?'); $chk->execute([$email]);
        if ($chk->fetch()) $errors[] = 'That email is already registered.';
    }
    if (!$errors) {
        // Public sign-ups are always "agent"; only an admin can promote.
        db()->prepare('INSERT INTO users (name,email,password,role) VALUES (?,?,?,"agent")')
            ->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT)]);
        flash('Account created. Log in to continue.');
        redirect('login.php');
    }
}
$pageTitle = 'Create account';
include 'includes/header.php';
?>
<main class="auth-split">
  <section class="auth-art">
    <h1>Start listing<br>in a minute.</h1>
    <p>Sign up as an agent and add your first property right away.</p>
  </section>
  <section class="auth-panel">
    <form method="post" class="auth-form" novalidate>
      <h2>Create account</h2>
      <?php foreach ($errors as $er): ?><p class="alert"><?= e($er) ?></p><?php endforeach; ?>
      <?= csrf_field() ?>
      <label>Full name<input name="name" required value="<?= e($name) ?>"></label>
      <label>Email<input type="email" name="email" required value="<?= e($email) ?>"></label>
      <label>Password
        <span class="pw"><input type="password" name="password" required minlength="8"><button type="button" class="pw-toggle" aria-label="Show password">Show</button></span>
      </label>
      <button class="btn btn-primary" type="submit">Create account</button>
      <p class="alt">Already registered? <a href="login.php">Log in</a></p>
    </form>
  </section>
</main>
<?php include 'includes/footer.php'; ?>
