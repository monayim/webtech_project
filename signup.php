<?php
require 'config/db.php';
$message = '';

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $department = $_POST['department'];
    $pass  = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role  = $_POST['role'];

    if ($role == 'organizer') {
        $approved = 0;
    } else {
        $approved = 1;
    }

    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, department, password, role, approved) VALUES (?,?,?,?,?,?,?)");
    if ($stmt->execute([$name, $email, $phone, $department, $pass, $role, $approved])) {
        $message = "User registered successfully!";
        if($role == 'organizer') {
            $message .= " Your account is pending approval.";
        }
    } else {
        $message = "Error occurred! (Maybe email already exists?)";
    }
}
?>
<?php include 'includes/header.php'; ?>
<div class="container">
  <h2>Sign Up</h2>
  <form method="post">
    <input type="text" name="name" placeholder="Full Name" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="text" name="phone" placeholder="Phone Number"><br>
    <input type="text" name="department" placeholder="Department"><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <select name="role" required>
      <option value="student">Student</option>
      <option value="organizer">Organizer</option>
      <option value="admin">Admin</option>
    </select><br>
    <button type="submit">Register</button>
  </form>
  <p><?= $message; ?></p>
</div>
<?php include 'includes/footer.php'; ?>