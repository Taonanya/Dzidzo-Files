<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?section=login');
    exit();
}

// Verify the user exists in database
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: index.php?section=register');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>VR Classroom</title>
    <style>
        body { margin: 0; overflow: hidden; }
        iframe { 
            width: 100vw; 
            height: 100vh; 
            border: none;
        }
    </style>
</head>
<body>
    <iframe src="https://skfb.ly/oo9sJ"
            allowfullscreen 
            allow="autoplay; fullscreen; vr" 
            mozallowfullscreen="true" 
            webkitallowfullscreen="true">
    </iframe>
</body>
</html>