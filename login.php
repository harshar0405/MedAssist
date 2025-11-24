<?php
include 'db_connect.php';
include 'header.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['patient_id'])) {
    header('Location: dashboard.php');
    exit;
}

$login_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $login_error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT patient_id, name, password FROM patients WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['patient_id'] = $user['patient_id'];
                $_SESSION['patient_name'] = $user['name'];

                echo '<script>
                    Swal.fire({
                        title: "Welcome Back!",
                        text: "Logging you in...",
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "dashboard.php";
                    });
                </script>';
                exit; 
            } else {
                $login_error = "Invalid email or password.";
            }
        } else {
            $login_error = "No account found with that email.";
        }
        $stmt->close();
    }
}
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card p-4">
            
            <div class="text-center mb-3">
                <img src="logo.jpeg" alt="Portal Logo" class="img-fluid" style="max-height: 80px;">
            </div>
            
            <h2 class="card-title text-center text-primary">Patient Login</h2>
            <hr>
            
            <?php if ($login_error): ?>
                <script>Swal.fire('Login Failed', '<?php echo $login_error; ?>', 'error');</script>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success w-100 mt-2">Log In</button>
            </form>
            
            <p class="mt-3 text-center">
                Don't have an account? <a href="register.php">Sign up here</a>.
            </p>
        </div>
    </div>
</div>

<?php 
mysqli_close($conn);
include 'footer.php'; 
?>


