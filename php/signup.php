<?php
session_start();
if (isset($_SESSION["user"])) {
   header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>signup</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container register-container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="register-form">
                    <h2 class="text-center">Register</h2>
                    
                    <?php
                    require "../config/database.php";
                    if (isset($_POST["register"])) {
                        $fullName = $_POST["fullname"];
                        $email = $_POST["email"];
                        $password = $_POST["password"];
                        $confirm_password = $_POST["confirm_password"];

                        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                        
                        $errors = array();
                        
                        if (empty($fullName) OR empty($email) OR empty($password) OR empty($confirm_password)) {
                            array_push($errors,"All fields are required");
                        }
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            array_push($errors, "Email is not valid");
                        }
                        if (strlen($password)<8) {
                            array_push($errors,"Password must be at least 8 characters long");
                        }
                        if ($password!==$confirm_password) {
                            array_push($errors,"Password does not match");
                        }

                        // Check if email already exists using PDO
                        try {
                            $database = new Database();
                            $pdo = $database->getConnection();
                            
                            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                            $stmt->execute([$email]);
                            $existingUser = $stmt->fetch();
                            
                            if ($existingUser) {
                                array_push($errors,"Email already exists");
                            }

                            if (count($errors)>0) {
                                foreach ($errors as  $error) {
                                    echo "<div class='alert alert-danger'>$error</div>";
                                }
                            }else{
                                // Insert new user using PDO
                                $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
                                $result = $stmt->execute([$fullName, $email, $passwordHash]);
                                
                                if ($result) {
                                    echo "<div class='alert alert-success'>You are registered successfully.</div>";
                                } else {
                                    echo "<div class='alert alert-danger'>Registration failed. Please try again.</div>";
                                }
                            }
                        } catch (Exception $e) {
                            echo "<div class='alert alert-danger'>Database error: " . $e->getMessage() . "</div>";
                        }
                    }
                    ?>
                    
                    <form action="signup.php" method="post">
                        <div class="form-group">
                            <label for="fullname">Full Name</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block" name="register">Register</button>
                    </form>
                    <p class="text-center mt-3">
                        Already have an account? <a href="../php/login.php">Login here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>