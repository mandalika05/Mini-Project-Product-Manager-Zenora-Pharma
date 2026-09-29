<?php
// READ + search/filter (GET) + dashboard statistik
require __DIR__ . "/../config/db.php";
require __DIR__ . "/../config/helpers.php";

$q   = trim($_GET["q"] ?? "");
$cat = trim($_GET["cat"] ?? "");
if ($cat !== "" && !in_array($cat, CATEGORIES, true)) $cat = "";

// Placeholder dibuat unik (:q1, :q2, ...) karena EMULATE_PREPARES = false
$stmt = $pdo->prepare(
    "SELECT id, name, category, price, stock FROM products
     WHERE (name LIKE :q1 OR category LIKE :q2) AND (:c1 = '' OR category = :c2)
     ORDER BY id DESC"
);
$stmt->execute(["q1" => "%$q%", "q2" => "%$q%", "c1" => $cat, "c2" => $cat]);
$products = $stmt->fetchAll();

$stats = $pdo->query(
    "SELECT COUNT(*) AS total, COALESCE(SUM(stock),0) AS units,
            COALESCE(SUM(price*stock),0) AS worth,
            COALESCE(SUM(stock <= " . LOW_STOCK . "),0) AS low FROM products"
)->fetch();

$flash = [
    "created"  => ["ok",  "Obat baru berhasil disimpan ke katalog."],
    "updated"  => ["ok",  "Data obat berhasil diperbarui."],
    "deleted"  => ["ok",  "Obat berhasil dihapus dari katalog."],
    "notfound" => ["bad", "Data obat tidak ditemukan."],
][$_GET["status"] ?? ""] ?? null;

page_header("Katalog Obat");
?>
<section class="hero">
  <div>
    <p class="eyebrow">// KATALOG &amp; KONTROL STOK FARMASI</p>
    <h1>Pusat Kendali Apotek</h1>
  </div>
  <a class="btn" href="create.php">+ Tambah Obat</a>
</section>

<?php if ($flash): ?>
  <div class="alert <?= $flash[0] ?>"><span>● <?= e($flash[1]) ?></span><button type="button" class="close" aria-label="Tutup">×</button></div>
<?php endif; ?>

<section class="stats">
  <div class="card stat"><small>TOTAL JENIS OBAT</small><strong><?= (int)$stats["total"] ?></strong><em>item terdaftar</em></div>
  <div class="card stat"><small>TOTAL UNIT STOK</small><strong><?= number_format((int)$stats["units"], 0, ",", ".") ?></strong><em>unit tersedia</em></div>
  <div class="card stat"><small>NILAI PERSEDIAAN</small><strong class="mono"><?= rupiah($stats["worth"]) ?></strong><em>harga × stok</em></div>
  <div class="card stat"><small>STOK KRITIS</small><strong class="<?= $stats["low"] > 0 ? "warn" : "" ?>"><?= (int)$stats["low"] ?></strong><em>menipis / habis</em></div>
</section>

<form method="GET" class="search">
  <input name="q" placeholder="🔍 Cari nama obat atau kategori..." value="<?= e($q) ?>">
  <select name="cat">
    <option value="">Semua kategori</option>
    <?php foreach (CATEGORIES as $c): ?>
      <option value="<?= e($c) ?>" <?= $cat === $c ? "selected" : "" ?>><?= e($c) ?></option>
    <?php endforeach; ?>
  </select>
  <button class="btn">Cari</button>
</form>

<?php if (!$products): ?>
  <p class="empty">Tidak ada obat yang cocok dengan pencarian.</p>
<?php endif; ?>

<section class="products">
<?php foreach ($products as $p):
    $ci = (int)array_search($p["category"], CATEGORIES, true);
    $badge = $p["stock"] == 0 ? ["out", "HABIS"] : ($p["stock"] <= LOW_STOCK ? ["low", "MENIPIS"] : ["ok", "TERSEDIA"]); ?>
  <article class="card product">
    <div class="p-top">
      <span class="tag c<?= $ci ?>"><?= e($p["category"]) ?></span>
      <span class="code">ZP-<?= str_pad((string)$p["id"], 4, "0", STR_PAD_LEFT) ?></span>
    </div>
    <h3><?= e($p["name"]) ?></h3>
    <p class="price"><?= rupiah($p["price"]) ?></p>
    <div class="stock-row">
      <span>Stok <strong><?= (int)$p["stock"] ?></strong></span>
      <span class="badge <?= $badge[0] ?>"><?= $badge[1] ?></span>
    </div>
    <div class="bar"><span class="<?= $badge[0] ?>" style="width:<?= min(100, (int)$p["stock"]) ?>%"></span></div>
    <div class="actions">
      <a class="btn btn-ghost" href="edit.php?id=<?= (int)$p["id"] ?>">✏️ Edit</a>
      <form method="POST" action="delete.php"
            onsubmit="return confirm(<?= e(json_encode("Hapus " . $p["name"] . "?")) ?>)">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int)$p["id"] ?>">
        <button class="btn btn-danger">🗑 Hapus</button>
      </form>
    </div>
  </article>
<?php endforeach; ?>
</section>
<?php page_footer(); ?>
