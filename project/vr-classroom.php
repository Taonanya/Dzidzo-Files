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
    <iframe src="https://create.viverse.com/GAbPuiG?_gl=1*3g9otw*_gcl_au*MzY5NzM1NTAzLjE3NDgyODMyMjc.*_ga*MzcwOTg0MTk5LjE3NDgyODMyMjg.*_ga_DGG9JZZMCG*czE3NTA2NzY5NTUkbzExJGcxJHQxNzUwNjc4NzIxJGo0NSRsMCRoMA.."
            frameborder="0" 
            allowfullscreen 
            allow="autoplay; fullscreen; vr" 
            mozallowfullscreen="true" 
            webkitallowfullscreen="true">
    </iframe>
</body>
</html>