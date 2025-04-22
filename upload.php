<?php
session_start();

// Configuration
$PASSWORD = "admin123"; // Change this to your desired password
$UPLOAD_DIR = "./downloads/"; // Directory to store downloaded files

// Function to format file size
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}

// Create upload directory if it doesn't exist
if (!file_exists($UPLOAD_DIR)) {
    mkdir($UPLOAD_DIR, 0755, true);
}

// Handle login
if (isset($_POST['login'])) {
    if ($_POST['password'] === $PASSWORD) {
        $_SESSION['authenticated'] = true;
    } else {
        $error = "Invalid password";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle file deletion
if (isset($_POST['delete']) && isset($_SESSION['authenticated'])) {
    $fileToDelete = $UPLOAD_DIR . basename($_POST['delete']);
    if (file_exists($fileToDelete)) {
        unlink($fileToDelete);
    }
}

// Handle file download
if (isset($_POST["download"]) && isset($_SESSION['authenticated'])) {
    $filename = basename($_POST['filename']);
    $url = $_POST['url'];
    
    if (!empty($url) && !empty($filename)) {
        $filepath = $UPLOAD_DIR . $filename;
        
        // Create SSL context options
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ],
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                ]
            ]
        ]);
        
        try {
            $file = file_put_contents($filepath, fopen($url, 'r', false, $context));
            
            if($file !== false) {
                $success = "Download complete! " . formatFileSize($file) . " saved as " . $filename;
            } else {
                $error = "Download failed. Please check the URL and try again.";
            }
        } catch (Exception $e) {
            $error = "Download failed: " . $e->getMessage();
        }
    } else {
        $error = "Please provide both URL and filename.";
    }
}

// Get list of downloaded files
$downloadedFiles = [];
if (isset($_SESSION['authenticated'])) {
    $files = glob($UPLOAD_DIR . "*");
    foreach ($files as $file) {
        if (is_file($file)) {
            $downloadedFiles[] = basename($file);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SimplePHPUploader</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background-color: #f8f9fa; }
        .card { margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .file-list { max-height: 300px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!isset($_SESSION['authenticated'])): ?>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-center">Login Required</h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            <form method="post">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3>Download File</h3>
                            <a href="?logout=1" class="btn btn-outline-danger btn-sm">Logout</a>
                        </div>
                        <div class="card-body">
                            <?php if (isset($success)): ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                            <?php endif; ?>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            <form method="post">
                                <div class="mb-3">
                                    <label for="url" class="form-label">Direct File URL</label>
                                    <input type="url" class="form-control" id="url" name="url" value="https://wordpress.org/latest.zip" required>
                                </div>
                                <div class="mb-3">
                                    <label for="filename" class="form-label">Save as</label>
                                    <input type="text" class="form-control" id="filename" name="filename" value="wordpress-latest.zip" required>
                                </div>
                                <button type="submit" name="download" class="btn btn-primary w-100">Download</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3>Downloaded Files</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($downloadedFiles)): ?>
                                <p class="text-muted">No files downloaded yet.</p>
                            <?php else: ?>
                                <div class="file-list">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Filename</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($downloadedFiles as $file): ?>
                                                <tr>
                                                    <td>
                                                        <a href="<?php echo htmlspecialchars($UPLOAD_DIR . $file); ?>" target="_blank">
                                                            <?php echo htmlspecialchars($file); ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <form method="post" style="display: inline;">
                                                            <input type="hidden" name="delete" value="<?php echo htmlspecialchars($file); ?>">
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this file?')">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <footer class="text-center text-muted mt-4" style="font-size: 0.8em;">
        <p>© 2024 Reza Esmaeili. All rights reserved.</p>
        <p>
            <a href="https://github.com/rezazoom/SimplePhpDownloader" target="_blank">⭐ Star this project on GitHub</a> |
            <a href="https://github.com/rezazoom" target="_blank">Follow me on GitHub</a>
        </p>
    </footer>
</body>
</html>
