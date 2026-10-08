<?php
require '../includes/auth.php';
require_role('admin');
$me = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    if ($id === (int)$me['id']) { flash('You cannot change your own account here.', 'error'); redirect('admin/users.php'); }
    if (($_POST['action'] ?? '') === 'role' && in_array($_POST['role'] ?? '', ['admin', 'agent'], true)) {
        db()->prepare('UPDATE users SET role = ? WHERE id = ?')->execute([$_POST['role'], $id]);
        flash('Role updated.');
    } elseif (($_POST['action'] ?? '') === 'delete') {
        db()->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
        flash('User and their listings deleted.');
    }
    redirect('admin/users.php');
}
$users = db()->query('SELECT u.*, (SELECT COUNT(*) FROM properties WHERE user_id = u.id) AS listings FROM users u ORDER BY u.created_at DESC')->fetchAll();
$pageTitle = 'Users';
include '../includes/header.php';
?>
<main class="wrap">
  <div class="page-head"><div><h1>Users</h1><p class="muted">Promote agents to admin or remove accounts.</p></div></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Name</th><th>Email</th><th>Listings</th><th>Role</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($users as $x): $self = (int)$x['id'] === (int)$me['id']; ?>
        <tr>
          <td data-label="Name"><strong><?= e($x['name']) ?></strong><?= $self ? ' <small>(you)</small>' : '' ?></td>
          <td data-label="Email"><?= e($x['email']) ?></td>
          <td data-label="Listings"><?= $x['listings'] ?></td>
          <td data-label="Role">
            <?php if ($self): ?><span class="role role-admin">admin</span><?php else: ?>
            <form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="action" value="role"><input type="hidden" name="id" value="<?= $x['id'] ?>">
              <select name="role" data-autosubmit><option value="agent" <?= $x['role'] === 'agent' ? 'selected' : '' ?>>agent</option><option value="admin" <?= $x['role'] === 'admin' ? 'selected' : '' ?>>admin</option></select>
            </form><?php endif; ?>
          </td>
          <td class="actions"><?php if (!$self): ?>
            <form method="post" class="inline" data-confirm="Delete <?= e($x['name']) ?> and all their listings?"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $x['id'] ?>"><button class="btn btn-danger btn-sm">Delete</button></form>
          <?php endif; ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include '../includes/footer.php'; ?>
