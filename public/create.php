<?php
// CREATE + validasi + Post-Redirect-Get
require __DIR__ . "/../config/db.php";
require __DIR__ . "/../config/helpers.php";

$values = ["category" => CATEGORIES[0]];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    csrf_check();
    [$values, $errors] = validate_product($pdo);

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO products (name, category, price, stock)
                 VALUES (:name, :category, :price, :stock)"
            );
            $stmt->execute($values);
            header("Location: index.php?status=created");
            exit; // PRG: hentikan eksekusi setelah redirect
        } catch (PDOException $ex) {
            if ($ex->getCode() === "23000") $errors["name"] = "Nama produk sudah terdaftar.";
            else throw $ex;
        }
    }
}

page_header("Tambah Produk");
?>
<h1>Tambah Produk</h1>
<?php product_form($values, $errors, "create.php", "Simpan produk"); ?>
<?php page_footer(); ?>
