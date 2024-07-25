<?php
// Veritabanı bağlantısı
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "who_wants_to_be_a_millionaire_db";
$port = 3307;

try {
    // Veritabanı bağlantısını oluşturun
    $dsn = "mysql:host=$servername;dbname=$dbname;port=$port;charset=utf8mb4";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // En son eklenen kullanıcıyı seçin
    $stmt = $conn->query("SELECT first_name, last_name FROM users ORDER BY id DESC LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $firstName = $user['first_name'];
    $lastName = $user['last_name'];
} catch(PDOException $e) {
    echo "Bağlantı hatası: " . $e->getMessage();
}

include 'questions.php';

session_start();

// Oturum değişkenlerini başlat
if (!isset($_SESSION['game_started'])) {
    $_SESSION['level'] = 1;
    $_SESSION['safe_level'] = 0; // Güvenli liman seviyesi
    $_SESSION['total_winnings'] = 0;
    $_SESSION['last_correct_winnings'] = 0; // Son doğru cevaptan kazanılan ödül
    $_SESSION['lifelines'] = [
        'fifty_fifty' => true,
        'double_dip' => true,
        'switch_question' => true
    ];
    $_SESSION['double_dip_used'] = false;
    $_SESSION['double_dip_first_try'] = false;
    $_SESSION['switched_question'] = false;
    $_SESSION['current_question'] = get_random_question($questions, $_SESSION['level']);
    $_SESSION['game_started'] = true;
}

$currentLevel = $_SESSION['level'];

// Rastgele bir soru seçme
function get_random_question($questions, $level) {
    $levelQuestions = array_filter($questions, function($question) use ($level) {
        return $question['level'] == $level;
    });
    return $levelQuestions ? $levelQuestions[array_rand($levelQuestions)] : null;
}

// Jokerler için işlevler
if (isset($_POST['lifeline'])) {
    $lifeline = $_POST['lifeline'];

    if ($lifeline == 'fifty_fifty' && $_SESSION['lifelines']['fifty_fifty']) {
        $_SESSION['lifelines']['fifty_fifty'] = false;
        $incorrectOptions = array_filter($_SESSION['current_question']['options'], function($key) {
            return $key != $_SESSION['current_question']['correct'];
        }, ARRAY_FILTER_USE_KEY);
        $keys = array_keys($incorrectOptions);
        $randomKeys = array_rand($keys, 2);
        $keysToKeep = [$_SESSION['current_question']['correct'], $keys[$randomKeys[0]]];
        $_SESSION['current_question']['options'] = array_filter($_SESSION['current_question']['options'], function($key) use ($keysToKeep) {
            return in_array($key, $keysToKeep);
        }, ARRAY_FILTER_USE_KEY);
    } elseif ($lifeline == 'double_dip' && $_SESSION['lifelines']['double_dip']) {
        $_SESSION['lifelines']['double_dip'] = false;
        $_SESSION['double_dip_used'] = true;
        $_SESSION['double_dip_first_try'] = true;
    } elseif ($lifeline == 'switch_question' && $_SESSION['lifelines']['switch_question']) {
        $_SESSION['lifelines']['switch_question'] = false;
        $_SESSION['switched_question'] = true;
        $newQuestion = null;
        do {
            $newQuestion = get_random_question($questions, $currentLevel);
        } while ($newQuestion['question'] === $_SESSION['current_question']['question']);
        $_SESSION['current_question'] = $newQuestion;
        $_SESSION['double_dip_used'] = false; // Yeni soruda Double Dip sıfırlanır
        $_SESSION['double_dip_first_try'] = false;
    }
}

$showCongratsPopup = false;

// Oyundan çekilme işlemi
if (isset($_POST['quit_game'])) {
    $_SESSION['total_winnings'] = $_SESSION['last_correct_winnings'];
    header('Location: end_game.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['option'])) {
    $selectedOption = $_POST['option'];

    // İkinci Double Dip denemesi için doğru cevabı tekrar kontrol et
    if ($selectedOption == $_SESSION['current_question']['correct']) {
        // Ödül basamaklarına göre güvenli liman hesaplaması
        if ($currentLevel == 5 || $currentLevel == 10 || $currentLevel == 15) {
            $_SESSION['safe_level'] = $_SESSION['current_question']['prize'];
        }

        $_SESSION['total_winnings'] = $_SESSION['current_question']['prize'];
        $_SESSION['last_correct_winnings'] = $_SESSION['current_question']['prize']; // Son doğru cevaptan kazanılan ödül
        $showCongratsPopup = true;

        if ($currentLevel == 15) {
            header('Location: end_game.php');
            exit;
        } else {
            $_SESSION['level']++;
            $_SESSION['double_dip_used'] = false;
            $_SESSION['double_dip_first_try'] = false;
            $_SESSION['switched_question'] = false;
            $_SESSION['current_question'] = get_random_question($questions, $_SESSION['level']);
            header('Location: game.php');
            exit;
        }
    } else {
        // Double Dip jokeri için ilk deneme
        if ($_SESSION['double_dip_used'] && $_SESSION['double_dip_first_try']) {
            $_SESSION['double_dip_first_try'] = false;
            $isDoubleDip = true;
        } else {
            // Soru yanlışsa oyun biter
            $_SESSION['total_winnings'] = $_SESSION['safe_level'];
            header('Location: end_game.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Who Wants to Be a Millionaire</title>
    <link rel="stylesheet" href="game.css">
    <style>
        .mute-button {
            position: fixed;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            cursor: pointer;
            z-index: 1000;
        }
        .mute-button img {
            width: 75px;
            height: 75px;
        }
    </style>
    <script>
        let timer, questionAudio, timeAudio, winAudio, loseAudio, isMuted = false;

        function startTimer() {
            let timeLeft = 60;
            const timerElement = document.getElementById('timer');
            const timerCircles = document.querySelectorAll('.timer div');

            timer = setInterval(() => {
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    loseAudio.play();
                    showTimesUpModal();
                    setTimeout(() => {
                        window.location.href = 'end_game.php';
                    }, 3000);
                } else {
                    timerCircles[60 - timeLeft].classList.add('empty');
                    timeLeft--;
                }
            }, 1000);

            // Play time audio in loop for 60 seconds
            timeAudio.play();
        }

        function playAudio(file) {
            const audio = new Audio(file);
            audio.play();
        }

        function stopAudios() {
            questionAudio.pause();
            timeAudio.pause();
        }

        function checkAnswer(selectedOption) {
            clearInterval(timer);
            stopAudios();
            const options = document.querySelectorAll('.options-container div');

            if (selectedOption.dataset.answer == '<?= $_SESSION['current_question']['correct'] ?>') {
                options.forEach(option => {
                    option.classList.add(option.dataset.answer == '<?= $_SESSION['current_question']['correct'] ?>' ? 'correct' : 'incorrect');
                    option.onclick = null;
                });
                document.getElementById('congratsPopup').style.display = 'block';
                winAudio.play();
            } else {
                loseAudio.play();
                if (<?= $_SESSION['double_dip_used'] && $_SESSION['double_dip_first_try'] ? 'true' : 'false' ?>) {
                    selectedOption.classList.add('incorrect');
                    selectedOption.onclick = null;
                } else {
                    options.forEach(option => {
                        option.classList.add(option.dataset.answer == '<?= $_SESSION['current_question']['correct'] ?>' ? 'correct' : 'incorrect');
                        option.onclick = null;
                    });
                    setTimeout(() => window.location.href = 'end_game.php', 3000);
                }
            }

            const form = document.createElement('form');
            form.method = 'post';
            form.action = '';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'option';
            input.value = selectedOption.dataset.answer;
            form.appendChild(input);
            document.body.appendChild(form);
            setTimeout(() => form.submit(), 3000);
        }

        function continueGame() {
            document.getElementById('congratsPopup').style.display = 'none';
            setTimeout(() => window.location.href = 'game.php', 3000);
        }

        function showTimesUpModal() {
            document.getElementById('timesUpModal').style.display = 'block';
        }

        function toggleMute() {
            isMuted = !isMuted;
            questionAudio.muted = isMuted;
            timeAudio.muted = isMuted;
            winAudio.muted = isMuted;
            loseAudio.muted = isMuted;
            document.getElementById('muteButton').src = isMuted ? 'muted.png' : 'unmuted.png';
            localStorage.setItem('isMuted', isMuted);
        }

        window.onload = function() {
            questionAudio = new Audio('question.m4a');
            timeAudio = new Audio('time.mp3');
            winAudio = new Audio('win.mp3');
            loseAudio = new Audio('lose.mp3');
            timeAudio.loop = true;

            // Mute status from localStorage
            if (localStorage.getItem('isMuted') === 'true') {
                isMuted = true;
                document.getElementById('muteButton').src = 'muted.png';
            } else {
                document.getElementById('muteButton').src = 'unmuted.png';
            }

            questionAudio.muted = isMuted;
            timeAudio.muted = isMuted;
            winAudio.muted = isMuted;
            loseAudio.muted = isMuted;

            // Play question and time audio
            questionAudio.play();
            timeAudio.play();

            startTimer();
        };
    </script>
</head>
<body>
    <div class="header">
        <h1>Who Wants to Be a Millionaire</h1>
    </div>
    <button class="mute-button" onclick="toggleMute()">
        <img id="muteButton" src="unmuted.png" alt="Mute/Unmute">
    </button>
    <ul class="money-list">
        <?php
        $prizes = [
            1 => '$100', 2 => '$200', 3 => '$300', 4 => '$500', 5 => '$1,000',
            6 => '$2,000', 7 => '$4,000', 8 => '$8,000', 9 => '$16,000', 10 => '$32,000',
            11 => '$64,000', 12 => '$125,000', 13 => '$250,000', 14 => '$500,000', 15 => '$1,000,000'
        ];
        $safeLevels = [5, 10, 15]; // Güvenli liman seviyeleri
        foreach ($prizes as $level => $prize) {
            echo '<li class="'. ($level == $currentLevel ? 'active' : '') .'">';
            if (in_array($level, $safeLevels)) {
                echo '<img src="https://img.icons8.com/emoji/48/000000/star-emoji.png" alt="Safe Level" class="star">';
            }
            echo 'Question ' . $level . ': ' . $prize . '</li>';
        }
        ?>
    </ul>
    <div class="question-container">
        <h2>Question <?= $currentLevel ?>: <?= $_SESSION['current_question']['question'] ?></h2>
        <div id="timer" class="timer">
            <?php for ($i = 0; $i < 60; $i++): ?>
                <div></div>
            <?php endfor; ?>
        </div>
        <div class="lifelines">
            <form method="post">
                <button type="submit" name="lifeline" value="fifty_fifty" <?= $_SESSION['lifelines']['fifty_fifty'] ? '' : 'disabled' ?>>50:50</button>
                <button type="submit" name="lifeline" value="double_dip" <?= $_SESSION['lifelines']['double_dip'] ? '' : 'disabled' ?>>Double Dip</button>
                <button type="submit" name="lifeline" value="switch_question" <?= $_SESSION['lifelines']['switch_question'] ? '' : 'disabled' ?>>Switch the Question</button>
            </form>
        </div>
    </div>
    <div class="options-container">
        <?php foreach ($_SESSION['current_question']['options'] as $key => $option): ?>
            <div data-answer="<?= $key ?>" onclick="checkAnswer(this)"><?= $option ?></div>
        <?php endforeach; ?>
    </div>
    <form method="post" style="text-align: center;">
        <button type="submit" name="quit_game" class="quit-button">Quit Game</button>
    </form>

    <div id="congratsPopup" class="popup" style="display: <?= $showCongratsPopup ? 'block' : 'none' ?>;">
        <p>Congratulations! You won $<?= $_SESSION['current_question']['prize'] ?>.</p>
        <button onclick="continueGame()">Continue</button>
    </div>

    <div id="timesUpModal" class="popup" style="display: none;">
        <p>Time's Up!</p>
    </div>
</body>
</html>
