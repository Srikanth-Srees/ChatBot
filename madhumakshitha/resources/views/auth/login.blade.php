<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to Aives Australia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Full-screen background video */
        .video-background {
            position: fixed; /* Fixes the video in the background */
            top: 0;
            left: 0;
            width: 100vw; /* Full viewport width */
            height: 100vh; /* Full viewport height */
            object-fit: cover; /* Ensures the video covers the entire area */
            z-index: -1; /* Places the video behind other content */
        }

        /* Login container styling */
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin: 0 auto; /* Centers the container */
            position: absolute; /* Absolute positioning to move the container to the bottom */
            bottom: 15px; /* Distance from the bottom */
            left: 50%; /* Center horizontally */
            transform: translateX(-50%); /* Center horizontally */
            backdrop-filter: blur(10px); /* Optional: adds a blur effect to the background behind the form */
        }

        /* Form styling */
        .login-form h2 {
            margin-bottom: 15px;
            font-size: 24px;
            color: #cd222b;
            text-align: center;
        }

        .form-group {
            padding: 10px;
            margin-bottom: 15px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn-primary {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #304e24;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #283921;
        }

        /* Error message styling */
        .alert-danger {
            background-color: #f8d7da;
            color: #842029;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        /* Responsive adjustments */
        @media (max-width: 600px) {
            .login-container {
                width: 90%;
                bottom: 10px; /* Less distance from the bottom on small screens */
            }
        }
    </style>
</head>
<body>

<video class="video-background" autoplay muted loop>
    <source src="https://madhumakshika.com/Welcome.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<div class="login-container">
    <div class="login-form">
        <h2>Madhumakshika</h2>

        <!-- Display error messages -->
        <div id="error-message" class="alert-danger" style="display: none;">
            <ul>
                <!-- Errors will be displayed here -->
            </ul>
        </div>

        <!-- Login form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required class="form-control">
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required class="form-control">
            </div>
            <button type="submit" class="btn-primary">Login</button>
        </form>
    </div>
</div>


</body>
</html>
