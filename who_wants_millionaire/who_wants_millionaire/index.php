<?php
// Include the configuration file for database connection
include_once 'config.php';

// Initialize variables to hold user input
$firstName = '';
$lastName = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and store form data
    $firstName = htmlspecialchars($_POST['firstName']);
    $lastName = htmlspecialchars($_POST['lastName']);

    try {
        // Prepare SQL statement to insert data into database
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name) VALUES (:first_name, :last_name)");
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);

        // Execute the statement
        $stmt->execute();

        // Close statement and database connection
        $stmt = null;
        $conn = null;

        // Redirect to welcome.php with intro parameter after successful submission
        header('Location: welcome.php?intro=1');
        exit;
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome to Who Wants to Be a Millionaire?</title>
<link rel="stylesheet" href="index.css">
</head>
<body>

<!-- Modal -->
<div id="myModal" class="modal">
  <div class="modal-content">
    <h2>Welcome to Who Wants to Be a Millionaire?</h2>
    <p>Please enter your name to continue:</p>
    <form id="nameForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
      <input type="text" id="firstName" name="firstName" placeholder="First Name" required><br>
      <input type="text" id="lastName" name="lastName" placeholder="Last Name" required><br><br>
      <button type="submit" class="continue-btn">Continue</button>
      <button type="button" class="cancel-btn" onclick="window.location.href='start.html'">Come Back to Website</button>
      <button type="button" class="rank-btn" onclick="window.location.href='rank_league.php'">Rank League</button>
    </form>
    <!-- Logo for mobile view -->
    <img src="neu-logo.png" alt="NEU Logo" class="logo">
  </div>
</div>

<!-- Credits Button -->
<button class="credits-btn" onclick="window.location.href='credits.php'">Credits</button>

<script>
  // Get the modal element
  var modal = document.getElementById('myModal');

  // When the page loads, show the modal
  window.onload = function() {
    modal.style.display = 'block';
  }
</script>

</body>
</html>
