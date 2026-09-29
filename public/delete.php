<?php
// DELETE: hanya POST + token CSRF
require __DIR__ . "/../config/db.php";
require __DIR__ . "/../config/helpers.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    header("Allow: POST");
    page_header("405 Method Not Allowed");
    echo '<div class="alert bad"><strong>405 - Method Not Allowed.</strong> Penghapusan hanya dapat dilakukan melalui metode POST.</div>';
    echo '<a class="btn" href="index.php">&larr; Kembali</a>';
    page_footer();
    exit;
}

csrf_check();
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

$stmt = $pdo->prepare("SELECT id FROM products WHERE id = :id");
$stmt->execute(["id" => $id ?: 0]);
if (!$stmt->fetch()) { header("Location: index.php?status=notfound"); exit; }

$pdo->prepare("DELETE FROM products WHERE id = :id")->execute(["id" => $id]);
header("Location: index.php?status=deleted");
exit;
