<?php
// upload.php
// Saves uploaded images into /media/uploads/ and returns simple success/error HTML.

declare(strict_types=1);

// --- Config ---
$uploadDir = __DIR__ . '/media/uploads/';        // filesystem path
$uploadUrl = 'media/uploads/';                   // URL path from site root
$maxBytes  = 8 * 1024 * 1024;                    // 8 MB
$allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Create uploads directory if missing
if (!is_dir($uploadDir)) {
  mkdir($uploadDir, 0755, true);
}

// Helper: simple escape
function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo "Method Not Allowed";
  exit;
}

// Validate file presence
if (!isset($_FILES['poop_image']) || $_FILES['poop_image']['error'] !== UPLOAD_ERR_OK) {
  $err = $_FILES['poop_image']['error'] ?? 'no_file';
  http_response_code(400);
  echo "Upload error: " . h((string)$err);
  exit;
}

$file = $_FILES['poop_image'];

// Size check
if ($file['size'] <= 0 || $file['size'] > $maxBytes) {
  http_response_code(400);
  echo "File size must be between 1 byte and " . h((string)$maxBytes) . " bytes.";
  exit;
}

// Extension check
$originalName = $file['name'] ?? '';
$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExt, true)) {
  http_response_code(400);
  echo "Invalid file type. Allowed: " . h(implode(', ', $allowedExt));
  exit;
}

// MIME check (stronger than extension)
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']) ?: '';
$allowedMime = [
  'image/jpeg',
  'image/png',
  'image/gif',
  'image/webp',
];
if (!in_array($mime, $allowedMime, true)) {
  http_response_code(400);
  echo "Invalid MIME type: " . h($mime);
  exit;
}

// Generate a safe unique filename
$random = bin2hex(random_bytes(16));
$newName = $random . '.' . $ext;
$targetPath = $uploadDir . $newName;

// Move the upload
if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
  http_response_code(500);
  echo "Failed to save file.";
  exit;
}

// Permissions (optional but nice)
@chmod($targetPath, 0644);

// Success page (simple)
$imageUrl = $uploadUrl . $newName;
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Upload Complete • ratemypoop.net</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/style-upload.css" />
</head>
<body>
  <header class="site-header">
    <div class="container header-inner">
      <a class="brand" href="index.html">ratemypoop<span class="dot">.net</span></a>
      <nav class="nav">
        <a href="index.html">Home</a>
        <a class="btn" href="upload.html">Upload</a>
      </nav>
    </div>
  </header>

  <main class="container upload-page">
    <h1>Upload complete ✅</h1>
    <p>Your masterpiece has been securely stored in the vault.</p>

    <div class="upload-result">
      <img src="<?php echo h($imageUrl); ?>" alt="Uploaded image preview" />
      <p class="small">
        Direct link: <a href="<?php echo h($imageUrl); ?>"><?php echo h($imageUrl); ?></a>
      </p>
    </div>

    <div class="actions">
      <a class="btn" href="upload.html">Upload another</a>
      <a class="btn secondary" href="index.html">Back to feed</a>
    </div>
  </main>

  <script src="scripts/app.js"></script>
  <script src="scripts/upload.js"></script>
</body>
</html>