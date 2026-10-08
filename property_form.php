<?php
require 'includes/auth.php';
require_login();
$u = current_user();
$id = (int)($_GET['id'] ?? 0);
$types = ['Apartment', 'Villa', 'Plot', 'Commercial']; $statuses = ['Available', 'Booked', 'Sold'];
$p = ['title' => '', 'location' => '', 'type' => 'Apartment', 'bedrooms' => 0, 'price' => '', 'status' => 'Available', 'description' => ''];

if ($id) { // edit: admin can edit any, agent only own
    $s = db()->prepare('SELECT * FROM properties WHERE id = ?'); $s->execute([$id]); $row = $s->fetch();
    if (!$row) { flash('Property not found.', 'error'); redirect('dashboard.php'); }
    if (!is_admin() && (int)$row['user_id'] !== (int)$u['id']) { http_response_code(403); exit('403 - You can only edit your own listings.'); }
    $p = $row;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $p = [
        'title' => trim($_POST['title'] ?? ''), 'location' => trim($_POST['location'] ?? ''),
        'type' => $_POST['type'] ?? '', 'bedrooms' => (int)($_POST['bedrooms'] ?? 0),
        'price' => $_POST['price'] ?? '', 'status' => $_POST['status'] ?? '', 'description' => trim($_POST['description'] ?? ''),
    ];
    if ($p['title'] === '') $errors[] = 'Enter a title.';
    if ($p['location'] === '') $errors[] = 'Enter a location.';
    if (!in_array($p['type'], $types, true)) $errors[] = 'Choose a valid type.';
    if (!in_array($p['status'], $statuses, true)) $errors[] = 'Choose a valid status.';
    if (!is_numeric($p['price']) || $p['price'] <= 0) $errors[] = 'Enter a price greater than 0.';
    if ($p['bedrooms'] < 0 || $p['bedrooms'] > 20) $errors[] = 'Bedrooms must be between 0 and 20.';

    if (!$errors) {
        if ($id) {
            db()->prepare('UPDATE properties SET title=?,location=?,type=?,bedrooms=?,price=?,status=?,description=? WHERE id=?')
                ->execute([$p['title'], $p['location'], $p['type'], $p['bedrooms'], $p['price'], $p['status'], $p['description'], $id]);
            flash('Property updated.');
        } else {
            db()->prepare('INSERT INTO properties (title,location,type,bedrooms,price,status,description,user_id) VALUES (?,?,?,?,?,?,?,?)')
                ->execute([$p['title'], $p['location'], $p['type'], $p['bedrooms'], $p['price'], $p['status'], $p['description'], $u['id']]);
            flash('Property added.');
        }
        redirect('dashboard.php');
    }
}
$pageTitle = $id ? 'Edit property' : 'Add property';
include 'includes/header.php';
?>
<main class="wrap narrow">
  <div class="page-head"><h1><?= $id ? 'Edit property' : 'Add property' ?></h1><a class="btn btn-ghost" href="dashboard.php">Back</a></div>
  <form method="post" class="card form-grid" novalidate>
    <?php foreach ($errors as $er): ?><p class="alert full"><?= e($er) ?></p><?php endforeach; ?>
    <?= csrf_field() ?>
    <label class="full">Title<input name="title" required value="<?= e($p['title']) ?>"></label>
    <label class="full">Location<input name="location" required value="<?= e($p['location']) ?>"></label>
    <label>Type<select name="type"><?php foreach ($types as $t): ?><option <?= $p['type'] === $t ? 'selected' : '' ?>><?= $t ?></option><?php endforeach; ?></select></label>
    <label>Bedrooms<input type="number" name="bedrooms" min="0" max="20" value="<?= e($p['bedrooms']) ?>"></label>
    <label>Price (₹)<input type="number" name="price" min="1" step="0.01" required value="<?= e($p['price']) ?>"><small id="price-hint" class="muted"></small></label>
    <label>Status<select name="status"><?php foreach ($statuses as $s): ?><option <?= $p['status'] === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select></label>
    <label class="full">Description<textarea name="description" rows="4"><?= e($p['description']) ?></textarea></label>
    <div class="full"><button class="btn btn-primary" type="submit"><?= $id ? 'Save changes' : 'Add property' ?></button></div>
  </form>
</main>
<?php include 'includes/footer.php'; ?>
