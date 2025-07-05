<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require '../config/database.php';

// Get user information from database
try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 0;
        }
        .profile-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .info-card {
            border-left: 4px solid #2204a8;
            margin-bottom: 20px;
        }
        .btn-custom {
            background: linear-gradient(135deg, #2204a8 0%, #568dc5 100%);
            border: none;
            color: white;
        }
        .btn-custom:hover {
            background: linear-gradient(135deg, #568dc5 0%, #2204a8 100%);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #2204a8;">
        <div class="container">
            <a class="navbar-brand" href="index.php">🎓 Student Portal</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Header -->
    <section class="dashboard-header">
        <div class="container">
            <h1>Welcome back, <?php echo htmlspecialchars($user['fullname']); ?>! 👋</h1>
            <p class="lead">Manage your student profile and information</p>
        </div>
    </section>

    <!-- Dashboard Content -->
    <div class="container my-5">
        <div class="row">
            <!-- Profile Card -->
            <div class="col-lg-4 mb-4">
                <div class="card profile-card">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <?php if ($user['profile_picture'] && file_exists("../uploads/" . $user['profile_picture'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" 
                                     alt="Profile Picture" class="profile-image">
                            <?php else: ?>
                                <img src="../assets/images/default-avatar.png" 
                                     alt="Default Profile" class="profile-image"
                                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTUwIiBoZWlnaHQ9IjE1MCIgdmlld0JveD0iMCAwIDE1MCAxNTAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxNTAiIGhlaWdodD0iMTUwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik03NSAzN0M4Ny43IDM3IDk4IDQ3LjMgOTggNjBDOTggNzIuNyA4Ny43IDgzIDc1IDgzQzYyLjMgODMgNTIgNzIuNyA1MiA2MEM1MiA0Ny4zIDYyLjMgMzcgNzUgMzdaTTc1IDEyOEM5Ni4yIDEyOCAxMTMgMTExLjIgMTEzIDkwSDM3QzM3IDExMS4yIDUzLjggMTI4IDc1IDEyOFoiIGZpbGw9IiM5Q0E0QUYiLz4KPC9zdmc+'">
                            <?php endif; ?>
                        </div>
                        <h4><?php echo htmlspecialchars($user['fullname']); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                        <a href="profile-update.php" class="btn btn-custom">
                            📝 Update Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Information Cards -->
            <div class="col-lg-8">
                <div class="row">
                    <!-- Personal Information -->
                    <div class="col-md-6 mb-4">
                        <div class="card info-card">
                            <div class="card-body">
                                <h5 class="card-title">📋 Personal Information</h5>
                                <p><strong>Full Name:</strong><br><?php echo htmlspecialchars($user['fullname']); ?></p>
                                <p><strong>Email:</strong><br><?php echo htmlspecialchars($user['email']); ?></p>
                                <p><strong>Phone:</strong><br><?php echo $user['phone'] ? htmlspecialchars($user['phone']) : 'Not provided'; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="col-md-6 mb-4">
                        <div class="card info-card">
                            <div class="card-body">
                                <h5 class="card-title">🎓 Academic Information</h5>
                                <p><strong>Course:</strong><br><?php echo $user['course'] ? htmlspecialchars($user['course']) : 'Not specified'; ?></p>
                                <p><strong>Member Since:</strong><br><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                                <p><strong>Last Updated:</strong><br><?php echo date('F j, Y', strtotime($user['updated_at'])); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">⚡ Quick Actions</h5>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <a href="profile-update.php" class="btn btn-outline-primary btn-block">
                                            ✏️ Edit Profile
                                        </a>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <a href="profile-update.php#photo" class="btn btn-outline-success btn-block">
                                            📸 Update Photo
                                        </a>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <a href="logout.php" class="btn btn-outline-danger btn-block">
                                            🚪 Logout
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
