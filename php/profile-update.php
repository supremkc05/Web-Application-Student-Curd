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
    $phone_secondary = trim($_POST['phone_secondary']);
    $course = trim($_POST['course']);
    $course_secondary = trim($_POST['course_secondary']);
    $course_tertiary = trim($_POST['course_tertiary']);
    $address = trim($_POST['address']);
    $date_of_birth = trim($_POST['date_of_birth']);
    $gender = trim($_POST['gender']);
    
    // Validation
    $errors = array();
    
    // Check if all required fields are not empty
    if (empty($fullname)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Validate phone numbers if provided
    if (!empty($phone) && !preg_match("/^[0-9+\-\s\(\)]+$/", $phone)) {
        $errors[] = "Invalid phone number format";
    }
    
    if (!empty($phone_secondary) && !preg_match("/^[0-9+\-\s\(\)]+$/", $phone_secondary)) {
        $errors[] = "Invalid secondary phone number format";
    }
    
    // Validate date of birth
    if (!empty($date_of_birth) && !preg_match("/^\d{4}-\d{2}-\d{2}$/", $date_of_birth)) {
        $errors[] = "Invalid date of birth format";
    }
    
    // Validate gender if provided
    if (!empty($gender) && !in_array($gender, ['Male', 'Female', 'Other'])) {
        $errors[] = "Invalid gender selection";
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
    $profile_picture = isset($user['profile_picture']) ? $user['profile_picture'] : null;
    $profile_picture_path = isset($user['profile_picture_path']) ? $user['profile_picture_path'] : null;
    
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
                if (isset($user['profile_picture']) && $user['profile_picture'] && file_exists($upload_dir . $user['profile_picture'])) {
                    unlink($upload_dir . $user['profile_picture']);
                }
                $profile_picture = $new_filename;
                $profile_picture_path = $upload_path;
            } else {
                $errors[] = "Failed to upload profile picture";
            }
        }
    }
    
    // Update user information if no errors
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET fullname = ?, email = ?, phone = ?, phone_secondary = ?, course = ?, course_secondary = ?, course_tertiary = ?, address = ?, date_of_birth = ?, gender = ?, profile_picture = ?, profile_picture_path = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $result = $stmt->execute([$fullname, $email, $phone, $phone_secondary, $course, $course_secondary, $course_tertiary, $address, $date_of_birth, $gender, $profile_picture, $profile_picture_path, $_SESSION['user_id']]);
            
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
    <link href="../assets/css/profile-update.css" rel="stylesheet">
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
    <div class="profile-update-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="profile-form-card">
                        <h2>Profile Information</h2>
                        
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
                            <div class="current-photo-section" id="photo">
                                <label>Current Profile Picture</label>
                                <?php if (isset($user['profile_picture']) && $user['profile_picture'] && file_exists("../uploads/" . $user['profile_picture'])): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" 
                                         alt="Current Profile" class="current-photo">
                                <?php else: ?>
                                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgdmlld0JveD0iMCAwIDEyMCAxMjAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMjAiIGhlaWdodD0iMTIwIiBmaWxsPSIjMDA3YmZmIiBmaWxsLW9wYWNpdHk9IjAuMSIgcng9IjYwIi8+CjxwYXRoIGQ9Ik02MCAzMEM2OS45IDMwIDc4IDM4LjEgNzggNDhDNzggNTcuOSA2OS45IDY2IDYwIDY2QzUwLjEgNjYgNDIgNTcuOSA0MiA0OEM0MiAzOC4xIDUwLjEgMzAgNjAgMzBaTTYwIDEwMkM3NyAxMDIgOTEgODguOSA5MSA3Mkg5MUMyOSA4OC45IDQzIDEwMiA2MCAxMDJaIiBmaWxsPSIjMDA3YmZmIiBmaWxsLW9wYWNpdHk9IjAuNyIvPgo8L3N2Zz4=" 
                                         alt="No Profile Picture" class="current-photo">
                                <?php endif; ?>
                            </div>

                            <!-- Profile Picture Upload -->
                            <div class="form-group">
                                <label for="profile_picture"> Update Profile Picture</label>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                                <small class="form-text text-muted">Choose JPG, JPEG, PNG, or GIF. Max size: 5MB</small>
                            </div>

                            <!-- Full Name -->
                            <div class="form-group">
                                <label for="fullname"> Full Name *</label>
                                <input type="text" class="form-control" id="fullname" name="fullname" 
                                       value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : htmlspecialchars($user['fullname']); ?>" required
                                       placeholder="Enter your full name">
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <label for="email"> Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($user['email']); ?>" required
                                       placeholder="Enter your email address">
                            </div>

                            <!-- Phone -->
                            <div class="form-group">
                                <label for="phone"> Primary Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : htmlspecialchars($user['phone'] ?? ''); ?>" 
                                       placeholder="e.g., +1234567890">
                            </div>

                            <!-- Secondary Phone -->
                            <div class="form-group">
                                <label for="phone_secondary"> Secondary Phone Number</label>
                                <input type="tel" class="form-control" id="phone_secondary" name="phone_secondary" 
                                       value="<?php echo isset($_POST['phone_secondary']) ? htmlspecialchars($_POST['phone_secondary']) : htmlspecialchars($user['phone_secondary'] ?? ''); ?>" 
                                       placeholder="e.g., +1234567890 (optional)">
                            </div>

                            <!-- Address -->
                            <div class="form-group">
                                <label for="address"> Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" 
                                          placeholder="Enter your full address"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : htmlspecialchars($user['address'] ?? ''); ?></textarea>
                            </div>

                            <!-- Date of Birth -->
                            <div class="form-group">
                                <label for="date_of_birth"> Date of Birth</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                       value="<?php echo isset($_POST['date_of_birth']) ? htmlspecialchars($_POST['date_of_birth']) : htmlspecialchars($user['date_of_birth'] ?? ''); ?>">
                            </div>

                            <!-- Gender -->
                            <div class="form-group">
                                <label for="gender">👥 Gender</label>
                                <select class="form-control" id="gender" name="gender">
                                    <option value="">Select gender</option>
                                    <?php
                                    $genderValue = isset($_POST['gender']) ? $_POST['gender'] : (isset($user['gender']) ? $user['gender'] : '');
                                    $genderOptions = ['Male', 'Female', 'Other'];
                                    foreach ($genderOptions as $option) {
                                        $selected = ($genderValue == $option) ? 'selected' : '';
                                        echo "<option value='$option' $selected>$option</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Course -->
                            <div class="form-group">
                                <label for="course">🎓 Primary Course/Program</label>
                                <select class="form-control" id="course" name="course">
                                    <option value="">Select your primary course</option>
                                    <?php
                                    $courseValue = isset($_POST['course']) ? $_POST['course'] : (isset($user['course']) ? $user['course'] : '');
                                    $courseOptions = [
                                        'Computer Science',
                                        'Information Technology',
                                        'Software Engineering',
                                        'Data Science',
                                        'Cybersecurity',
                                        'Web Development',
                                        'Mobile Development',
                                        'Business Administration',
                                        'Digital Marketing',
                                        'Other'
                                    ];
                                    foreach ($courseOptions as $option) {
                                        $selected = ($courseValue == $option) ? 'selected' : '';
                                        echo "<option value='$option' $selected>$option</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Secondary Course -->
                            <div class="form-group">
                                <label for="course_secondary"> Secondary Course/Minor (Optional)</label>
                                <select class="form-control" id="course_secondary" name="course_secondary">
                                    <option value="">Select secondary course (optional)</option>
                                    <?php
                                    $courseSecondaryValue = isset($_POST['course_secondary']) ? $_POST['course_secondary'] : (isset($user['course_secondary']) ? $user['course_secondary'] : '');
                                    $courseSecondaryOptions = [
                                        'Computer Science',
                                        'Information Technology',
                                        'Software Engineering',
                                        'Data Science',
                                        'Cybersecurity',
                                        'Web Development',
                                        'Mobile Development',
                                        'Business Administration',
                                        'Digital Marketing',
                                        'Mathematics',
                                        'Physics',
                                        'Other'
                                    ];
                                    foreach ($courseSecondaryOptions as $option) {
                                        $selected = ($courseSecondaryValue == $option) ? 'selected' : '';
                                        echo "<option value='$option' $selected>$option</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            </div>

                            <!-- Tertiary Course -->
                            <div class="form-group">
                                <label for="course_tertiary"> Tertiary Course/Specialization (Optional)</label>
                                <input type="text" class="form-control" id="course_tertiary" name="course_tertiary" 
                                       value="<?php echo isset($_POST['course_tertiary']) ? htmlspecialchars($_POST['course_tertiary']) : htmlspecialchars($user['course_tertiary'] ?? ''); ?>" 
                                       placeholder="e.g., Machine Learning, Cloud Computing, etc.">
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary">
                                Update Profile
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <a href="dashboard.php" class="btn btn-link">← Back to Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        // Image preview functionality
        document.getElementById('profile_picture').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const currentPhoto = document.querySelector('.current-photo');
                    currentPhoto.src = e.target.result;
                    currentPhoto.style.border = '3px solid #28a745';
                };
                reader.readAsDataURL(file);
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const fullname = document.getElementById('fullname').value.trim();
            const email = document.getElementById('email').value.trim();
            
            if (!fullname || !email) {
                e.preventDefault();
                alert('Please fill in all required fields (Full Name and Email)');
                return false;
            }
            
            // Email format validation
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return false;
            }
            
            // Phone number validation
            const phone = document.getElementById('phone').value.trim();
            const phoneSecondary = document.getElementById('phone_secondary').value.trim();
            const phonePattern = /^[0-9+\-\s\(\)]+$/;
            
            if (phone && !phonePattern.test(phone)) {
                e.preventDefault();
                alert('Please enter a valid phone number');
                return false;
            }
            
            if (phoneSecondary && !phonePattern.test(phoneSecondary)) {
                e.preventDefault();
                alert('Please enter a valid secondary phone number');
                return false;
            }
            
            return true;
        });

        // Debug function to check dropdown values on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Gender value:', document.getElementById('gender').value);
            console.log('Course value:', document.getElementById('course').value);
            console.log('Secondary course value:', document.getElementById('course_secondary').value);
        });
    </script>
</body>
</html>
