# Who Wants to Be a Millionaire?

This project is a web-based version of the "Who Wants to Be a Millionaire?" game. Players attempt to answer various questions correctly to win the grand prize. This README file provides detailed steps to set up and run the project and explains its features.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [File Structure](#file-structure)
- [Features](#features)
- [Technical Details](#technical-details)
- [Troubleshooting](#troubleshooting)
- [Developed By](#developed-by)

## Installation

### Requirements

- PHP 7.4 or higher
- MySQL
- Web server (Apache or Nginx recommended)
- Browser (Google Chrome, Firefox, Safari, etc.)

### Steps

1. **Clone the Repository:**

   ```sh
   git clone https://github.com/username/millionaire.git
   cd millionaire
   ```

2. **Set Up the Database:**

   - Create your MySQL database and name it `who_wants_to_be_a_millionaire_db`.
   - Populate the database using the `database.sql` file.

   ```sh
   mysql -u root -p who_wants_to_be_a_millionaire_db < database.sql
   ```

3. **Configure Database Connection:**

   Open the `config.php` file and set your database connection parameters.

   ```php
   <?php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "who_wants_to_be_a_millionaire_db";
   $port = 3307;

   try {
       $conn = new PDO("mysql:host=$servername;dbname=$dbname;port=$port;charset=utf8mb4", $username, $password);
       $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   } catch(PDOException $e) {
       echo "Connection error: " . $e->getMessage();
   }
   ?>
   ```

4. **Start the Web Server:**

   Start a PHP built-in web server in the project directory.

   ```sh
   php -S localhost:8000
   ```

5. **Open in Browser:**

   Open your browser and navigate to `http://localhost:8000`.

## Usage

1. **Home Page:**

   - Players enter their names and click the `Continue` button to start the game.
   - Click the `Credits` button to view the developer's information.

2. **Game Screen:**

   - Questions and options will be displayed.
   - Players try to choose the correct answer.
   - Lifelines (50:50, Double Dip, Switch the Question) can be used.

3. **Game Over:**

   - Players will see the amount of money they won and can restart the game.

## File Structure

- `index.php`: Home page of the game.
- `welcome.php`: Player information and initial dialogues.
- `game.php`: Game screen and question-answer mechanism.
- `end_game.php`: Game over screen.
- `config.php`: Database connection settings.
- `questions.php`: List of questions.
- `welcome.css`: Styles for the welcome page.
- `game.css`: Styles for the game page.
- `credits.php`: Developer information page.
- `credits.css`: Styles for the credits page.
- `intro.mp4`: Intro video played at the start of the game.
- `question.m4a`: Audio played during questions.
- `time.mp3`: Audio played for the timer.
- `win.mp3`: Audio played for correct answers.
- `lose.mp3`: Audio played for incorrect answers.
- `farewell.mp3`: Audio played at the end of the game.
- `unmuted.png`: Icon for unmuted sound.
- `muted.png`: Icon for muted sound.

## Features

- Automatic video playback and user entry dialogues.
- Answering questions and tracking winnings.
- Using lifelines (50:50, Double Dip, Switch the Question).
- Displaying winnings at the end of the game.
- Mute/Unmute button for controlling audio.

## Technical Details

- **Backend:** PHP, MySQL
- **Frontend:** HTML, CSS, JavaScript
- **Database:** Questions and user information are stored in a MySQL database.

## Troubleshooting

- **Database Connection Error:**

  Check your database connection settings in the `config.php` file.

- **Audio Playback Issue:**

  Check your browser's autoplay permissions. User interaction may be required.

- **Styling Issues:**

  Clear your browser cache and reload the page.

- **Video Playback Issue:**

  Check your browser's autoplay permissions for videos. User interaction may be required.

## Developed By

This project was developed by:

**Muhammed Murat Yanaşoğlu**

- Final year(4th Grade Student) student of Software Engineering (English) at Near East University.
- Specializes in web development.
- [LinkedIn](https://www.linkedin.com/in/muratyanasoglu/)
- [GitHub](https://github.com/muratyanasoglu)
