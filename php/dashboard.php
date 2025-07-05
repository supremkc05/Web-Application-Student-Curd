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
    error_log("Dashboard error: " . $e->getMessage());
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
    <link href="../assets/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Suprem College</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Home</a>
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
            <h1>Welcome back, <?php echo htmlspecialchars($user['fullname']); ?>!</h1>
            <p class="lead">Manage your student profile and information</p>
        </div>
    </section>

    <!-- Dashboard Content -->
    <div class="container my-5 dashboard-content">
        <!-- Welcome Message -->
        <div class="welcome-message">
            <h3>Welcome back, <?php echo htmlspecialchars($user['fullname']); ?>!</h3>
            <p>Here's your personalized dashboard to manage your student profile and information.</p>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-6 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon">📚</div>
                    <div class="stat-number"><?php echo (isset($user['course']) && $user['course']) ? '1' : '0'; ?></div>
                    <div class="stat-label">Course Enrolled</div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon">📸</div>
                    <div class="stat-number"><?php echo (isset($user['profile_picture']) && $user['profile_picture'] && file_exists("../uploads/" . $user['profile_picture'])) ? '1' : '0'; ?></div>
                    <div class="stat-label">Profile Photo</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Profile Card -->
            <div class="col-lg-4 mb-4">
                <div class="card profile-card">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <?php if (isset($user['profile_picture']) && $user['profile_picture'] && file_exists("../uploads/" . $user['profile_picture'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" 
                                     alt="Profile Picture" class="profile-image">
                            <?php else: ?>
                                <img src="../assets/images/default-avatar.png" 
                                     alt="Default Profile" class="profile-image"
                                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgdmlld0JveD0iMCAwIDEyMCAxMjAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMjAiIGhlaWdodD0iMTIwIiBmaWxsPSIjMDA3YmZmIiBmaWxsLW9wYWNpdHk9IjAuMSIgcng9IjYwIi8+CjxwYXRoIGQ9Ik02MCAzMEM2OS45IDMwIDc4IDM4LjEgNzggNDhDNzggNTcuOSA2OS45IDY2IDYwIDY2QzUwLjEgNjYgNDIgNTcuOSA0MiA0OEM0MiAzOC4xIDUwLjEgMzAgNjAgMzBaTTYwIDEwMkM3Ny4xIDEwMiA5MS4yIDg4LjkgOTEuMiA3MkgyOC44QzI4LjggODguOSA0Mi45IDEwMiA2MCAxMDJaIiBmaWxsPSIjMDA3YmZmIiBmaWxsLW9wYWNpdHk9IjAuNyIvPgo8L3N2Zz4='">
                            <?php endif; ?>
                        </div>
                        <h4><?php echo htmlspecialchars($user['fullname']); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                        <div class="mt-3">
                            <span class="badge badge-primary">Student</span>
                            <?php if (isset($user['course']) && $user['course']): ?>
                                <span class="badge badge-success"><?php echo htmlspecialchars($user['course']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="mt-3">
                            <a href="profile-update.php" class="btn btn-custom">
                                Update Profile
                            </a>
                        </div>
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
                                <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['fullname']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                <p><strong>Primary Phone:</strong> <?php echo (isset($user['phone']) && $user['phone']) ? htmlspecialchars($user['phone']) : '<span class="text-muted">Not provided</span>'; ?></p>
                                <?php if (isset($user['phone_secondary']) && $user['phone_secondary']): ?>
                                    <p><strong>Secondary Phone:</strong> <?php echo htmlspecialchars($user['phone_secondary']); ?></p>
                                <?php endif; ?>
                                <?php if (isset($user['date_of_birth']) && $user['date_of_birth']): ?>
                                    <p><strong>Date of Birth:</strong> <?php echo date('F j, Y', strtotime($user['date_of_birth'])); ?></p>
                                <?php endif; ?>
                                <?php if (isset($user['gender']) && $user['gender']): ?>
                                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
                                <?php endif; ?>
                                <?php if (isset($user['address']) && $user['address']): ?>
                                    <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="col-md-6 mb-4">
                        <div class="card info-card">
                            <div class="card-body">
                                <h5 class="card-title">🎓 Academic Information</h5>
                                <p><strong>Primary Course:</strong> <?php echo (isset($user['course']) && $user['course']) ? htmlspecialchars($user['course']) : '<span class="text-muted">Not specified</span>'; ?></p>
                                <?php if (isset($user['course_secondary']) && $user['course_secondary']): ?>
                                    <p><strong>Secondary Course:</strong> <?php echo htmlspecialchars($user['course_secondary']); ?></p>
                                <?php endif; ?>
                                <?php if (isset($user['course_tertiary']) && $user['course_tertiary']): ?>
                                    <p><strong>Specialization:</strong> <?php echo htmlspecialchars($user['course_tertiary']); ?></p>
                                <?php endif; ?>
                                <p><strong>Status:</strong> 
                                    <?php 
                                    $status = isset($user['status']) ? $user['status'] : 'Active';
                                    $statusClass = $status === 'Active' ? 'success' : ($status === 'Inactive' ? 'warning' : 'danger');
                                    echo "<span class='badge badge-{$statusClass}'>{$status}</span>";
                                    ?>
                                </p>
                                <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                                <p><strong>Last Updated:</strong> <?php echo date('F j, Y g:i A', strtotime($user['updated_at'])); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="col-12">
                        <div class="card quick-actions-card">
                            <div class="card-body">
                                <h5 class="card-title"> Quick Actions</h5>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <a href="profile-update.php" class="btn btn-outline-primary btn-block action-btn">
                                             Edit Profile
                                        </a>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <a href="profile-update.php#photo" class="btn btn-outline-success btn-block action-btn">
                                             Update Photo
                                        </a>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <a href="logout.php" class="btn btn-outline-danger btn-block action-btn" onclick="return confirm('Are you sure you want to logout?')">
                                             Logout
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
