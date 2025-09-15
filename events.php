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

// Get all events
$stmt = $pdo->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'includes/header.php'; ?>
<div class="container">
  <h2>Available Events</h2>
  
  <?php if (count($events) > 0): ?>
    <div class="events-list">
      <?php foreach($events as $e): ?>
        <div class="event-card">
          <h3><?= e($e['title']); ?></h3>
          <p class="event-date"><strong>Date:</strong> <?= date('F j, Y', strtotime($e['event_date'])); ?></p>
          <p class="event-description"><?= e($e['description']); ?></p>
          
          <?php if(isset($_SESSION['user']) && $_SESSION['user']['role']=='student'): ?>
            <form method="post" action="registration.php">
              <input type="hidden" name="csrf_token" value="<?= generateCsrfToken(); ?>">
              <input type="hidden" name="event_id" value="<?= $e['id']; ?>">
              <button type="submit" class="btn btn-primary">Register for this Event</button>
            </form>
          <?php elseif(!isset($_SESSION['user'])): ?>
            <p><a href="login.php">Login</a> to register for this event</p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p>No upcoming events available.</p>
  <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>