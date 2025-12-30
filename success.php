<?php
//success.php

declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  http_response_code(400);
  exit("Missing or invalid id.");
}

require __DIR__ . "/scripts/db.php"; // path from root: ratemypoop/upload.php -> ratemypoop/scripts/db.php

try {
  $pdo = new PDO(
    "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
    $DB_USER,
    $DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
  );

  $stmt = $pdo->prepare("SELECT id, db_file_name, original_user_file_name, mime_type, created_at FROM uploads WHERE id = ?");
  $stmt->execute([$id]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$row) {
    http_response_code(404);
    exit("No record found for id " . htmlspecialchars((string)$id));
  }

} catch (Throwable $e) {
  exit("DB error: " . htmlspecialchars($e->getMessage()));
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Upload Success • ratemypoop.net</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/style-upload.css" />
</head>
<body>
  <main class="container">
    <h1>✅ Upload successful</h1>

    <p><strong>DB ID:</strong> <?php echo (int)$row['id']; ?></p>

    <div class="preview">
      <p><strong>Original name:</strong> <?php echo htmlspecialchars($row['original_user_file_name']); ?></p>
      <img
        src="/media/uploads/<?php echo htmlspecialchars($row['db_file_name']); ?>"
        alt="Uploaded image preview"
      />
      <p>Quick Link: <a href="/media/uploads/<?php echo htmlspecialchars($row['db_file_name']); ?>"><?php echo htmlspecialchars($row['db_file_name']); ?></a></p>
      <p>
        <strong>MIME:</strong> <?php echo htmlspecialchars($row['mime_type']); ?>
        <?php if (!empty($row['created_at'])): ?>
          • <strong>Created:</strong> <?php echo htmlspecialchars($row['created_at']); ?>
        <?php endif; ?>
      </p>
    </div>

    <p>
      <a href="upload.html">Upload another</a> • <a href="index.html">Back to home</a>
    </p>
  </main>
</body>
</html>