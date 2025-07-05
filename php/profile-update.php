<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require '../config/database.php';

$success = '';
$error = '';

// Get user information
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $course = trim($_POST['course']);
    
    // Validation
    $errors = array();
    
    if (empty($fullname)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (!empty($phone) && !preg_match("/^[0-9+\-\s\(\)]+$/", $phone)) {
        $errors[] = "Invalid phone number format";
    }
    
    // Check if email is already taken by another user
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                $errors[] = "Email is already taken by another user";
            }
        } catch (Exception $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
    
    // Handle file upload
    $profile_picture = $user['profile_picture']; // Keep existing if no new upload
    
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $upload_dir = '../uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_info = pathinfo($_FILES['profile_picture']['name']);
        $extension = strtolower($file_info['extension']);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($extension, $allowed_extensions)) {
            $errors[] = "Only JPG, JPEG, PNG, and GIF files are allowed";
        } elseif ($_FILES['profile_picture']['size'] > 5000000) { // 5MB limit
            $errors[] = "File size must be less than 5MB";
        } else {
            $new_filename = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                // Delete old profile picture if exists
                if ($user['profile_picture'] && file_exists($upload_dir . $user['profile_picture'])) {
                    unlink($upload_dir . $user['profile_picture']);
                }
                $profile_picture = $new_filename;
            } else {
                $errors[] = "Failed to upload profile picture";
            }
        }
    }
    
    // Update user information if no errors
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET fullname = ?, email = ?, phone = ?, course = ?, profile_picture = ? WHERE id = ?");
            $result = $stmt->execute([$fullname, $email, $phone, $course, $profile_picture, $_SESSION['user_id']]);
            
            if ($result) {
                $_SESSION['email'] = $email; // Update session email
                $success = "Profile updated successfully!";
                
                // Refresh user data
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch();
            } else {
                $error = "Failed to update profile";
            }
        } catch (Exception $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } else {
        $error = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile - Student Portal</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 0;
        }
        .current-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #2204a8;
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
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <section class="profile-header">
        <div class="container">
            <h1>Update Your Profile</h1>
            <p class="lead">Keep your information current and accurate</p>
        </div>
    </section>

    <!-- Profile Update Form -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="register-form">
                    <h2 class="text-center mb-4">Profile Information</h2>
                    
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="profile-update.php" method="POST" enctype="multipart/form-data">
                        <!-- Current Profile Picture -->
                        <div class="form-group text-center" id="photo">
                            <label>Current Profile Picture</label><br>
                            <?php if ($user['profile_picture'] && file_exists("../uploads/" . $user['profile_picture'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" 
                                     alt="Current Profile" class="current-photo mb-3">
                            <?php else: ?>
                                <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgdmlld0JveD0iMCAwIDEyMCAxMjAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMjAiIGhlaWdodD0iMTIwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik02MCAzMEM2OS45IDMwIDc4IDM4LjEgNzggNDhDNzggNTcuOSA2OS45IDY2IDYwIDY2QzUwLjEgNjYgNDIgNTcuOSA0MiA0OEM0MiAzOC4xIDUwLjEgMzAgNjAgMzBaTTYwIDEwMkM3NyAxMDIgOTEgODguOSA5MSA3Mkg5MUMyOSA4OC45IDQzIDEwMiA2MCAxMDJaIiBmaWxsPSIjOUNBNEFGIi8+Cjwvc3ZnPg==" 
                                     alt="No Profile Picture" class="current-photo mb-3">
                            <?php endif; ?>
                        </div>

                        <!-- Profile Picture Upload -->
                        <div class="form-group">
                            <label for="profile_picture">Update Profile Picture</label>
                            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                            <small class="form-text text-muted">Choose JPG, JPEG, PNG, or GIF. Max size: 5MB</small>
                        </div>

                        <!-- Full Name -->
                        <div class="form-group">
                            <label for="fullname">Full Name *</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" 
                                   value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                                   placeholder="e.g., +1234567890">
                        </div>

                        <!-- Course -->
                        <div class="form-group">
                            <label for="course">Course/Program</label>
                            <select class="form-control" id="course" name="course">
                                <option value="">Select your course</option>
                                <option value="Computer Science" <?php echo ($user['course'] == 'Computer Science') ? 'selected' : ''; ?>>Computer Science</option>
                                <option value="Information Technology" <?php echo ($user['course'] == 'Information Technology') ? 'selected' : ''; ?>>Information Technology</option>
                                <option value="Software Engineering" <?php echo ($user['course'] == 'Software Engineering') ? 'selected' : ''; ?>>Software Engineering</option>
                                <option value="Data Science" <?php echo ($user['course'] == 'Data Science') ? 'selected' : ''; ?>>Data Science</option>
                                <option value="Cybersecurity" <?php echo ($user['course'] == 'Cybersecurity') ? 'selected' : ''; ?>>Cybersecurity</option>
                                <option value="Web Development" <?php echo ($user['course'] == 'Web Development') ? 'selected' : ''; ?>>Web Development</option>
                                <option value="Mobile Development" <?php echo ($user['course'] == 'Mobile Development') ? 'selected' : ''; ?>>Mobile Development</option>
                                <option value="Other" <?php echo ($user['course'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-block">
                            💾 Update Profile
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="dashboard.php" class="btn btn-link">← Back to Dashboard</a>
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
