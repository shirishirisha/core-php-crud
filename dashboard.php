<?php
require 'includes/auth.php';
require_login();
$u = current_user();

$q = trim($_GET['q'] ?? ''); $status = $_GET['status'] ?? '';
$where = []; $args = [];
if (!is_admin()) { $where[] = 'p.user_id = ?'; $args[] = $u['id']; }
if ($q !== '')  { $where[] = '(p.title LIKE ? OR p.location LIKE ?)'; $args[] = "%$q%"; $args[] = "%$q%"; }
if (in_array($status, ['Available', 'Booked', 'Sold'], true)) { $where[] = 'p.status = ?'; $args[] = $status; }
$sql = 'SELECT p.*, u.name AS owner FROM properties p JOIN users u ON u.id = p.user_id'
     . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY p.created_at DESC';
$stmt = db()->prepare($sql); $stmt->execute($args); $props = $stmt->fetchAll();

$sc = db()->prepare('SELECT status, COUNT(*) c FROM properties' . (is_admin() ? '' : ' WHERE user_id = ?') . ' GROUP BY status');
$sc->execute(is_admin() ? [] : [$u['id']]);
$counts = ['Available' => 0, 'Booked' => 0, 'Sold' => 0] + $sc->fetchAll(PDO::FETCH_KEY_PAIR);

$pageTitle = 'Properties';
include 'includes/header.php';
?>
<main class="wrap">
  <div class="page-head">
    <div>
      <h1><?= is_admin() ? 'All properties' : 'My properties' ?></h1>
      <p class="muted"><?= is_admin() ? 'Portfolio across every agent.' : 'Listings you manage.' ?></p>
    </div>
    <a class="btn btn-primary" href="property_form.php">Add property</a>
  </div>

  <div class="stats">
    <div><strong><?= array_sum($counts) ?></strong><span>Total</span></div>
    <div><strong class="c-available"><?= $counts['Available'] ?></strong><span>Available</span></div>
    <div><strong class="c-booked"><?= $counts['Booked'] ?></strong><span>Booked</span></div>
    <div><strong class="c-sold"><?= $counts['Sold'] ?></strong><span>Sold</span></div>
  </div>

  <form class="filters" method="get">
    <input type="search" name="q" placeholder="Search title or location" value="<?= e($q) ?>" aria-label="Search">
    <select name="status" aria-label="Status">
      <option value="">All statuses</option>
      <?php foreach (['Available', 'Booked', 'Sold'] as $s): ?>
        <option <?= $status === $s ? 'selected' : '' ?>><?= $s ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn-ghost" type="submit">Filter</button>
  </form>

  <?php if (!$props): ?>
    <div class="empty"><h3>No properties found</h3><p>Add your first listing or clear the filters.</p><a class="btn btn-primary" href="property_form.php">Add property</a></div>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Property</th><th>Type</th><th>Beds</th><th>Price</th><th>Status</th><?php if (is_admin()): ?><th>Agent</th><?php endif; ?><th></th></tr></thead>
      <tbody>
      <?php foreach ($props as $p): ?>
        <tr>
          <td data-label="Property"><strong><?= e($p['title']) ?></strong><small><?= e($p['location']) ?></small></td>
          <td data-label="Type"><?= e($p['type']) ?></td>
          <td data-label="Beds"><?= $p['bedrooms'] ?: '—' ?></td>
          <td data-label="Price"><?= money((float)$p['price']) ?></td>
          <td data-label="Status"><span class="pill pill-<?= strtolower($p['status']) ?>"><?= e($p['status']) ?></span></td>
          <?php if (is_admin()): ?><td data-label="Agent"><?= e($p['owner']) ?></td><?php endif; ?>
          <td class="actions">
            <a class="btn btn-ghost btn-sm" href="property_form.php?id=<?= $p['id'] ?>">Edit</a>
            <button class="btn btn-danger btn-sm" data-delete="<?= $p['id'] ?>" data-name="<?= e($p['title']) ?>">Delete</button>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</main>

<dialog id="confirm">
  <form method="post" action="property_delete.php">
    <?= csrf_field() ?><input type="hidden" name="id" id="del-id">
    <h3>Delete this property?</h3>
    <p id="del-name" class="muted"></p>
    <div class="dlg-actions"><button class="btn btn-ghost" value="cancel" formmethod="dialog">Keep it</button><button class="btn btn-danger" type="submit">Delete property</button></div>
  </form>
</dialog>
<?php include 'includes/footer.php'; ?>
