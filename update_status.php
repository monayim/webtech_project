<?php
// 1. Functions first
require_once 'includes/functions.php';

// 2. Start session safely
safe_session_start();

// 3. Database connection
require_once 'config/db.php';

// 4. CSRF functions
require_once 'includes/csrf.php';

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','organizer'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        $_SESSION['error'] = "Invalid request";
    } else {
        $id = (int)$_POST['id'];
        $status = $_POST['status'];
        
        // Verify the user has permission to update this registration
        if ($_SESSION['user']['role'] == 'organizer') {
            $check_stmt = $pdo->prepare("
                SELECT r.id FROM registrations r
                JOIN events e ON r.event_id = e.id
                WHERE r.id = ? AND e.created_by = ?
            ");
            $check_stmt->execute([$id, $_SESSION['user']['id']]);
            
            if ($check_stmt->rowCount() == 0) {
                $_SESSION['error'] = "Access denied";
                header("Location: registrations.php");
                exit;
            }
        }
        
        $stmt = $pdo->prepare("UPDATE registrations SET status=? WHERE id=?");
        $stmt->execute([$status, $id]);
        $_SESSION['message'] = "Status updated successfully";
    }
}

header("Location: registrations.php");
exit;
?>
