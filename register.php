<?php
include 'db_connect.php';
include 'header.php'; // Includes session_start(), Bootstrap, and SweetAlert

// Check if user is already logged in, redirect to dashboard
if (isset($_SESSION['patient_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    
    // Fixed sign-up method for standard registration
    $signup_method = 'Registered'; 

    // --- Basic Validation ---
    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $stmt = $conn->prepare("SELECT patient_id FROM patients WHERE email = ? OR phone = ?");
        $stmt->bind_param("ss", $email, $phone);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email or Phone number is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO patients (name, email, phone, password, signup_method) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $phone, $hashed_password, $signup_method);

            if ($stmt->execute()) {
                $success = "Registration successful! You can now log in.";
                $_POST = array();
            } else {
                $error = "Registration failed: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card p-4">
            <h2 class="card-title text-center text-primary">Patient Sign Up</h2>
            <hr>

            <?php if ($error): ?>
                <script>Swal.fire('Error!', '<?php echo $error; ?>', 'error');</script>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <script>
                Swal.fire({
                    title: 'Success!',
                    text: '<?php echo $success; ?>',
                    icon: 'success',
                    confirmButtonText: 'Go to Login'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'login.php'; 
                    }
                });
                </script>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" required value="<?php echo $_POST['name'] ?? ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required value="<?php echo $_POST['email'] ?? ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="phone" class="form-control" required value="<?php echo $_POST['phone'] ?? ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <input type="hidden" name="signup_method" value="Registered">
                <div class="mb-3">
                    <label class="form-label">Sign Up Method</label>
                    <input type="text" class="form-control" value="Registered (Standard)" disabled>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 mt-2">Sign Up</button>
            </form>
            <p class="mt-3 text-center">
                Already have an account? <a href="login.php">Log in here</a>.
            </p>
            <div class="text-center mt-3">
                <button class="btn btn-danger btn-sm mx-1" disabled>
                    <i class="bi bi-google"></i> Sign in with Google (Disabled)
                </button>
                <button class="btn btn-primary btn-sm mx-1" disabled>
                    <i class="bi bi-facebook"></i> Sign in with Facebook (Disabled)
                </button>
            </div>
        </div>
    </div>
</div>

<?php 
mysqli_close($conn); 
include 'footer.php'; 
?>