<?php
session_start();
require_once 'db.php';

// Initialize variables
$isLoggedIn = false;
$currentUser = null;
$isAdmin = false;

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $currentUser = $stmt->fetch();
    $isLoggedIn = true;
    
    // Check if user is admin
    if ($isLoggedIn && isset($currentUser['role']) && $currentUser['role'] === 'admin') {
        $isAdmin = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dzidzo - Augmented Reality Education</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #007BFF;
            --primary-dark: #0056b3;
            --primary-light: #e6f2ff;
            --white: #ffffff;
            --light-gray: #f8f9fa;a
            --dark-gray: #343a40;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
            --gradient: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-gray);
            color: var(--dark-gray);
            line-height: 1.6;
        }
        
        header {
            background: var(--gradient);
            color: var(--white);
            padding: 2rem 0;
            text-align: center;
            position: relative;
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
            z-index: 2;
        }
        
        .header-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.05;
            z-index: 1;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="%23ffffff"><circle cx="25" cy="25" r="3"/><circle cx="75" cy="25" r="3"/><circle cx="25" cy="75" r="3"/><circle cx="75" cy="75" r="3"/></svg>');
            background-size: 60px;
        }
        
        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .tagline {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 1.5rem;
        }
        
        nav {
            background-color: var(--primary-dark);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow);
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            align-items: center;
        }
        
        nav a {
            color: var(--white);
            text-decoration: none;
            margin: 0 1.2rem;
            padding: 0.6rem 1rem;
            font-weight: 500;
            border-radius: 6px;
            transition: var(--transition);
            position: relative;
            display: flex;
            align-items: center;
        }
        
        nav a i {
            margin-right: 8px;
            font-size: 1rem;
        }
        
        nav a:hover {
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }
        
        nav a.active {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        nav a.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 60%;
            height: 3px;
            background-color: var(--white);
            border-radius: 3px;
        }
        
        .auth-buttons {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            gap: 12px;
        }
        
        .btn {
            padding: 0.7rem 1.4rem;
            border-radius: 30px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            font-family: 'Poppins', sans-serif;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .btn-primary {
            background-color: var(--white);
            color: var(--primary-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-light);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        
        .btn-outline {
            background-color: transparent;
            color: var(--white);
            border: 2px solid var(--white);
        }
        
        .btn-outline:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-3px);
        }
        
        main {
            max-width: 1200px;
            margin: 3rem auto;
            padding: 0 20px;
        }
        
        .content-section {
            background-color: var(--white);
            border-radius: 12px;
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            box-shadow: var(--shadow);
            animation: fadeIn 0.5s ease;
            display: none;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .content-section h2 {
            color: var(--primary-dark);
            margin-bottom: 1.8rem;
            font-size: 2.2rem;
            position: relative;
            padding-bottom: 0.8rem;
            display: inline-block;
        }
        
        .content-section h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50%;
            height: 4px;
            background: var(--gradient);
            border-radius: 2px;
        }
        
        .content-section p {
            margin-bottom: 1.8rem;
            color: #555;
            font-size: 1.1rem;
            line-height: 1.7;
        }
        
        .login-container, .registration-container {
            max-width: 500px;
            margin: 3rem auto;
            background-color: var(--white);
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: var(--shadow);
            display: none;
            animation: fadeIn 0.5s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .form-group {
            margin-bottom: 1.8rem;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.8rem;
            font-weight: 500;
            color: var(--dark-gray);
            font-size: 1rem;
        }
        
        .form-group input {
            width: 100%;
            padding: 1rem 1.2rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            transition: var(--transition);
            font-size: 1rem;
        }
        
        .form-group input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.15);
        }
        
        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: var(--gradient);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(0, 91, 187, 0.2);
        }
        
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 91, 187, 0.3);
        }
        
        .message {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            text-align: center;
            font-size: 0.95rem;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .link-text {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            border-bottom: 1px dotted var(--primary-color);
        }
        
        .link-text:hover {
            color: var(--primary-dark);
            text-decoration: none;
            border-bottom: 1px solid var(--primary-dark);
        }
        
        .text-center {
            text-align: center;
        }
        
        .mt-3 {
            margin-top: 1.8rem;
        }
        
        .vr-button {
            display: inline-flex;
            align-items: center;
            padding: 1.2rem 2.5rem;
            background: var(--gradient);
            color: var(--white);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 1.8rem 0;
            transition: var(--transition);
            box-shadow: 0 6px 20px rgba(0, 91, 187, 0.2);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .vr-button i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .vr-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 91, 187, 0.3);
        }
        
        .vr-description {
            font-style: italic;
            color: #666;
            margin-top: 0.5rem;
            font-size: 0.95rem;
        }
        
        .feature-image {
            width: 100%;
            max-height: 450px;
            object-fit: cover;
            border-radius: 12px;
            margin: 2rem 0;
            box-shadow: var(--shadow);
            border: 1px solid rgba(0, 0, 0, 0.1);
            transition: transform 0.5s ease;
        }
        
        .feature-image:hover {
            transform: scale(1.02);
        }
        
        footer {
            background-color: var(--dark-gray);
            color: var();
            text-align: center;
            padding: 3rem 0;
            margin-top: 4rem;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin: 1.5rem 0;
            gap: 1.5rem;
        }
        
        .footer-links a {
            color: var();
            text-decoration: none;
            transition: var(--transition);
            font-weight: 500;
        }
        
        .footer-links a:hover {
            color: var(--primary-light);
        }
        
        .social-icons {
            margin: 2rem 0;
        }
        
        .social-icons a {
            color: var();
            font-size: 1.5rem;
            margin: 0 1rem;
            transition: var(--transition);
        }
        
        .social-icons a:hover {
            color: var(--primary-light);
            transform: translateY(-3px);
        }
        
        .copyright {
            opacity: 0.8;
            font-size: 0.9rem;
            margin-top: 1.5rem;
        }
        
        /* Feature Cards */
        .feature-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            margin: 3rem 0;
            justify-content: center;
        }
        
        .feature-card {
            flex: 1;
            min-width: 280px;
            max-width: 350px;
            background: var(--white);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: var(--shadow);
            transition: var(--transition);
            text-align: center;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }
        
        .feature-card h3 {
            color: var(--primary-dark);
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        
        .feature-card p {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        /* Library Section Styles */
        .library-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2.5rem;
            margin: 3rem 0;
        }
        
        .library-category {
            background-color: var(--white);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }
        
        .library-category::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: var(--gradient);
        }
        
        .library-category:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        
        .textbook-container {
            display: flex;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .textbook-image {
            width: 150px;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }
        
        .textbook-image:hover {
            transform: scale(1.05);
        }
        
        .textbook-list ul {
            margin-left: 1rem;
            list-style-type: none;
        }
        
        .textbook-list li {
            margin-bottom: 0.8rem;
            font-size: 0.95rem;
            position: relative;
            padding-left: 1.5rem;
            line-height: 1.5;
        }
        
        .textbook-list li::before {
            content: '•';
            color: var(--primary-color);
            font-weight: bold;
            position: absolute;
            left: 0;
        }
        
        .library-features {
            margin-top: 4rem;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .feature-item {
            text-align: center;
            background: var(--white);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .feature-item:hover {
            transform: translateY(-5px);
        }
        
        .feature-item img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .feature-item p {
            font-weight: 500;
            color: var(--dark-gray);
        }
        
        /* Hero Section */
        .hero {
            position: relative;
            height: 500px;
            background: var(--gradient);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
            margin-bottom: 4rem;
            border-radius: 12px;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="%23ffffff" opacity="0.05"><circle cx="25" cy="25" r="5"/><circle cx="75" cy="25" r="5"/><circle cx="25" cy="75" r="5"/><circle cx="75" cy="75" r="5"/></svg>');
            background-size: 100px;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 0 20px;
        }
        
        .hero h2 {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .hero-buttons {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        
        /* Stats Section */
        .stats {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin: 4rem 0;
            gap: 1.5rem;
        }
        
        .stat-item {
            text-align: center;
            padding: 2rem;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
            flex: 1;
            min-width: 200px;
            transition: var(--transition);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .stat-item:hover {
            transform: translateY(-5px);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 1rem;
            color: #666;
        }
        
        /* Testimonials */
        .testimonials {
            margin: 4rem 0;
        }
        
        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .testimonial-card {
            background: var(--white);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--shadow);
            position: relative;
            transition: var(--transition);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .testimonial-card:hover {
            transform: translateY(-5px);
        }
        
        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 5rem;
            color: rgba(0, 123, 255, 0.1);
            font-family: serif;
            line-height: 1;
            z-index: 1;
        }
        
        .testimonial-content {
            position: relative;
            z-index: 2;
            margin-bottom: 1.5rem;
            font-style: italic;
            color: #555;
            line-height: 1.7;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
        }
        
        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 1rem;
            border: 3px solid var(--primary-light);
        }
        
        .author-info h4 {
            color: var(--primary-dark);
            margin-bottom: 0.2rem;
        }
        
        .author-info p {
            font-size: 0.8rem;
            color: #777;
        }
        
        /* Responsive Styles */
        @media (max-width: 992px) {
            .hero h2 {
                font-size: 2.5rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
        }
        
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }
            
            .tagline {
                font-size: 1rem;
            }
            
            .nav-container {
                flex-direction: column;
                align-items: center;
            }
            
            nav a {
                margin: 0.5rem 0;
                width: 100%;
                text-align: center;
                justify-content: center;
            }
            
            .auth-buttons {
                position: static;
                transform: none;
                margin-top: 1rem;
                justify-content: center;
                width: 100%;
            }
            
            .hero {
                height: 400px;
            }
            
            .hero h2 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .textbook-container {
                flex-direction: column;
            }
            
            .textbook-image {
                width: 100%;
                height: 200px;
            }
            
            .feature-cards {
                flex-direction: column;
                align-items: center;
            }
            
            .content-section, .login-container, .registration-container {
                padding: 1.5rem;
            }
            
            .stats {
                flex-direction: column;
                align-items: center;
            }
            
            .stat-item {
                width: 100%;
                max-width: 300px;
            }
        }
        
        @media (max-width: 576px) {
            .hero {
                height: 350px;
            }
            
            .hero h2 {
                font-size: 1.8rem;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .vr-button {
                padding: 1rem 1.5rem;
                font-size: 0.9rem;
            }
        }
        
        /* Animation Classes */
        .animate-pop {
            animation: popIn 0.5s ease-out;
        }
        
        @keyframes popIn {
            0% { transform: scale(0.8); opacity: 0; }
            80% { transform: scale(1.05); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        /* Floating Animation */
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
        
        /* AI Chatbot Styles */
        .ai-chatbot-container {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            font-family: 'Poppins', sans-serif;
        }
        
        .ai-chatbot-toggle {
            width: 70px;
            height: 70px;
            background: var(--gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(0, 91, 187, 0.3);
            transition: var(--transition);
            border: none;
            outline: none;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(0, 123, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
        }
        
        .ai-chatbot-toggle:hover {
            transform: scale(1.1) rotate(10deg);
        }
        
        .ai-chatbot-window {
            width: 380px;
            height: 550px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            display: none;
            flex-direction: column;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .ai-chatbot-window.active {
            display: flex;
            transform: translateY(0);
            opacity: 1;
        }
        
        .ai-chatbot-header {
            background: var(--gradient);
            color: white;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .ai-chatbot-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
        }
        
        .ai-chatbot-header h3 i {
            margin-right: 10px;
        }
        
        .ai-chatbot-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .ai-chatbot-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }
        
        .ai-chatbot-body {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background-color: #f9f9f9;
            display: flex;
            flex-direction: column;
        }
        
        .ai-message {
            max-width: 80%;
            padding: 12px 16px;
            margin-bottom: 15px;
            border-radius: 18px;
            font-size: 0.95rem;
            line-height: 1.5;
            animation: messageIn 0.3s ease-out;
            position: relative;
        }
        
        @keyframes messageIn {
            from { transform: translateY(10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .ai-bot-message {
            background: white;
            border: 1px solid #e0e0e0;
            align-self: flex-start;
            border-bottom-left-radius: 5px;
            color: #333;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .ai-user-message {
            background: var(--primary-color);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 91, 187, 0.2);
        }
        
        .ai-chatbot-input {
            padding: 15px;
            border-top: 1px solid #eee;
            display: flex;
            background: white;
        }
        
        .ai-chatbot-input input {
            flex: 1;
            padding: 12px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            outline: none;
            font-family: 'Poppins', sans-serif;
            transition: var(--transition);
            font-size: 0.95rem;
        }
        
        .ai-chatbot-input input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.15);
        }
        
        .ai-chatbot-send {
            background: var(--gradient);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-left: 10px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
        
        .ai-chatbot-send:hover {
            transform: scale(1.05);
        }
        
        .ai-quick-replies {
            display: flex;
            flex-wrap: wrap;
            margin-top: 15px;
            gap: 10px;
        }
        
        .ai-quick-reply {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 20px;
            padding: 8px 15px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .ai-quick-reply:hover {
            background: var(--primary-light);
            border-color: var(--primary-color);
            color: var(--primary-dark);
        }
        
        .ai-typing {
            display: inline-block;
            padding: 12px 16px;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 18px;
            border-bottom-left-radius: 5px;
            margin-bottom: 15px;
            align-self: flex-start;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .ai-typing span {
            height: 10px;
            width: 10px;
            background: #aaa;
            border-radius: 50%;
            display: inline-block;
            margin: 0 3px;
            animation: typing 1s infinite ease-in-out;
        }
        
        .ai-typing span:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .ai-typing span:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes typing {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }


.stat-icon {
    width: 60px;
    height: 60px;
    background: var(--primary-light);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1.5rem;
    color: var(--primary-color);
    font-size: 1.5rem;
}

.stat-content h3 {
    font-size: 1rem;
    color: #666;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.stat-number {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--primary-dark);
    margin-bottom: 0.3rem;
}

.stat-change {
    font-size: 0.8rem;
    font-weight: 500;
}

.stat-change.positive {
    color: #28a745;
}

.stat-change.negative {
    color: #dc3545;
}

.stat-change i {
    margin-right: 3px;
}

.admin-panels {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
    margin-top: 2rem;
}

.admin-panel {
    background: var(--white);
    border-radius: 12px;
    box-shadow: var(--shadow);
    border: 1px solid rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.half-panel {
    grid-column: span 1;
}

.panel-header {
    padding: 1.2rem 1.5rem;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--primary-light);
}

.panel-header h3 {
    margin: 0;
    font-size: 1.1rem;
    color: var(--primary-dark);
    display: flex;
    align-items: center;
}

.panel-header h3 i {
    margin-right: 10px;
    font-size: 1rem;
}

.panel-action {
    font-size: 0.85rem;
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition);
}

.panel-action:hover {
    color: var(--primary-dark);
    text-decoration: underline;
}

.panel-action-btn {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
}

.panel-action-btn i {
    margin-right: 5px;
}

.panel-action-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
}

.panel-content {
    padding: 1.5rem;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}

.admin-table th {
    text-align: left;
    padding: 0.8rem;
    background: #f5f5f5;
    color: #555;
    font-weight: 600;
    border-bottom: 2px solid #eee;
}

.admin-table td {
    padding: 0.8rem;
    border-bottom: 1px solid #eee;
    color: #555;
}

.admin-table tr:last-child td {
    border-bottom: none;
}

.admin-table tr:hover td {
    background: #f9f9f9;
}

.status-badge {
    display: inline-block;
    padding: 0.3rem 0.6rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.active {
    background: #d4edda;
    color: #155724;
}

.status-badge.pending {
    background: #fff3cd;
    color: #856404;
}

.status-badge.inactive {
    background: #f8d7da;
    color: #721c24;
}

.status-badge.published {
    background: #d1ecf1;
    color: #0c5460;
}

.status-badge.draft {
    background: #e2e3e5;
    color: #383d41;
}

.table-btn {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: none;
    background: transparent;
    cursor: pointer;
    transition: var(--transition);
    color: #666;
}

.table-btn:hover {
    background: #f0f0f0;
    color: var(--primary-color);
}

.table-btn.view {
    color: var(--primary-color);
}

.table-btn.edit {
    color: #ffc107;
}

.time-filter {
    display: flex;
    gap: 0.5rem;
}

.time-btn {
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    border: 1px solid #ddd;
    background: white;
    font-size: 0.8rem;
    cursor: pointer;
    transition: var(--transition);
}

.time-btn.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.time-btn:hover:not(.active) {
    background: #f5f5f5;
}

.chart-container {
    width: 100%;
    height: 250px;
    position: relative;
}

.resource-list {
    display: grid;
    gap: 1rem;
}

.resource-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #eee;
    transition: var(--transition);
}

.resource-item:hover {
    background: #f9f9f9;
    transform: translateX(5px);
}

.resource-icon {
    width: 40px;
    height: 40px;
    background: var(--primary-light);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    color: var(--primary-color);
}

.resource-info {
    flex: 1;
}

.resource-info h4 {
    margin: 0 0 0.2rem;
    font-size: 0.95rem;
    color: #333;
}

.resource-info p {
    margin: 0;
    font-size: 0.8rem;
    color: #777;
}

.resource-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.resource-btn {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: none;
    background: transparent;
    cursor: pointer;
    transition: var(--transition);
    color: #666;
}

.resource-btn:hover {
    background: #f0f0f0;
    color: var(--primary-color);
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.quick-action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.5rem 0.5rem;
    border-radius: 8px;
    border: 1px solid #eee;
    background: white;
    cursor: pointer;
    transition: var(--transition);
}

.quick-action-btn:hover {
    background: var(--primary-light);
    border-color: var(--primary-color);
    transform: translateY(-3px);
    color: var(--primary-dark);
}

.action-icon {
    width: 40px;
    height: 40px;
    background: var(--primary-light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.8rem;
    color: var(--primary-color);
    font-size: 1.1rem;
}

.quick-action-btn span {
    font-size: 0.85rem;
    font-weight: 500;
    text-align: center;
}

@media (min-width: 992px) {
    .admin-panels {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 768px) {
    .quick-actions {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .admin-table {
        display: block;
        overflow-x: auto;
    }
}
  /* Admin Dashboard Styles */
  .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .admin-stat-card {
            display: flex;
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: var(--transition);
        }
        
        .admin-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            background: var(--primary-light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.5rem;
            color: var(--primary-color);
            font-size: 1.5rem;
        }
        
        .stat-content h3 {
            font-size: 1rem;
            color: #666;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 0.3rem;
        }
        
        .stat-change {
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .stat-change.positive {
            color: #28a745;
        }
        
        .stat-change.negative {
            color: #dc3545;
        }
        
        .stat-change i {
            margin-right: 3px;
        }
        
        .admin-panels {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .admin-panel {
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .half-panel {
            grid-column: span 1;
        }
        
        .panel-header {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--primary-light);
        }
        
        .panel-header h3 {
            margin: 0;
            font-size: 1.1rem;
            color: var(--primary-dark);
            display: flex;
            align-items: center;
        }
        
        .panel-header h3 i {
            margin-right: 10px;
            font-size: 1rem;
        }
        
        .panel-action {
            font-size: 0.85rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .panel-action:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .panel-action-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
        }
        
        .panel-action-btn i {
            margin-right: 5px;
        }
        
        .panel-action-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .panel-content {
            padding: 1.5rem;
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        
        .admin-table th {
            text-align: left;
            padding: 0.8rem;
            background: #f5f5f5;
            color: #555;
            font-weight: 600;
            border-bottom: 2px solid #eee;
        }
        
        .admin-table td {
            padding: 0.8rem;
            border-bottom: 1px solid #eee;
            color: #555;
        }
        
        .admin-table tr:last-child td {
            border-bottom: none;
        }
        
        .admin-table tr:hover td {
            background: #f9f9f9;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.3rem 0.6rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .status-badge.active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-badge.inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-badge.published {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-badge.draft {
            background: #e2e3e5;
            color: #383d41;
        }
        
        .table-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: var(--transition);
            color: #666;
        }
        
        .table-btn:hover {
            background: #f0f0f0;
            color: var(--primary-color);
        }
        
        .table-btn.view {
            color: var(--primary-color);
        }
        
        .table-btn.edit {
            color: #ffc107;
        }
        
        .table-btn.delete {
            color: #dc3545;
        }
        
        .time-filter {
            display: flex;
            gap: 0.5rem;
        }
        
        .time-btn {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            border: 1px solid #ddd;
            background: white;
            font-size: 0.8rem;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .time-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .time-btn:hover:not(.active) {
            background: #f5f5f5;
        }
        
        .chart-container {
            width: 100%;
            height: 250px;
            position: relative;
        }
        
        .resource-list {
            display: grid;
            gap: 1rem;
        }
        
        .resource-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #eee;
            transition: var(--transition);
        }
        
        .resource-item:hover {
            background: #f9f9f9;
            transform: translateX(5px);
        }
        
        .resource-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-light);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: var(--primary-color);
        }
        
        .resource-info {
            flex: 1;
        }
        
        .resource-info h4 {
            margin: 0 0 0.2rem;
            font-size: 0.95rem;
            color: #333;
        }
        
        .resource-info p {
            margin: 0;
            font-size: 0.8rem;
            color: #777;
        }
        
        .resource-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .resource-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: var(--transition);
            color: #666;
        }
        
        .resource-btn:hover {
            background: #f0f0f0;
            color: var(--primary-color);
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }
        
        .quick-action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 0.5rem;
            border-radius: 8px;
            border: 1px solid #eee;
            background: white;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .quick-action-btn:hover {
            background: var(--primary-light);
            border-color: var(--primary-color);
            transform: translateY(-3px);
            color: var(--primary-dark);
        }
        
        .action-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.8rem;
            color: var(--primary-color);
            font-size: 1.1rem;
        }
        
        .quick-action-btn span {
            font-size: 0.85rem;
            font-weight: 500;
            text-align: center;
        }
        
        @media (min-width: 992px) {
            .admin-panels {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .quick-actions {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .admin-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-bg"></div>
        <div class="header-content">
            <h1>Dzidzo Augmented Reality Education</h1>
            <p class="tagline">Transforming learning through immersive technology</p>
            <div class="auth-buttons">
                <?php if ($isLoggedIn): ?>
                    <button class="btn btn-outline logout-btn" id="logout-button" onclick="logout()">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                <?php else: ?>
                    <button class="btn btn-primary" id="login-button" onclick="showSection('login')">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                    <button class="btn btn-outline" onclick="showSection('register')">
                        <i class="fas fa-user-plus"></i> Register
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <nav>
        <div class="nav-container">
            <a href="#" onclick="showSection('home')" id="nav-home">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="#" onclick="showSection('library')" id="nav-library">
                <i class="fas fa-book"></i> Library
            </a>
            <a href="#" onclick="showSection('classes')" id="nav-classes">
                <i class="fas fa-vr-cardboard"></i> Classes
            </a>
            <a href="#" onclick="showSection('about')" id="nav-about">
                <i class="fas fa-info-circle"></i> About
            </a>
            <?php if ($isAdmin): ?>
    <a href="#" onclick="showSection('admin')" id="nav-admin">
        <i class="fas fa-shield-alt"></i> Admin
    </a>
<?php endif; ?>
            <?php if (!$isLoggedIn): ?>
                <a href="#" onclick="showSection('register')" id="nav-register">
                    <i class="fas fa-user-plus"></i> Register
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h2>Experience Learning Like Never Before</h2>
                <p>Immerse yourself in interactive 3D lessons, virtual laboratories, and augmented reality textbooks that bring education to life.</p>
                <div class="hero-buttons">
                    <?php if ($isLoggedIn): ?>
                        <a href="#" onclick="showSection('classes')" class="btn btn-primary">
                            <i class="fas fa-vr-cardboard"></i> Enter Classroom
                        </a>
                    <?php else: ?>
                        <a href="#" onclick="showSection('register')" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Get Started
                        </a>
                        <a href="#" onclick="showSection('login')" class="btn btn-outline">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>

         <!-- Admin Dashboard Section -->
         <section id="admin" class="content-section">
            <h2>Admin Dashboard</h2>
            
            <div class="admin-stats">
                <div class="admin-stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Users</h3>
                        <div class="stat-number">1,248</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> 12% this month
                        </div>
                    </div>
                </div>
                
                <div class="admin-stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-vr-cardboard"></i>
                    </div>
                    <div class="stat-content">
                        <h3>VR Sessions</h3>
                        <div class="stat-number">3,567</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> 24% this month
                        </div>
                    </div>
                </div>
                
                <div class="admin-stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-content">
                        <h3>AR Resources</h3>
                        <div class="stat-number">512</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> 8 new this week
                        </div>
                    </div>
                </div>
                
                <div class="admin-stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Engagement</h3>
                        <div class="stat-number">78%</div>
                        <div class="stat-change negative">
                            <i class="fas fa-arrow-down"></i> 2% from last month
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="admin-panels">
                <div class="admin-panel">
                    <div class="panel-header">
                        <h3><i class="fas fa-user-plus"></i> Recent Registrations</h3>
                        <a href="#" class="panel-action">View All</a>
                    </div>
                    <div class="panel-content">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Joined</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>John Doe</td>
                                    <td>john@example.com</td>
                                    <td>2 days ago</td>
                                    <td><span class="status-badge active">Active</span></td>
                                    <td>
                                        <button class="table-btn view"><i class="fas fa-eye"></i></button>
                                        <button class="table-btn edit"><i class="fas fa-edit"></i></button>
                                        <button class="table-btn delete"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Jane Smith</td>
                                    <td>jane@example.com</td>
                                    <td>5 days ago</td>
                                    <td><span class="status-badge active">Active</span></td>
                                    <td>
                                        <button class="table-btn view"><i class="fas fa-eye"></i></button>
                                        <button class="table-btn edit"><i class="fas fa-edit"></i></button>
                                        <button class="table-btn delete"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Bob Johnson</td>
                                    <td>bob@example.com</td>
                                    <td>1 week ago</td>
                                    <td><span class="status-badge pending">Pending</span></td>
                                    <td>
                                        <button class="table-btn view"><i class="fas fa-eye"></i></button>
                                        <button class="table-btn edit"><i class="fas fa-edit"></i></button>
                                        <button class="table-btn delete"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Alice Brown</td>
                                    <td>alice@example.com</td>
                                    <td>1 week ago</td>
                                    <td><span class="status-badge active">Active</span></td>
                                    <td>
                                        <button class="table-btn view"><i class="fas fa-eye"></i></button>
                                        <button class="table-btn edit"><i class="fas fa-edit"></i></button>
                                        <button class="table-btn delete"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Charlie Wilson</td>
                                    <td>charlie@example.com</td>
                                    <td>2 weeks ago</td>
                                    <td><span class="status-badge inactive">Inactive</span></td>
                                    <td>
                                        <button class="table-btn view"><i class="fas fa-eye"></i></button>
                                        <button class="table-btn edit"><i class="fas fa-edit"></i></button>
                                        <button class="table-btn delete"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="admin-panel">
                    <div class="panel-header">
                        <h3><i class="fas fa-chart-bar"></i> Platform Activity</h3>
                        <div class="time-filter">
                            <button class="time-btn active">Week</button>
                            <button class="time-btn">Month</button>
                            <button class="time-btn">Year</button>
                        </div>
                    </div>
                    <div class="panel-content">
                        <div class="chart-container">
                            <canvas id="activityChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="admin-panel half-panel">
                    <div class="panel-header">
                        <h3><i class="fas fa-book"></i> Resource Management</h3>
                        <button class="panel-action-btn">
                            <i class="fas fa-plus"></i> Add New
                        </button>
                    </div>
                    <div class="panel-content">
                        <div class="resource-list">
                            <div class="resource-item">
                                <div class="resource-icon">
                                    <i class="fas fa-atom"></i>
                                </div>
                                <div class="resource-info">
                                    <h4>3D Molecular Structures</h4>
                                    <p>Chemistry • 45 models</p>
                                </div>
                                <div class="resource-actions">
                                    <span class="status-badge published">Published</span>
                                    <button class="resource-btn"><i class="fas fa-ellipsis-v"></i></button>
                                </div>
                            </div>
                            
                            <div class="resource-item">
                                <div class="resource-icon">
                                    <i class="fas fa-heartbeat"></i>
                                </div>
                                <div class="resource-info">
                                    <h4>Human Anatomy</h4>
                                    <p>Medicine • 12 systems</p>
                                </div>
                                <div class="resource-actions">
                                    <span class="status-badge published">Published</span>
                                    <button class="resource-btn"><i class="fas fa-ellipsis-v"></i></button>
                                </div>
                            </div>
                            
                            <div class="resource-item">
                                <div class="resource-icon">
                                    <i class="fas fa-landmark"></i>
                                </div>
                                <div class="resource-info">
                                    <h4>Historical Sites</h4>
                                    <p>History • 8 locations</p>
                                </div>
                                <div class="resource-actions">
                                    <span class="status-badge draft">Draft</span>
                                    <button class="resource-btn"><i class="fas fa-ellipsis-v"></i></button>
                                </div>
                            </div>
                            
                            <div class="resource-item">
                                <div class="resource-icon">
                                    <i class="fas fa-microscope"></i>
                                </div>
                                <div class="resource-info">
                                    <h4>Virtual Labs</h4>
                                    <p>Physics • 5 experiments</p>
                                </div>
                                <div class="resource-actions">
                                    <span class="status-badge published">Published</span>
                                    <button class="resource-btn"><i class="fas fa-ellipsis-v"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="admin-panel half-panel">
                    <div class="panel-header">
                        <h3><i class="fas fa-tasks"></i> Quick Actions</h3>
                    </div>
                    <div class="panel-content">
                        <div class="quick-actions">
                            <button class="quick-action-btn">
                                <div class="action-icon">
                                    <i class="fas fa-user-cog"></i>
                                </div>
                                <span>Manage Users</span>
                            </button>
                            
                            <button class="quick-action-btn">
                                <div class="action-icon">
                                    <i class="fas fa-file-upload"></i>
                                </div>
                                <span>Upload Resources</span>
                            </button>
                            
                            <button class="quick-action-btn">
                                <div class="action-icon">
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                                <span>View Analytics</span>
                            </button>
                            
                            <button class="quick-action-btn">
                                <div class="action-icon">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <span>System Settings</span>
                            </button>
                            
                            <button class="quick-action-btn">
                                <div class="action-icon">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <span>Notifications</span>
                            </button>
                            
                            <button class="quick-action-btn">
                                <div class="action-icon">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                <span>Support</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <script>
        // Initialize admin chart
        function initAdminChart() {
            const ctx = document.getElementById('activityChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [
                        {
                            label: 'VR Sessions',
                            data: [120, 190, 170, 220, 240, 180, 210],
                            borderColor: 'rgba(0, 123, 255, 1)',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'AR Views',
                            data: [80, 120, 100, 140, 160, 110, 130],
                            borderColor: 'rgba(40, 167, 69, 1)',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Initialize chart when admin section is shown
        document.addEventListener('DOMContentLoaded', function() {
            // Check if we're on the admin page
            if (document.getElementById('admin')) {
                initAdminChart();
            }
            
            // Time filter buttons
            const timeBtns = document.querySelectorAll('.time-btn');
            timeBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    timeBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    // Here you would typically reload the chart data based on the selected time period
                });
            });
            
            // Quick action buttons
            const quickActions = document.querySelectorAll('.quick-action-btn');
            quickActions.forEach(btn => {
                btn.addEventListener('click', function() {
                    const action = this.querySelector('span').textContent;
                    alert(`Action: ${action} would be performed here`);
                });
            });
            
            // Table action buttons
            const viewBtns = document.querySelectorAll('.table-btn.view');
            viewBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const name = row.querySelector('td:first-child').textContent;
                    alert(`Viewing details for: ${name}`);
                });
            });
            
            const editBtns = document.querySelectorAll('.table-btn.edit');
            editBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const name = row.querySelector('td:first-child').textContent;
                    alert(`Editing user: ${name}`);
                });
            });
            
            const deleteBtns = document.querySelectorAll('.table-btn.delete');
            deleteBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to delete this user?')) {
                        const row = this.closest('tr');
                        row.style.opacity = '0.5';
                        row.style.backgroundColor = '#ffecec';
                        setTimeout(() => {
                            row.remove();
                        }, 500);
                    }
                });
            });
        });

        // Modify showSection function to handle admin section
        function showSection(sectionId) {
            // Protected sections that require login
            const protectedSections = ['library', 'classes', 'admin'];
            
            if (protectedSections.includes(sectionId)) {
                if (!<?php echo $isLoggedIn ? 'true' : 'false'; ?>) {
                    showSection('login');
                    document.getElementById('error-message').innerText = "Please login to access this section.";
                    document.getElementById('error-message').style.display = 'block';
                    return;
                }
                
                // Additional check for admin section
                if (sectionId === 'admin' && !<?php echo $isAdmin ? 'true' : 'false'; ?>) {
                    showSection('home');
                    alert('You do not have permission to access the admin dashboard');
                    return;
                }
            }
            
            const sections = document.querySelectorAll('.content-section, .login-container, .registration-container');
            sections.forEach(section => {
                section.style.display = 'none';
            });
            
            const activeSection = document.getElementById(sectionId);
            if (activeSection) {
                activeSection.style.display = 'block';
            } else {
                document.getElementById('home').style.display = 'block';
            }
            
            // Update active nav link
            document.querySelectorAll('nav a').forEach(link => {
                link.classList.remove('active');
            });
            document.getElementById(`nav-${sectionId}`)?.classList.add('active');
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
</body>
</html>
        
        <!-- Stats Section -->
        <div class="stats">
            <div class="stat-item animate-pop" style="animation-delay: 0.1s;">
                <div class="stat-number">10,000+</div>
                <div class="stat-label">Digital Resources</div>
            </div>
            <div class="stat-item animate-pop" style="animation-delay: 0.2s;">
                <div class="stat-number">500+</div>
                <div class="stat-label">AR-Enhanced Materials</div>
            </div>
            <div class="stat-item animate-pop" style="animation-delay: 0.3s;">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Access Anywhere</div>
            </div>
            <div class="stat-item animate-pop" style="animation-delay: 0.4s;">
                <div class="stat-number">100%</div>
                <div class="stat-label">Interactive Learning</div>
            </div>
        </div>
        
        <!-- Home Section -->
        <section id="home" class="content-section">
            <h2>Welcome to the Future of Education</h2>
            <!-- Replace your current image with this 3D container -->
<div class="image-3d-container">
  <div class="image-3d-inner">
    <img src="https://images.unsplash.com/photo-1620712943543-bcc4688e7485?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80&fm=webp" 
         alt="Immersive AR Education" 
         class="feature-image-3d">
  </div>
</div>

<style>
/* 3D Image Container */
.image-3d-container {
  perspective: 1500px;
  margin: 2rem auto;
  width: 100%;
  max-width: 800px;
}

.image-3d-inner {
  transition: transform 0.5s;
  transform-style: preserve-3d;
  position: relative;
  width: 100%;
  height: 450px;
}

.feature-image-3d {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 12px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  transform: translateZ(50px);
  border: 3px solid rgba(0, 123, 255, 0.3);
  filter: drop-shadow(0 10px 20px rgba(0, 91, 187, 0.3));
}

/* Mouse-tilt 3D Effect */
.image-3d-container:hover .image-3d-inner {
  transform: rotateY(10deg) rotateX(5deg);
}

/* Floating Animation */
@keyframes float-3d {
  0%, 100% { transform: translateZ(50px) translateY(0); }
  50% { transform: translateZ(50px) translateY(-20px); }
}

.feature-image-3d {
  animation: float-3d 6s ease-in-out infinite;
}
</style>

<script>
// Enhanced 3D Parallax on Mouse Move
document.querySelector('.image-3d-container').addEventListener('mousemove', (e) => {
  const xAxis = (window.innerWidth / 2 - e.pageX) / 25;
  const yAxis = (window.innerHeight / 2 - e.pageY) / 25;
  document.querySelector('.image-3d-inner').style.transform = `rotateY(${xAxis}deg) rotateX(${yAxis}deg)`;
});
</script>
            <p>Explore the exciting world of augmented reality in education. Our platform revolutionizes the learning process by bringing abstract concepts to life through immersive AR experiences.</p>
            <p>With Dzidzo, students can interact with 3D models, explore virtual environments, and gain a deeper understanding of complex subjects through hands-on, visual learning.</p>
            
            <div class="feature-cards">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-atom"></i>
                    </div>
                    <h3>Interactive Learning</h3>
                    <p>Engage with 3D educational content that makes learning memorable and effective for all types of learners.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-vr-cardboard"></i>
                    </div>
                    <h3>Immersive Experiences</h3>
                    <p>Step into virtual environments that bring lessons to life with realistic simulations and interactive elements.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3>Enhanced Understanding</h3>
                    <p>Visualize complex concepts through augmented reality that makes abstract ideas concrete and understandable.</p>
                </div>
            </div>
        </section>
    
        <!-- Library Section -->
        <section id="library" class="content-section">
            <h2>Knowledge Library</h2>
            <?php if ($isLoggedIn): ?>
            <p>Explore our comprehensive collection of university textbooks and augmented reality learning resources:</p>
            
            <div class="library-grid">
                <!-- Science Textbooks -->
                <div class="library-category">
                    <h3 style="color: var(--primary-dark); margin-bottom: 1rem; border-bottom: 2px solid var(--primary-color); padding-bottom: 0.5rem;">Science & Technology</h3>
                    <div class="textbook-container">
                        <img src="https://images.unsplash.com/photo-1589998059171-988d887df646?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Science textbooks" class="textbook-image">
                        <div class="textbook-list">
                            <ul>
                                <li>University Physics with Modern Physics</li>
                                <li>Molecular Biology of the Cell</li>
                                <li>Organic Chemistry by Clayden</li>
                                <li>Introduction to Algorithms</li>
                                <li>Computer Networking: A Top-Down Approach</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Engineering Textbooks -->
                <div class="library-category">
                    <h3 style="color: var(--primary-dark); margin-bottom: 1rem; border-bottom: 2px solid var(--primary-color); padding-bottom: 0.5rem;">Engineering</h3>
                    <div class="textbook-container">
                        <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Engineering textbooks" class="textbook-image">
                        <div class="textbook-list">
                            <ul>
                                <li>Mechanics of Materials</li>
                                <li>Fundamentals of Electric Circuits</li>
                                <li>Chemical Engineering Design</li>
                                <li>Structures: Or Why Things Don't Fall Down</li>
                                <li>Control Systems Engineering</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Medicine Textbooks -->
                <div class="library-category">
                    <h3 style="color: var(--primary-dark); margin-bottom: 1rem; border-bottom: 2px solid var(--primary-color); padding-bottom: 0.5rem;">Medicine</h3>
                    <div class="textbook-container">
                        <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Medical textbooks" class="textbook-image">
                        <div class="textbook-list">
                            <ul>
                                <li>Gray's Anatomy for Students</li>
                                <li>Robbins and Cotran Pathologic Basis of Disease</li>
                                <li>Harrison's Principles of Internal Medicine</li>
                                <li>Netter's Atlas of Human Anatomy</li>
                                <li>Basic & Clinical Pharmacology</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- AR/VR Resources -->
                <div class="library-category">
                    <h3 style="color: var(--primary-dark); margin-bottom: 1rem; border-bottom: 2px solid var(--primary-color); padding-bottom: 0.5rem;">AR/VR Resources</h3>
                    <div class="textbook-container">
                        <img src="https://images.unsplash.com/photo-1581092921461-39b2f2aa99b3?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="AR/VR resources" class="textbook-image">
                        <div class="textbook-list">
                            <ul>
                                <li>Interactive 3D Human Anatomy</li>
                                <li>Molecular Structures in AR</li>
                                <li>Virtual Engineering Labs</li>
                                <li>Historical Events Recreated in VR</li>
                                <li>Geographical Explorations</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="library-features">
                <h3 style="color: var(--primary-dark); margin: 2rem 0 1rem; text-align: center;">Library Features</h3>
                <div class="features-grid">
                    <div class="feature-item">
                        <img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?ixlib=rb-1.2.1&auto=format&fit=crop&w=300&q=80" alt="Library study area" class="feature-image">
                        <p>24/7 Digital Access to all resources</p>
                    </div>
                    <div class="feature-item">
                        <img src="https://images.unsplash.com/photo-1501504905252-473c47e087f8?ixlib=rb-1.2.1&auto=format&fit=crop&w=300&q=80" alt="Library bookshelves" class="feature-image">
                        <p>Over 10,000 digital textbooks available</p>
                    </div>
                    <div class="feature-item">
                        <img src="https://images.unsplash.com/photo-1588072432836-e10032774350?ixlib=rb-1.2.1&auto=format&fit=crop&w=300&q=80" alt="AR in education" class="feature-image">
                        <p>500+ AR-enhanced learning materials</p>
                    </div>
                </div>
            </div>
            <?php else: ?>
                <div class="message error-message">
                    Please <a href="#" class="link-text" onclick="showSection('login')">login</a> or 
                    <a href="#" class="link-text" onclick="showSection('register')">register</a> to access our library resources.
                </div>
                <img src="https://images.unsplash.com/photo-1501504905252-473c47e087f8?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Library Preview" class="feature-image">
                <p>Our comprehensive library of AR-enhanced learning materials is available to registered users. Gain access to thousands of interactive resources by creating an account.</p>
            <?php endif; ?>
        </section>
            
        <!-- Classes Section -->
        <section id="classes" class="content-section">
            <h2>Immersive Classes</h2>
            <div id="classes-content">
                <?php if ($isLoggedIn): ?>
                    <p>Join our innovative AR-enhanced classes designed to make learning more engaging and effective:</p>
                    <div style="text-align: center;">
                        <a href="vr-classroom.php" class="vr-button" id="vr-classroom-link">
                            <i class="fas fa-vr-cardboard"></i> Join Virtual Reality Classroom 1
                        </a>
                        <p class="vr-description">Experience our fully immersive VR learning environment</p>
                    </div>

                    <div style="text-align: center;">
                        <a href="vr-classroom2.php" class="vr-button" id="vr-classroom-link">
                            <i class="fas fa-vr-cardboard"></i> Join Virtual Reality Classroom 2
                        </a>
                        <p class="vr-description">Experience our fully immersive VR learning environment</p>
                    </div>

                    <p>Our classes combine traditional teaching methods with cutting-edge AR technology to create unforgettable learning experiences that improve retention and understanding.</p>
                    
                    <!-- Testimonials -->
                    <div class="testimonials">
                        <h3 style="color: var(--primary-dark); margin: 3rem 0 1.5rem; text-align: center;">What Our Students Say</h3>
                        <div class="testimonial-grid">
                            <div class="testimonial-card">
                                <div class="testimonial-content">
                                    "The VR classrooms transformed how I understand complex concepts. Being able to interact with 3D models made everything click in a way textbooks and other online learning platforms never could."
                                </div>
                                <div class="testimonial-author">
                                    <img src="IMG-20240531-WA0028" alt="Student" class="author-avatar">
                                    <div class="author-info">
                                        <h4>Randy Mhandu</h4>
                                        <p>CIS Student</p>
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial-card">
                                <div class="testimonial-content">
                                    "As a Computer Science student, the AR simulations have been invaluable. I can visualize computer systems and hardware in ways that were impossible before."
                                </div>
                                <div class="testimonial-author">
                                    <img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Student" class="author-avatar">
                                    <div class="author-info">
                                        <h4>Michael Mafemba</h4>
                                        <p>CS Student</p>
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial-card">
                                <div class="testimonial-content">
                                    "The immersive learning experience has improved my retention dramatically. I'm able to recall information much better when I've interacted with it in VR."
                                </div>
                                <div class="testimonial-author">
                                    <img src="WhatsApp Image 2025-04-11 at 10.55.42_4fe5f31c" alt="Student" class="author-avatar">
                                    <div class="author-info">
                                        <h4>Hermish Paunganwa</h4>
                                        <p>CIS student</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="message error-message">
                        Please <a href="#" class="link-text" onclick="showSection('register'); return false;">register</a> and 
                        <a href="#" class="link-text" onclick="showSection('login'); return false;">login</a> to access our VR classes.
                    </div>
                    <img src="assets/images/vr-preview.jpg" alt="VR Classes Preview" class="feature-image">
                    <p>Our immersive VR classes are available to registered users only. Please create an account to experience our cutting-edge virtual reality classrooms.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="content-section">
            <h2>About Our Mission</h2>
            <p>Dzidzo was founded with a simple goal: to make online learning more practical, exciting, accessible, engaging, and effective through augmented reality, virtual reality and AI technology. Instead of the normal boring classroom lessons or lectures, Dzidzo has brought forth a fun way to learn and enjoy ones studies.</p>
            <p>Our team of educators, developers, and designers work together to create learning experiences that:</p>
            <ul style="margin-left: 2rem; margin-bottom: 1.5rem;">
                <li>Cater to different learning styles</li>
                <li>Bring abstract concepts to life</li>
                <li>Make learning fun and interactive</li>
                <li>Prepare students for the technology-driven future</li>
            </ul>
            
            <div class="feature-cards">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Expert Educators</h3>
                    <p>Our team includes experienced educators who understand how students learn best and design content accordingly.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-code"></i>
                    </div>
                    <h3>Innovative Developers</h3>
                    <p>Skilled developers create immersive experiences that push the boundaries of educational technology.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-paint-brush"></i>
                    </div>
                    <h3>Creative Designers</h3>
                    <p>Talented designers ensure our interfaces are intuitive and our visual content is engaging and effective.</p>
                </div>
            </div>
        </section>
        
        <!-- Login Section -->
        <div class="login-container" id="login">
            <h2>Login to Your Account</h2>
            <form id="login-form" onsubmit="event.preventDefault(); loginUser();">
                <div class="error-message message" id="error-message" style="display: none;"></div>
                <div class="success-message message" id="success-message" style="display: none;"></div>
                
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>    
                
                <button type="button" class="submit-btn" onclick="loginUser()">Login</button>
            </form>
            
            <div class="text-center mt-3">
                Don't have an account? <a href="#" class="link-text" onclick="showSection('register')">Register here</a>
            </div>
            
            <div class="text-center mt-3">
                <a href="#" class="link-text" onclick="showSection('home')">← Back to Home</a>
            </div>
        </div>

        <!-- Registration Section -->
        <div class="registration-container" id="register">
            <h2>Create Your Account</h2>
            <form id="registration-form" onsubmit="event.preventDefault(); registerUser();">
                <div class="form-group">
                    <label for="reg_full_name">Full Name:</label>
                    <input type="text" id="reg_full_name" name="reg_full_name" required>
                </div>
                <div class="form-group">
                    <label for="reg_email">Email:</label>
                    <input type="email" id="reg_email" name="reg_email" required>
                </div>
                <div class="form-group">
                    <label for="reg_username">Username:</label>
                    <input type="text" id="reg_username" name="reg_username" required>
                </div>
                
                <div class="form-group">
                    <label for="reg_password">Password:</label>
                    <input type="password" id="reg_password" name="reg_password" required>
                </div>
                
                <div class="form-group">
                    <label for="reg_confirm_password">Confirm Password:</label>
                    <input type="password" id="reg_confirm_password" name="reg_confirm_password" required>
                </div>
                
                <button id="register-btn" type="button" class="submit-btn" onclick="registerUser()">Register</button>
            </form>
            <div class="text-center mt-3">
                <a href="#" class="link-text" onclick="showSection('home')">← Back to Home</a>
            </div>
        </div>

        <!-- Admin Dashboard Section -->
<section id="admin" class="content-section">
    <h2>Admin Dashboard</h2>
    
    <div class="admin-stats">
        <div class="admin-stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>Total Users</h3>
                <div class="stat-number">1,248</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> 12% this month
                </div>
            </div>
        </div>
        
        <div class="admin-stat-card">
            <div class="stat-icon">
                <i class="fas fa-vr-cardboard"></i>
            </div>
            <div class="stat-content">
                <h3>VR Sessions</h3>
                <div class="stat-number">3,567</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> 24% this month
                </div>
            </div>
        </div>
        
        <div class="admin-stat-card">
            <div class="stat-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-content">
                <h3>AR Resources</h3>
                <div class="stat-number">512</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> 8 new this week
                </div>
            </div>
        </div>
        
        <div class="admin-stat-card">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h3>Engagement</h3>
                <div class="stat-number">78%</div>
                <div class="stat-change negative">
                    <i class="fas fa-arrow-down"></i> 2% from last month
                </div>
            </div>
        </div>
    </div>
    
    <div class="admin-panels">
        <div class="admin-panel">
            <div class="panel-header">
                <h3><i class="fas fa-user-plus"></i> Recent Registrations</h3>
                <a href="#" class="panel-action">View All</a>
            </div>
            <div class="panel-content">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Randy Mhandu</td>
                            <td>randy@example.com</td>
                            <td>2 days ago</td>
                            <td><span class="status-badge active">Active</span></td>
                            <td>
                                <button class="table-btn view"><i class="fas fa-eye"></i></button>
                                <button class="table-btn edit"><i class="fas fa-edit"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>Michael Mafemba</td>
                            <td>michael@example.com</td>
                            <td>5 days ago</td>
                            <td><span class="status-badge active">Active</span></td>
                            <td>
                                <button class="table-btn view"><i class="fas fa-eye"></i></button>
                                <button class="table-btn edit"><i class="fas fa-edit"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>Hermish Paunganwa</td>
                            <td>hermish@example.com</td>
                            <td>1 week ago</td>
                            <td><span class="status-badge pending">Pending</span></td>
                            <td>
                                <button class="table-btn view"><i class="fas fa-eye"></i></button>
                                <button class="table-btn edit"><i class="fas fa-edit"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>Tafadzwa Chigumbura</td>
                            <td>tafadzwa@example.com</td>
                            <td>1 week ago</td>
                            <td><span class="status-badge active">Active</span></td>
                            <td>
                                <button class="table-btn view"><i class="fas fa-eye"></i></button>
                                <button class="table-btn edit"><i class="fas fa-edit"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>Kudzai Moyo</td>
                            <td>kudzai@example.com</td>
                            <td>2 weeks ago</td>
                            <td><span class="status-badge inactive">Inactive</span></td>
                            <td>
                                <button class="table-btn view"><i class="fas fa-eye"></i></button>
                                <button class="table-btn edit"><i class="fas fa-edit"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="admin-panel">
            <div class="panel-header">
                <h3><i class="fas fa-chart-bar"></i> Platform Activity</h3>
                <div class="time-filter">
                    <button class="time-btn active">Week</button>
                    <button class="time-btn">Month</button>
                    <button class="time-btn">Year</button>
                </div>
            </div>
            <div class="panel-content">
                <div class="chart-container">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="admin-panel half-panel">
            <div class="panel-header">
                <h3><i class="fas fa-book"></i> Resource Management</h3>
                <button class="panel-action-btn">
                    <i class="fas fa-plus"></i> Add New
                </button>
            </div>
            <div class="panel-content">
                <div class="resource-list">
                    <div class="resource-item">
                        <div class="resource-icon">
                            <i class="fas fa-atom"></i>
                        </div>
                        <div class="resource-info">
                            <h4>3D Molecular Structures</h4>
                            <p>Chemistry • 45 models</p>
                        </div>
                        <div class="resource-actions">
                            <span class="status-badge published">Published</span>
                            <button class="resource-btn"><i class="fas fa-ellipsis-v"></i></button>
                        </div>
                    </div>
                    
                    <div class="resource-item">
                        <div class="resource-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div class="resource-info">
                            <h4>Human Anatomy</h4>
                            <p>Medicine • 12 systems</p>
                        </div>
                        <div class="resource-actions">
                            <span class="status-badge published">Published</span>
                            <button class="resource-btn"><i class="fas fa-ellipsis-v"></i></button>
                        </div>
                    </div>
                    
                    <div class="resource-item">
                        <div class="resource-icon">
                            <i class="fas fa-landmark"></i>
                        </div>
                        <div class="resource-info">
                            <h4>Historical Sites</h4>
                            <p>History • 8 locations</p>
                        </div>
                        <div class="resource-actions">
                            <span class="status-badge draft">Draft</span>
                            <button class="resource-btn"><i class="fas fa-ellipsis-v"></i></button>
                        </div>
                    </div>
                    
                    <div class="resource-item">
                        <div class="resource-icon">
                            <i class="fas fa-microscope"></i>
                        </div>
                        <div class="resource-info">
                            <h4>Virtual Labs</h4>
                            <p>Physics • 5 experiments</p>
                        </div>
                        <div class="resource-actions">
                            <span class="status-badge published">Published</span>
                            <button class="resource-btn"><i class="fas fa-ellipsis-v"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="admin-panel half-panel">
            <div class="panel-header">
                <h3><i class="fas fa-tasks"></i> Quick Actions</h3>
            </div>
            <div class="panel-content">
                <div class="quick-actions">
                    <button class="quick-action-btn">
                        <div class="action-icon">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <span>Manage Users</span>
                    </button>
                    
                    <button class="quick-action-btn">
                        <div class="action-icon">
                            <i class="fas fa-file-upload"></i>
                        </div>
                        <span>Upload Resources</span>
                    </button>
                    
                    <button class="quick-action-btn">
                        <div class="action-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <span>View Analytics</span>
                    </button>
                    
                    <button class="quick-action-btn">
                        <div class="action-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <span>System Settings</span>
                    </button>
                    
                    <button class="quick-action-btn">
                        <div class="action-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <span>Notifications</span>
                    </button>
                    
                    <button class="quick-action-btn">
                        <div class="action-icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <span>Support</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
    </main>
    
    <footer>
        <div class="er-content">
            <h3 style="color: var(); margin-bottom: 1.5rem;">Dzidzo Augmented Reality Education</h3>
            <div class="footer-links">
                <a href="#" onclick="showSection('home')">Home</a>
                <a href="#" onclick="showSection('library')">Library</a>
                <a href="#" onclick="showSection('classes')">Classes</a>
                <a href="#" onclick="showSection('about')">About</a>
                <a href="#" onclick="showSection('register')">Register</a>
                <a href="#" onclick="showSection('login')">Login</a>
            </div>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
            <p class="copyright">© 2025 Dzidzo Augmented Reality Education. All Rights Reserved.</p>
        </div>
    </footer>
    
    <script>
        let users = []; // Simulated user database
        let currentUser = null;
        
        // Add this function to check authentication
        function checkAuth() {
            return <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
        }

        // Modify showSection function to handle protected sections
        function showSection(sectionId) {
            // Protected sections that require login
            const protectedSections = ['library', 'classes'];
            
            if (protectedSections.includes(sectionId)) {
                if (!checkAuth()) {
                    showSection('login');
                    document.getElementById('error-message').innerText = "Please login to access this section.";
                    document.getElementById('error-message').style.display = 'block';
                    return;
                }
            }
            
            const sections = document.querySelectorAll('.content-section, .login-container, .registration-container');
            sections.forEach(section => {
                section.style.display = 'none';
            });
            
            const activeSection = document.getElementById(sectionId);
            if (activeSection) {
                activeSection.style.display = 'block';
            } else {
                document.getElementById('home').style.display = 'block';
            }
            
            // Update active nav link
            document.querySelectorAll('nav a').forEach(link => {
                link.classList.remove('active');
            });
            document.getElementById(`nav-${sectionId}`)?.classList.add('active');
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Show home section by default
        document.addEventListener('DOMContentLoaded', function() {
            showSection('home');
            
            // Check if user is logged in (for demo purposes)
            if (currentUser) {
                document.getElementById('login-button').style.display = 'none';
                document.getElementById('logout-button').style.display = 'block';
            } else {
                document.getElementById('login-button').style.display = 'block';
                document.getElementById('logout-button').style.display = 'none';
            }
        });

        function registerUser() {
            const fullName = document.getElementById('reg_full_name').value;
            const email = document.getElementById('reg_email').value;
            const username = document.getElementById('reg_username').value;
            const password = document.getElementById('reg_password').value;
            const confirmPassword = document.getElementById('reg_confirm_password').value;

            // Basic validation
            if (password !== confirmPassword) {
                document.getElementById('error-message').innerText = "Passwords don't match";
                document.getElementById('error-message').style.display = 'block';
                return;
            }

            fetch('register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `full_name=${encodeURIComponent(fullName)}&email=${encodeURIComponent(email)}&username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('error-message').style.display = 'none';
                    document.getElementById('success-message').innerText = "Registration successful! Please login.";
                    document.getElementById('success-message').style.display = 'block';
                    
                    // Show login form after registration
                    setTimeout(() => {
                        showSection('login');
                    }, 2000);
                } else {
                    document.getElementById('error-message').innerText = data.message;
                    document.getElementById('error-message').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('error-message').innerText = 'An error occurred during registration.';
                document.getElementById('error-message').style.display = 'block';
            });
        }

        function loginUser() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            fetch('login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('error-message').style.display = 'none';
                    document.getElementById('success-message').innerText = data.message;
                    document.getElementById('success-message').style.display = 'block';
                    
                    // Update UI
                    document.getElementById('login-button').style.display = 'none';
                    document.getElementById('logout-button').style.display = 'block';
                    document.getElementById('login').style.display = 'none';
                    
                    // Redirect to classes section after login
                    setTimeout(() => {
                        showSection('classes');
                        location.reload(); // Refresh to update navigation
                    }, 1000);
                } else {
                    document.getElementById('error-message').innerText = data.message;
                    document.getElementById('error-message').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('error-message').innerText = 'An error occurred during login.';
                document.getElementById('error-message').style.display = 'block';
            });
        }

        function logout() {
            fetch('logout.php')
            .then(() => {
                // Update UI for logged out user
                document.getElementById('logout-button').style.display = 'none';
                document.getElementById('login-button').style.display = 'block';
                
                // Show logout confirmation
                document.getElementById('success-message').innerText = "You have been logged out successfully.";
                document.getElementById('success-message').style.display = 'block';
                document.getElementById('error-message').style.display = 'none';
                
                // Redirect to home after 1 second
                setTimeout(() => {
                    showSection('home');
                    location.reload(); // Refresh to update navigation
                }, 1000);
            });
        }
        // Initialize admin chart
