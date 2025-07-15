<?php
// Load quiz data from JSON file
$quizData = json_decode(file_get_contents('data.json'), true);
$quiz = $quizData['questions'];

// Initialize variables
$totalQuestions = count($quiz);
$currentQuestion = isset($_POST['current_question']) ? (int)$_POST['current_question'] : 0;
$showSummary = false;
$showCertificate = false;
$userResponses = isset($_POST['answers']) ? $_POST['answers'] : [];
$score = 0;
$feedbackMode = isset($_POST['feedback_mode']) ? $_POST['feedback_mode'] : 'end';
$shuffleQuestions = isset($_POST['shuffle_questions']);
$quizStarted = isset($_POST['quiz_started']);

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['start_quiz'])) {
        $quizStarted = true;
        $feedbackMode = $_POST['feedback_mode'] ?? 'end';
        $shuffleQuestions = isset($_POST['shuffle_questions']);
        
        if ($shuffleQuestions) {
            shuffle($quiz);
        }
    } 
    elseif (isset($_POST['action'])) {
        $quizStarted = true;
        $currentQuestion = (int)$_POST['current_question'];
        
        if ($_POST['action'] === 'next') {
            if (isset($_POST['timeout']) && $_POST['timeout'] === 'true') {
                $userResponses[$currentQuestion] = null;
            }
            $currentQuestion = min($currentQuestion + 1, $totalQuestions - 1);
        } 
        elseif ($_POST['action'] === 'prev') {
            $currentQuestion = max($currentQuestion - 1, 0);
        } 
        elseif ($_POST['action'] === 'submit') {
            $showSummary = true;
            $score = calculateScore($quiz, $userResponses);
            $showCertificate = ($score / $totalQuestions >= $quizData['certificate']['passing_score']);
        }
    }
}

