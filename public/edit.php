<?php
// READ one (by ID) + UPDATE + PRG
require __DIR__ . "/../config/db.php";
require __DIR__ . "/../config/helpers.php";

$isPost = $_SERVER["REQUEST_METHOD"] === "POST";
$id = $isPost ? filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT)
              : filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if ($isPost) csrf_check();

$stmt = $pdo->prepare("SELECT id, name, category, price, stock FROM products WHERE id = :id");
$stmt->execute(["id" => $id ?: 0]);
$product = $stmt->fetch();
if (!$product) { header("Location: index.php?status=notfound"); exit; }

$values = $product;
$errors = [];

if ($isPost) {
    [$values, $errors] = validate_product($pdo, (int)$id);

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                "UPDATE products SET name=:name, category=:category, price=:price, stock=:stock
                 WHERE id=:id"
            );
            $stmt->execute($values + ["id" => $id]);
            header("Location: index.php?status=updated");
            exit;
        } catch (PDOException $ex) {
            if ($ex->getCode() === "23000") $errors["name"] = "Nama produk sudah terdaftar.";
            else throw $ex;
        }
    }
}

page_header("Edit Produk");
?>
<h1>Edit Produk</h1>
<?php product_form($values, $errors, "edit.php", "Simpan perubahan", (int)$id); ?>
<?php page_footer(); ?>
