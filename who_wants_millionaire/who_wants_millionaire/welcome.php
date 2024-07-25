<?php
// Veritabanı bağlantısı için config dosyasını dahil edin
include_once 'config.php';

// Kullanıcı bilgilerini alma ve en son eklenen kullanıcıyı seçme
try {
    $stmt = $conn->query("SELECT first_name, last_name FROM users ORDER BY id DESC LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $firstName = $user['first_name'];
    $lastName = $user['last_name'];
} catch(PDOException $e) {
    echo "Bağlantı hatası: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome to Who Wants to Be a Millionaire?</title>
<link rel="stylesheet" href="welcome.css">
</head>
<body>

<?php if (isset($_GET['intro'])): ?>
    <!-- Intro Video -->
    <div class="video-container">
        <video id="introVideo" class="video" autoplay>
            <source src="intro.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <button class="skip-btn" onclick="skipIntro()">Skip Intro</button>
    </div>

    <script>
        document.getElementById('introVideo').onended = function() {
            window.location.href = 'welcome.php';
        };

        function skipIntro() {
            window.location.href = 'welcome.php';
        }
    </script>
<?php else: ?>
    <!-- Modal -->
    <div id="dialog1" class="modal">
      <div class="modal-content">
        <h2>Jeremy Clarkson</h2>
        <p>Hello, I'm Jeremy Clarkson. Welcome to Who Wants to Be a Millionaire. Let's welcome our first contestant of the day, <?php echo $firstName . ' ' . $lastName; ?>.</p>
        <button class="continue-btn" onclick="nextDialog('dialog1', 'dialog2')">Continue</button>
      </div>
    </div>

    <div id="dialog2" class="modal hidden">
      <div class="modal-content">
        <h2>Jeremy Clarkson</h2>
        <p>Welcome, <?php echo $firstName; ?>. Can you tell us a bit about yourself?</p>
        <textarea id="contestantInfo" placeholder="Your information" rows="4" required></textarea>
        <button class="continue-btn" onclick="saveInfo()">Save</button>
        <div id="savedInfo" class="hidden">
          <p><?php echo $firstName . ' ' . $lastName; ?>: <span id="infoText"></span></p>
          <button class="continue-btn" onclick="nextDialog('dialog2', 'dialog3')">Continue</button>
        </div>
      </div>
    </div>

    <div id="dialog3" class="modal hidden">
      <div class="modal-content">
        <h2>Jeremy Clarkson</h2>
        <p>We have a confident contestant here. I'm looking forward to the game. Before we start, do you know the rules of the game?</p>
        <button class="continue-btn" onclick="nextDialog('dialog3', 'dialog5')">Yes</button>
        <button class="cancel-btn" onclick="nextDialog('dialog3', 'dialog4')">No</button>
      </div>
    </div>

    <div id="dialog4" class="modal hidden">
      <div class="modal-content">
        <h2>Jeremy Clarkson</h2>
        <p>Game Rules:</p>
        <p>Objective: The goal of the game is to answer a series of 15 multiple-choice questions correctly to win the grand prize of one million dollars.</p>
        <p>Question Structure: There are 15 questions in total, divided into different levels of difficulty. The questions increase in difficulty as you progress. Each question has four possible answers, but only one is correct.</p>
        <p>Lifelines: You have three lifelines to assist you during the game:</p>
        <ul>
          <li>50:50: This lifeline will remove two incorrect answers, leaving you with one correct answer and one incorrect answer.</li>
          <li>Double Dip: This lifeline allows you to select two answers for a single question. If your first answer is incorrect, you can choose another answer.</li>
          <li>Switch the Question: This lifeline allows you to change the current question to a new one.</li>
        </ul>
        <p>Game Progression: You must answer each question to move on to the next one. You have 1 minute to think and answer each question. A timer will be displayed on the screen. If you answer incorrectly, you will lose the game and walk away with the amount of money from the last "safe level" you reached. The safe levels are typically at question 5 and question 10.</p>
        <p>Winning the Game: To win the game and claim the grand prize of one million dollars, you must correctly answer all 15 questions.</p>
        <p>Decision Making: You can decide to walk away with the money you have accumulated at any point before answering a question.</p>
        <p>Additional Notes: Use your lifelines wisely; they can only be used once per game. Take your time to think through each question and answer carefully within the 1-minute limit.</p>
        <button class="continue-btn" onclick="nextDialog('dialog4', 'dialog5')">Continue</button>
      </div>
    </div>

    <div id="dialog5" class="modal hidden">
      <div class="modal-content">
        <h2>Jeremy Clarkson</h2>
        <p>Alright then, let's start our game with the first question.</p>
        <button class="continue-btn" onclick="startGame()">Start Game</button>
      </div>
    </div>

    <script>
      function nextDialog(currentId, nextId) {
        document.getElementById(currentId).classList.add('hidden');
        document.getElementById(nextId).classList.remove('hidden');
      }

      function saveInfo() {
        var info = document.getElementById('contestantInfo').value;
        if (info) {
          document.getElementById('infoText').textContent = info;
          document.getElementById('contestantInfo').classList.add('hidden');
          document.querySelector('#dialog2 .continue-btn').classList.add('hidden');
          document.getElementById('savedInfo').classList.remove('hidden');
        }
      }

      function showRules() {
        nextDialog('dialog3', 'dialog4');
      }

      function startGame() {
        window.location.href = 'game.php'; // Başlangıç oyun sayfanıza yönlendirin
      }
    </script>
<?php endif; ?>

</body>
</html>