function calculateScore($quiz, $userResponses) {
    $score = 0;
    foreach ($quiz as $index => $question) {
        if (isset($userResponses[$index]) && $userResponses[$index] === $question['answer']) {
            $score++;
        }
    }
    return $score;
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $quizData['meta']['title'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="quiz-container">
        <div class="quiz-header">
            <h1><?= $quizData['meta']['title'] ?></h1>
            <p class="quiz-description"><?= $quizData['meta']['description'] ?></p>
            <button id="theme-toggle" class="theme-toggle">🌓</button>
        </div>
        
        <?php if (!$quizStarted && !$showSummary): ?>
            <div class="settings-panel">
                <h3 class="settings-title">Quiz Settings</h3>
                <div class="settings-options">
                    <div class="setting-option">
                        <input type="radio" id="feedback-end" name="feedback_mode" value="end" <?= $feedbackMode === 'end' ? 'checked' : '' ?>>
                        <label for="feedback-end">Show feedback at end</label>
                    </div>
                    <div class="setting-option">
                        <input type="radio" id="feedback-immediate" name="feedback_mode" value="immediate" <?= $feedbackMode === 'immediate' ? 'checked' : '' ?>>
                        <label for="feedback-immediate">Show immediate feedback</label>
                    </div>
                    <div class="setting-option">
                        <input type="checkbox" id="shuffle-questions" name="shuffle_questions" <?= $shuffleQuestions ? 'checked' : '' ?>>
                        <label for="shuffle-questions">Shuffle questions</label>
                    </div>
                </div>
                <form method="POST">
                    <input type="hidden" name="start_quiz" value="1">
                    <input type="hidden" name="quiz_started" value="1">
                    <button type="submit" class="start-btn">Start Quiz</button>
                </form>
            </div>
        <?php elseif (!$showSummary): ?>
            <div class="quiz-controls">
                <div class="progress-container">
                    <div class="progress-bar" id="progress-bar" style="width: <?= (($currentQuestion + 1) / $totalQuestions) * 100 ?>%">
                        <span class="progress-text" id="progress-text"><?= $currentQuestion + 1 ?>/<?= $totalQuestions ?></span>
                    </div>
                </div>
                
                <div class="quiz-actions">
                    <button id="pause-btn" class="pause-btn">⏸ Pause</button>
                    <div class="timer-container">
                        <span>Time:</span>
                        <div class="timer" id="timer">1:00</div>
                    </div>
                </div>
            </div>
            
            <form method="POST" id="quiz-form">
                <input type="hidden" name="current_question" id="current-question" value="<?= $currentQuestion ?>">
                <input type="hidden" name="action" id="action" value="">
                <input type="hidden" name="feedback_mode" value="<?= $feedbackMode ?>">
                <input type="hidden" name="quiz_started" value="1">
                <input type="hidden" name="timeout" id="timeout-flag" value="false">
                
                <?php foreach ($quiz as $index => $question): ?>
                    <div class="question-container <?= $index === $currentQuestion ? 'active' : '' ?> <?= isset($userResponses[$index]) ? 'answered' : '' ?>" id="question-<?= $index ?>">
                        <div class="question">
                            <div class="question-number"><?= $index + 1 ?></div>
                            <h3><?= htmlspecialchars($question['question']) ?></h3>
                            <div class="options">
                                <?php foreach ($question['options'] as $option): ?>
                                    <div class="option">
                                        <input type="radio" 
                                            id="q<?= $index ?>-<?= md5($option) ?>" 
                                            name="answers[<?= $index ?>]" 
                                            value="<?= htmlspecialchars($option) ?>"
                                            <?= isset($userResponses[$index]) && $userResponses[$index] === $option ? 'checked' : '' ?>
                                            <?= $feedbackMode === 'immediate' ? 'onchange="checkAnswer(this)"' : '' ?>
                                            <?= isset($userResponses[$index]) ? 'disabled' : '' ?>>
                                        <label for="q<?= $index ?>-<?= md5($option) ?>">
                                            <span class="custom-radio"></span>
                                            <?= htmlspecialchars($option) ?>
                                        </label>
                                    </div>
                                <?php endforeach ?>
                            </div>
                            
                            <?php if ($feedbackMode === 'immediate' && isset($userResponses[$index])): ?>
                                <div class="immediate-feedback <?= $userResponses[$index] === $question['answer'] ? 'correct' : 'incorrect' ?>">
                                    <?php if ($userResponses[$index] === $question['answer']): ?>
                                        <p>✅ Correct! <?= $question['explanation'] ?></p>
                                    <?php else: ?>
                                        <p>❌ Incorrect. The correct answer is: <?= htmlspecialchars($question['answer']) ?></p>
                                        <p><?= $question['explanation'] ?></p>
                                    <?php endif ?>
                                </div>
                            <?php endif ?>
                        </div>
                        
                        <div class="navigation-buttons">
                            <button type="button" class="prev-btn" <?= $currentQuestion === 0 ? 'disabled' : '' ?>>
                                ← Previous
                            </button>
                            <?php if ($currentQuestion < $totalQuestions - 1): ?>
                                <button type="button" class="next-btn">
                                    Next →
                                </button>
                            <?php else: ?>
                                <button type="button" class="submit-btn">
                                    Submit Quiz
                                </button>
                            <?php endif ?>
                        </div>
                    </div>
                <?php endforeach ?>
            </form>
        <?php else: ?>
            <div class="result-container show <?= $score / $totalQuestions >= 0.8 ? 'success' : ($score / $totalQuestions >= 0.6 ? 'info' : 'warning-result') ?>">
                <h2 class="result-title">
                    <?= $score / $totalQuestions >= 0.8 ? "Excellent Work!" : ($score / $totalQuestions >= 0.6 ? "Good Job!" : "Keep Learning!") ?>
                </h2>
                <div class="score"><?= $score ?>/<?= $totalQuestions ?></div>
                <div class="feedback">
                    <?= "You scored " . number_format(($score / $totalQuestions) * 100, 1) . "%" ?>
                </div>
                
                <div class="stats-container">
                    <div class="stat-box">
                        <div class="stat-value"><?= $score ?></div>
                        <div class="stat-label">Correct</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value"><?= $totalQuestions - $score ?></div>
                        <div class="stat-label">Incorrect</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value"><?= number_format(($score / $totalQuestions) * 100, 1) ?>%</div>
                        <div class="stat-label">Score</div>
                    </div>
                </div>
                
                <div class="answer-summary">
                    <h3 class="summary-title">Answer Summary</h3>
                    <?php foreach ($quiz as $index => $question): ?>
                        <div class="answer-item <?= isset($userResponses[$index]) && $userResponses[$index] === $question['answer'] ? 'correct' : 'incorrect' ?>">
                            <p><strong>Question <?= $index + 1 ?>:</strong> <?= $question['question'] ?></p>
                            <p><strong>Your answer:</strong> 
                                <?= isset($userResponses[$index]) ? htmlspecialchars($userResponses[$index] ?? 'Not answered') : 'Not answered' ?>
                            </p>
                            <p><strong>Correct answer:</strong> <?= htmlspecialchars($question['answer']) ?></p>
                            <p><strong>Explanation:</strong> <?= $question['explanation'] ?></p>
                        </div>
                    <?php endforeach ?>
                </div>
                
                <?php if ($showCertificate): ?>
                    <button type="button" class="certificate-btn show" id="show-certificate">
                        🏆 Get Your Certificate
                    </button>
                <?php endif ?>
                
                <div class="result-actions">
                    <button type="button" id="restart-quiz" class="restart-btn">
                        ↻ Take Quiz Again
                    </button>
                    <button type="button" id="export-results" class="export-btn">
                        💾 Save Results
                    </button>
                </div>
            </div>
            
            <?php if ($showCertificate): ?>
                <div class="certificate-container" id="certificate-container" style="display: none;">
                    <div class="certificate">
                        <div class="certificate-content">
                            <div class="certificate-title">Certificate of Achievement</div>
                            <div class="certificate-subtitle">PHP Knowledge Mastery</div>
                            <div class="certificate-text">
                                This is to certify that
                            </div>
                            <div class="certificate-name" id="certificate-name">Quiz Participant</div>
                            <div class="certificate-text">
                                has successfully completed the PHP Mastery Quiz with a score of
                            </div>
                            <div class="certificate-score">
                                <?= $score ?> out of <?= $totalQuestions ?> 
                                (<?= number_format(($score / $totalQuestions) * 100, 1) ?>%)
                            </div>
                            <div class="certificate-date">
                                <?= date('F j, Y') ?>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="download-btn" id="download-certificate">
                        ⬇️ Download Certificate
                    </button>
                </div>
            <?php endif ?>
        <?php endif ?>
    </div>

    <script src="assets/js/script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</body>
</html>