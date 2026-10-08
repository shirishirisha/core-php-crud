<?php
// Run once:  php install.php   (or open in browser). Delete this file afterwards.
require __DIR__ . '/config/database.php';
$pdo = db();
if ($pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() > 0) { exit("Already installed.\n"); }

$ins = $pdo->prepare('INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)');
$ins->execute(['Site Admin', 'admin@estatedesk.test', password_hash('Admin@123', PASSWORD_DEFAULT), 'admin']);
$ins->execute(['Demo Agent', 'agent@estatedesk.test', password_hash('Agent@123', PASSWORD_DEFAULT), 'agent']);

$p = $pdo->prepare('INSERT INTO properties (title,location,type,bedrooms,price,status,description,user_id) VALUES (?,?,?,?,?,?,?,?)');
$rows = [
  ['Lakeview 3BHK Apartment', 'Gachibowli, Hyderabad', 'Apartment', 3, 9800000, 'Available', 'East-facing, clubhouse, two covered parking slots.', 2],
  ['Garden Villa with Terrace', 'Kokapet, Hyderabad', 'Villa', 4, 32500000, 'Booked', 'Gated community, private garden, solar water heating.', 2],
  ['Corner Plot 300 sq.yd', 'Shamshabad, Hyderabad', 'Plot', 0, 6200000, 'Available', 'DTCP approved, 40 ft road, near ORR.', 1],
  ['Ground-floor Retail Space', 'Banjara Hills, Hyderabad', 'Commercial', 0, 18000000, 'Sold', 'High-footfall frontage, 1,200 sq.ft.', 1],
];
foreach ($rows as $r) { $p->execute($r); }
echo "Installed. Admin: admin@estatedesk.test / Admin@123 | Agent: agent@estatedesk.test / Agent@123\n";
