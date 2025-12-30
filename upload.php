<?php
// upload.php
// Saves uploaded images into /media/uploads/ and returns simple success/error HTML.

declare(strict_types=1);

//important things to organize later

require __DIR__ . "/scripts/db.php"; // path from root: ratemypoop/upload.php -> ratemypoop/scripts/db.php

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

//prepping for the database
$originalName = $_FILES['poop_image']['name'];
$storedName   = $newName;               // the random filename we generated
$mimeType     = $mime;                  // the validated mime we computed
$fileSize     = (int) $_FILES['poop_image']['size'];

//DB stuff
$stmt = $pdo->prepare("
  INSERT INTO uploads (db_file_name, original_user_file_name, mime_type, file_size)
  VALUES (:db_file_name, :original_user_file_name, :mime_type, :file_size)
");

$stmt->execute([
  ":db_file_name"   => $storedName,
  ":original_user_file_name" => $originalName,
  ":mime_type"     => $mime,
  ":file_size"     => $fileSize,
]);

$newId = (int)$pdo->lastInsertId();

//sanity check
//echo "<p>DB row created! Upload ID: <strong>{$newId}</strong></p>";

// Permissions (optional but nice)
@chmod($targetPath, 0644);

// Success page (simple)
$imageUrl = $uploadUrl . $storedName;

// Redirect to homepage (or a view page later)
//header("Location: index.html?uploaded=1&id=" . $newId);
// Redirect to success page for minimal verification UX
header("Location: success.php?id=" . $newId);
exit;

?>