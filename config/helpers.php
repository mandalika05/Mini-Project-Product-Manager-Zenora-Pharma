<?php
// Fungsi bantu: session, CSRF, escape output, validasi, layout, form
session_start();
$_SESSION["csrf"] ??= bin2hex(random_bytes(32));

const CATEGORIES = ["Obat Bebas", "Obat Bebas Terbatas", "Obat Keras",
                    "Vitamin & Suplemen", "Alat Kesehatan", "Perawatan Tubuh"];
const LOW_STOCK = 10;

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8"); }
function rupiah($n): string { return "Rp " . number_format((float)$n, 0, ",", "."); }
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e($_SESSION["csrf"]) . '">'; }
function csrf_check(): void {
    if (!hash_equals($_SESSION["csrf"] ?? "", $_POST["csrf"] ?? "")) {
        http_response_code(403); exit("Token tidak valid");
    }
}

// Normalisasi + validasi server-side. Return [data, errors]
function validate_product(PDO $pdo, int $ignoreId = 0): array {
    $name  = trim($_POST["name"] ?? "");
    $cat   = trim($_POST["category"] ?? "");
    $price = filter_input(INPUT_POST, "price", FILTER_VALIDATE_FLOAT);
    $stock = filter_input(INPUT_POST, "stock", FILTER_VALIDATE_INT);
    $errors = [];

    if (mb_strlen($name) < 3)  $errors["name"] = "Nama minimal 3 karakter.";
    elseif (mb_strlen($name) > 100) $errors["name"] = "Nama maksimal 100 karakter.";
    else {
        $st = $pdo->prepare("SELECT id FROM products WHERE name = :name AND id <> :id");
        $st->execute(["name" => $name, "id" => $ignoreId]);
        if ($st->fetch()) $errors["name"] = "Nama produk sudah terdaftar.";
    }
    if (!in_array($cat, CATEGORIES, true)) $errors["category"] = "Kategori tidak valid.";
    if ($price === false || $price === null || $price <= 0) $errors["price"] = "Harga harus > 0.";
    if ($stock === false || $stock === null || $stock < 0)  $errors["stock"] = "Stok tidak boleh negatif.";

    return [["name" => $name, "category" => $cat, "price" => $price, "stock" => $stock], $errors];
}

function page_header(string $title): void {
    $page = basename($_SERVER["SCRIPT_NAME"]);
    $cat  = $_GET["cat"] ?? "";
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> | Zenora Pharma</title>
<link rel="stylesheet" href="assets/style.css">
<script src="assets/app.js"></script>
</head>
<body>
<aside class="sidebar">
  <a class="logo" href="index.php">
    <span class="logo-icon">✚</span>
    <span class="logo-text">Zenora <b>Pharma</b><small>v1.0 // SMART PHARMACY</small></span>
  </a>
  <nav>
    <p class="nav-label">MENU UTAMA</p>
    <a class="nav-link <?= ($page === 'index.php' && $cat === '') ? 'active' : '' ?>" href="index.php">📦 Katalog Obat</a>
    <a class="nav-link <?= $page === 'create.php' ? 'active' : '' ?>" href="create.php">➕ Tambah Obat</a>
    <p class="nav-label">FILTER KATEGORI</p>
    <?php foreach (CATEGORIES as $i => $c): ?>
      <a class="nav-link cat c<?= $i ?> <?= $cat === $c ? 'active' : '' ?>" href="index.php?cat=<?= urlencode($c) ?>"><i></i><?= e($c) ?></a>
    <?php endforeach; ?>
  </nav>
  <button type="button" class="btn btn-ghost" id="themeToggle">🌗 Ganti Tema</button>
</aside>
<div class="content">
<main class="main">
<?php }

function page_footer(): void { ?>
</main>
<footer class="footer">Zenora Pharma &middot; Praktikum 3 Pemrograman Web &middot; PHP &amp; MySQL</footer>
</div>
</body>
</html>
<?php }

// Form dipakai bersama oleh create.php dan edit.php
function product_form(array $v, array $errors, string $action, string $label, int $id = 0): void { ?>
<form method="POST" action="<?= e($action) ?>" class="card form" novalidate>
  <?= csrf_field() ?>
  <?php if ($id): ?><input type="hidden" name="id" value="<?= $id ?>"><?php endif; ?>

  <label for="name">Nama obat / produk</label>
  <input id="name" name="name" minlength="3" maxlength="100" required value="<?= e($v["name"] ?? "") ?>">
  <?php if (isset($errors["name"])): ?><p class="error"><?= e($errors["name"]) ?></p><?php endif; ?>

  <label for="category">Kategori</label>
  <select id="category" name="category" required>
    <?php foreach (CATEGORIES as $c): ?>
      <option value="<?= e($c) ?>" <?= ($v["category"] ?? "") === $c ? "selected" : "" ?>><?= e($c) ?></option>
    <?php endforeach; ?>
  </select>
  <?php if (isset($errors["category"])): ?><p class="error"><?= e($errors["category"]) ?></p><?php endif; ?>

  <div class="row">
    <div>
      <label for="price">Harga (Rp)</label>
      <input id="price" name="price" type="number" min="1" step="any" required value="<?= e($v["price"] ?? "") ?>">
      <?php if (isset($errors["price"])): ?><p class="error"><?= e($errors["price"]) ?></p><?php endif; ?>
    </div>
    <div>
      <label for="stock">Stok</label>
      <input id="stock" name="stock" type="number" min="0" step="1" required value="<?= e($v["stock"] ?? "") ?>">
      <?php if (isset($errors["stock"])): ?><p class="error"><?= e($errors["stock"]) ?></p><?php endif; ?>
    </div>
  </div>

  <div class="actions">
    <button class="btn" type="submit"><?= e($label) ?></button>
    <a class="btn btn-ghost" href="index.php">Batal</a>
  </div>
</form>
<?php }