function initAdminChart() {
    const ctx = document.getElementById('activityChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [
                {
                    label: 'VR Sessions',
                    data: [120, 190, 170, 220, 240, 180, 210],
                    borderColor: 'rgba(0, 123, 255, 1)',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'AR Views',
                    data: [80, 120, 100, 140, 160, 110, 130],
                    borderColor: 'rgba(40, 167, 69, 1)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Initialize chart when admin section is shown
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the admin page
    if (document.getElementById('admin')) {
        // Load Chart.js library dynamically
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
        script.onload = initAdminChart;
        document.head.appendChild(script);
    }
});
    </script>
    <!-- Add this right before the closing </body> tag -->
<div class="ai-chatbot-container">
    <button class="ai-chatbot-toggle" id="aiToggle">
        <i class="fas fa-robot"></i>
    </button>
    
    <div class="ai-chatbot-window" id="aiChatWindow">
        <div class="ai-chatbot-header">
            <h3>Dzidzo Learning Assistant</h3>
            <button class="ai-chatbot-close" id="aiClose">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="ai-chatbot-body" id="aiChatBody">
            <div class="ai-message ai-bot-message">
                Hello there! I'm Dzidzo your AI learning assistant. How can I help you explore our AR/VR education platform today?
            </div>
            
            <div class="ai-quick-replies" id="quickReplies">
                <div class="ai-quick-reply" onclick="sendQuickReply('How do I access VR classes?')">Access VR classes</div>
                <div class="ai-quick-reply" onclick="sendQuickReply('What AR resources are available?')">AR resources</div>
                <div class="ai-quick-reply" onclick="sendQuickReply('How do I register?')">Registration help</div>
            </div>
        </div>
        
        <div class="ai-chatbot-input">
            <input type="text" id="aiUserInput" placeholder="Ask me about Dzidziso...">
            <button class="ai-chatbot-send" id="aiSend">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>


<style>
    /* AI Chatbot Styles */
    .ai-chatbot-container {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
        font-family: 'Poppins', sans-serif;
    }
    
    .ai-chatbot-toggle {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        cursor: pointer;
        box-shadow: var(--shadow);
        transition: var(--transition);
        border: none;
        outline: none;
    }
    
    .ai-chatbot-toggle:hover {
        transform: scale(1.1);
    }
    
    .ai-chatbot-window {
        width: 350px;
        height: 500px;
        background: white;
        border-radius: 15px;
        box-shadow: var(--shadow);
        overflow: hidden;
        display: none;
        flex-direction: column;
        transform: translateY(20px);
        opacity: 0;
        transition: all 0.3s ease;
    }
    
    .ai-chatbot-window.active {
        display: flex;
        transform: translateY(0);
        opacity: 1;
    }
    
    .ai-chatbot-header {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        padding: 15px 20px;
        display: flex;
        align-items: center;
    }
    
    .ai-chatbot-header h3 {
        margin: 0;
        font-weight: 600;
        font-size: 1.1rem;
        flex: 1;
    }
    
    .ai-chatbot-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
    }
    
    .ai-chatbot-body {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background-color: #f9f9f9;
        display: flex;
        flex-direction: column;
    }
    
    .ai-message {
        max-width: 80%;
        padding: 10px 15px;
        margin-bottom: 10px;
        border-radius: 18px;
        font-size: 0.9rem;
        line-height: 1.4;
        animation: messageIn 0.2s ease-out;
    }
    
    @keyframes messageIn {
        from { transform: translateY(10px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    .ai-bot-message {
        background: white;
        border: 1px solid #e0e0e0;
        align-self: flex-start;
        border-bottom-left-radius: 5px;
        color: #333;
    }
    
    .ai-user-message {
        background: var(--primary-color);
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 5px;
    }
    
    .ai-chatbot-input {
        padding: 15px;
        border-top: 1px solid #eee;
        display: flex;
        background: white;
    }
    
    .ai-chatbot-input input {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 25px;
        outline: none;
        font-family: 'Poppins', sans-serif;
        transition: var(--transition);
    }
    
    .ai-chatbot-input input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.2);
    }
    
    .ai-chatbot-send {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-left: 10px;
        cursor: pointer;
        transition: var(--transition);
    }
    
    .ai-chatbot-send:hover {
        transform: scale(1.05);
    }
    
    .ai-quick-replies {
        display: flex;
        flex-wrap: wrap;
        margin-top: 10px;
        gap: 8px;
    }
    
    .ai-quick-reply {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 15px;
        padding: 6px 12px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: var(--transition);
    }
    
    .ai-quick-reply:hover {
        background: var(--primary-light);
        border-color: var(--primary-color);
    }
    
    /* Typing indicator */
    .ai-typing {
        display: inline-block;
        padding: 10px 15px;
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 18px;
        border-bottom-left-radius: 5px;
        margin-bottom: 10px;
        align-self: flex-start;
    }
    
    .ai-typing span {
        height: 8px;
        width: 8px;
        background: #aaa;
        border-radius: 50%;
        display: inline-block;
        margin: 0 2px;
        animation: typing 1s infinite ease-in-out;
    }
    
    .ai-typing span:nth-child(2) {
        animation-delay: 0.2s;
    }
    
    .ai-typing span:nth-child(3) {
        animation-delay: 0.4s;
    }
    
    @keyframes typing {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
</style>

<!-- Add this script right before the closing </script> tag -->
<script>
    // AI Chatbot Functionality
    const aiToggle = document.getElementById('aiToggle');
    const aiChatWindow = document.getElementById('aiChatWindow');
    const aiClose = document.getElementById('aiClose');
    const aiChatBody = document.getElementById('aiChatBody');
    const aiUserInput = document.getElementById('aiUserInput');
    const aiSend = document.getElementById('aiSend');
    const quickReplies = document.getElementById('quickReplies');
    
    // Toggle chatbot window
    aiToggle.addEventListener('click', function() {
        this.classList.toggle('active');
        aiChatWindow.classList.toggle('active');
    });
    
    // Close chatbot
    aiClose.addEventListener('click', function() {
        aiToggle.classList.remove('active');
        aiChatWindow.classList.remove('active');
    });
    
    // Send message function
    function sendMessage() {
        const message = aiUserInput.value.trim();
        if (message) {
            // Add user message
            addMessage(message, 'user');
            aiUserInput.value = '';
            
            // Show typing indicator
            showTyping();
            
            // Process message and generate response
            setTimeout(() => {
                // Remove typing indicator
                const typing = document.querySelector('.ai-typing');
                if (typing) typing.remove();
                
                // Generate response
                const response = generateResponse(message);
                addMessage(response, 'bot');
                
                // Scroll to bottom
                aiChatBody.scrollTop = aiChatBody.scrollHeight;
            }, 1000 + Math.random() * 2000); // Random delay for realism
        }
    }
    
    // Send quick reply
    function sendQuickReply(message) {
        aiUserInput.value = message;
        sendMessage();
    }
    
    // Add message to chat
    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('ai-message', `ai-${sender}-message`);
        messageDiv.textContent = text;
        aiChatBody.appendChild(messageDiv);
        
        // Hide quick replies after first message
        if (sender === 'user') {
            quickReplies.style.display = 'none';
        }
        
        // Scroll to bottom
        aiChatBody.scrollTop = aiChatBody.scrollHeight;
    }
    
    // Show typing indicator
    function showTyping() {
        const typingDiv = document.createElement('div');
        typingDiv.classList.add('ai-typing');
        typingDiv.innerHTML = '<span></span><span></span><span></span>';
        aiChatBody.appendChild(typingDiv);
        aiChatBody.scrollTop = aiChatBody.scrollHeight;
    }
    
    // Generate bot responses
    function generateResponse(message) {
        const lowerMsg = message.toLowerCase();
        
        // Common questions about the platform
        if (lowerMsg.includes('hello') || lowerMsg.includes('hi') || lowerMsg.includes('hey')) {
            return "Hi there! I'm here to help you navigate Dzidzo's AR/VR education platform. What would you like to know?";
        }
        else if (lowerMsg.includes('access') || lowerMsg.includes('vr class') || lowerMsg.includes('virtual')) {
            return "To access our VR classes:\n1. Go to the 'Classes' section\n2. Click 'Enter Virtual Reality Classroom'\n3. Put on your VR headset or use desktop mode\n4. Start your immersive learning experience!";
        }
        else if (lowerMsg.includes('ar') || lowerMsg.includes('augmented') || lowerMsg.includes('resource')) {
            return "Our AR resources include:\n- Interactive 3D models for STEM subjects\n- AR-enhanced textbooks\n- Virtual lab simulations\n- Historical recreations\nVisit the 'Library' section to explore them!";
        }
        else if (lowerMsg.includes('register') || lowerMsg.includes('account') || lowerMsg.includes('sign up')) {
            return "To register:\n1. Click 'Register' in the navigation\n2. Fill in your details\n3. Create a username and password\n4. Click 'Register'\nYou'll then be able to access all features!";
        }
        else if (lowerMsg.includes('feature') || lowerMsg.includes('what can') || lowerMsg.includes('offer')) {
            return "Dzidzo offers:\n- Immersive VR classrooms\n- Interactive AR learning materials\n- 3D model library\n- Personalized learning paths\n- Progress tracking\nExplore the different sections to see everything!";
        }
        else if (lowerMsg.includes('help') || lowerMsg.includes('support')) {
            return "For technical support or additional help:\n1. Check our 'About' section\n2. Contact us through the information provided\n3. Our team will respond within 24 hours\nHow else can I assist you?";
        }
        else {
            // Default response if question isn't recognized
            const responses = [
                "I can help you navigate our AR/VR education platform. Try asking about specific features or how to get started!",
                "That's an interesting question! Our platform combines augmented and virtual reality to enhance learning experiences.",
                "I'm designed to help students make the most of Dzidzo's immersive learning tools. What would you like to know more about?",
                "For the best experience, I recommend exploring our VR classrooms and AR library. Would you like details about either?",
                "I'm still learning about all the possibilities of our platform. Could you ask your question in a different way?"
            ];
            return responses[Math.floor(Math.random() * responses.length)];
        }
    }
    
    // Event listeners
    aiSend.addEventListener('click', sendMessage);
    aiUserInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
</script>

<!-- Add this to the <head> section if not already present -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</body>
</html>