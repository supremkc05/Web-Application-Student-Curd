<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - Home</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/index.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Suprem College</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="signup.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-4 mb-4">Welcome to Student Portal</h1>
            <p class="lead mb-4">Your comprehensive platform for managing student information and academic profiles</p>
            <a href="login.php" class="btn btn-light btn-lg mr-3">Login</a>
            <a href="signup.php" class="btn btn-outline-light btn-lg">Register</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="text-center">Portal Features</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="card feature-card text-center">
                        <div class="card-body">
                            <h3>Profile Management</h3>
                            <p>Update your personal information, contact details, and academic course information easily.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card text-center">
                        <div class="card-body">
                            <h3>Photo Upload</h3>
                            <p>Upload and manage your profile picture to personalize your student account.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card text-center">
                        <div class="card-body">
                            <h3>Secure Access</h3>
                            <p>Your data is protected with secure authentication and encrypted password storage.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h2>About Student Portal</h2>
                    <p>The Student Portal is a comprehensive web application designed to help students manage their academic profiles and personal information efficiently.</p>
                    <ul>
                        <li>Easy profile management</li>
                        <li>Secure login system</li>
                        <li>Photo upload functionality</li>
                        <li>Course information tracking</li>
                        <li>Responsive design for all devices</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Getting Started</h5>
                            <p class="card-text">Ready to join our student community?</p>
                            <ol>
                                <li>Click the "Register" button to create your account</li>
                                <li>Fill out your student information</li>
                                <li>Login with your credentials</li>
                                <li>Complete your profile and upload your photo</li>
                            </ol>
                            <a href="signup.php" class="btn btn-primary">Get Started</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Student-Portal. Created by Suprem Khatri. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
