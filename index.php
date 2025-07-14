<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Mastery Quiz</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --accent: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #2ecc71;
            --warning: #f39c12;
            --danger: #e74c3c;
            --info: #3498db;
            --gray: #95a5a6;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .quiz-container {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .quiz-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        
        h1 {
            color: var(--primary);
            margin-bottom: 10px;
            font-weight: 700;
            font-size: 2.2rem;
        }
        
        .quiz-description {
            color: var(--gray);
            margin-bottom: 20px;
        }
        
        .question-container {
            display: none;
            animation: fadeIn 0.5s ease;
        }
        
        .question-container.active {
            display: block;
        }
        
        .question {
            background: var(--light);
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            border-left: 5px solid var(--accent);
            position: relative;
        }
        
        .question-number {
            position: absolute;
            top: -15px;
            left: 20px;
            background: var(--primary);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2);
        }
        
        .question h3 {
            margin-top: 10px;
            color: var(--secondary);
            font-size: 1.2rem;
        }
        
        .options {
            margin-top: 20px;
        }
        
        .option {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            padding: 12px 15px;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid #e0e0e0;
        }
        
        .option:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.05);
        }
        
        .option input {
            display: none;
        }
        
        .option label {
            cursor: pointer;
            width: 100%;
            display: flex;
            align-items: center;
        }
        
        .custom-radio {
            width: 20px;
            height: 20px;
            border: 2px solid var(--gray);
            border-radius: 50%;
            margin-right: 15px;
            position: relative;
            transition: all 0.3s;
        }
        
        .option input:checked + label .custom-radio {
            border-color: var(--primary);
            background-color: var(--primary);
        }
        
        .option input:checked + label .custom-radio::after {
            content: '';
            position: absolute;
            width: 10px;
            height: 10px;
            background: white;
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        .timer-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding: 10px 15px;
            background: var(--light);
            border-radius: 8px;
        }
        
        .timer {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        .timer.warning {
            color: var(--warning);
        }
        
        .timer.danger {
            color: var(--danger);
            animation: pulse 1s infinite;
        }
        
        .progress-container {
            width: 100%;
            height: 10px;
            background-color: #e0e0e0;
            border-radius: 5px;
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--accent), var(--primary));
            width: 0%;
            transition: width 0.4s ease;
        }
        
        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        button {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        button:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        button:disabled {
            background-color: var(--gray);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .result-container {
            text-align: center;
            padding: 30px;
            border-radius: 10px;
            margin-top: 20px;
            font-size: 1.1rem;
            display: none;
            animation: fadeIn 0.8s ease;
        }
        
        .result-container.show {
            display: block;
        }
        
        .success {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success);
            border: 2px solid var(--success);
        }
        
        .info {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--info);
            border: 2px solid var(--info);
        }
        
        .warning-result {
            background-color: rgba(243, 156, 18, 0.1);
            color: var(--warning);
            border: 2px solid var(--warning);
        }
        
        .result-title {
            font-size: 1.8rem;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .score {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 20px 0;
            color: var(--primary);
        }
        
        .feedback {
            margin-bottom: 25px;
            font-size: 1.2rem;
        }
        
        .answer-summary {
            text-align: left;
            margin-top: 30px;
        }
        
        .answer-item {
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            background: white;
            border-left: 4px solid var(--gray);
        }
        
        .answer-item.correct {
            border-left-color: var(--success);
            background: rgba(46, 204, 113, 0.05);
        }
        
        .answer-item.incorrect {
            border-left-color: var(--danger);
            background: rgba(231, 76, 60, 0.05);
        }
        
        .summary-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--secondary);
        }
        
        .certificate-btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 15px 30px;
            font-size: 1.1rem;
            margin: 20px auto;
            display: none;
        }
        
        .certificate-btn.show {
            display: inline-flex;
        }
        
        .certificate-container {
            display: none;
            padding: 30px;
            background: white;
            border-radius: 10px;
            margin-top: 30px;
            text-align: center;
            border: 2px dashed var(--primary);
        }
        
        .certificate {
            background: url('https://images.unsplash.com/photo-1579389083078-4e7018379f7e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') center/cover;
            padding: 40px;
            position: relative;
            color: #333;
        }
        
        .certificate::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .certificate-content {
            position: relative;
            z-index: 1;
        }
        
        .certificate-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .certificate-subtitle {
            font-size: 1.2rem;
            margin-bottom: 30px;
            color: var(--secondary);
        }
        
        .certificate-name {
            font-size: 1.8rem;
            font-weight: 600;
            margin: 30px 0;
            padding: 15px;
            border-top: 2px solid var(--primary);
            border-bottom: 2px solid var(--primary);
            display: inline-block;
        }
        
        .certificate-text {
            margin: 20px 0;
            font-size: 1.1rem;
        }
        
        .certificate-score {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 20px 0;
        }
        
        .certificate-date {
            margin-top: 30px;
            font-style: italic;
        }
        
        .print-btn {
            background: var(--success);
            margin-top: 20px;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .quiz-container {
                padding: 20px;
            }
            
            h1 {
                font-size: 1.8rem;
            }
            
            .question {
                padding: 20px 15px;
            }
            
            .option {
                padding: 10px;
            }
            
            .navigation-buttons {
                flex-direction: column;
                gap: 10px;
            }
            
            button {
                width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .quiz-container {
                padding: 15px;
            }
            
            .question h3 {
                font-size: 1.1rem;
            }
            
            .result-title {
                font-size: 1.5rem;
            }
            
            .score {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="quiz-container">
        <div class="quiz-header">
            <h1>PHP Mastery Quiz</h1>
            <p class="quiz-description">Test your PHP knowledge with this interactive quiz</p>
        </div>
        
        <div class="progress-container">
            <div class="progress-bar" id="progress-bar"></div>
        </div>
        
        <?php
        // Define the quiz questions and answers
        $quiz = [
            [
                'question' => 'What does PHP stand for?',
                'options' => [
                    'Personal Home Page',
                    'PHP: Hypertext Preprocessor',
                    'Private Hosting Platform',
                    'Preprocessed Hypertext Page'
                ],
                'answer' => 'PHP: Hypertext Preprocessor',
                'explanation' => 'PHP originally stood for Personal Home Page, but it now stands for PHP: Hypertext Preprocessor (a recursive acronym).'
            ],
            [
                'question' => 'Which of the following is NOT a valid PHP variable name?',
                'options' => [
                    '$myVariable',
                    '$my_variable',
                    '$my-variable',
                    '$myVariable1'
                ],
                'answer' => '$my-variable',
                'explanation' => 'Variable names in PHP can only contain alphanumeric characters and underscores. Hyphens are not allowed.'
            ],
            [
                'question' => 'What is the result of echo "1" + "2" in PHP?',
                'options' => [
                    '12',
                    '3',
                    '"1"+"2"',
                    'Error'
                ],
                'answer' => '3',
                'explanation' => 'PHP performs type juggling. When using the + operator with strings that contain numbers, PHP converts them to numbers before adding.'
            ],
            [
                'question' => 'Which function is used to check if a variable is an array?',
                'options' => [
                    'is_array()',
                    'array_check()',
                    'isarray()',
                    'check_array()'
                ],
                'answer' => 'is_array()',
                'explanation' => 'The is_array() function is the correct PHP function to check if a variable is an array.'
            ],
            [
                'question' => 'What does the === operator compare in PHP?',
                'options' => [
                    'Only values',
                    'Only types',
                    'Both values and types',
                    'Neither values nor types'
                ],
                'answer' => 'Both values and types',
                'explanation' => 'The === operator is the identical operator in PHP, which checks both value and type.'
            ],
            [
                'question' => 'Which of these is used to start a session in PHP?',
                'options' => [
                    'session_begin()',
                    'start_session()',
                    'session_start()',
                    'init_session()'
                ],
                'answer' => 'session_start()',
                'explanation' => 'session_start() is the correct function to start a new session or resume an existing one.'
            ],
            [
                'question' => 'What is the correct way to include a file in PHP?',
                'options' => [
                    '#include "file.php"',
                    'include "file.php"',
                    'include file.php',
                    'include: "file.php"'
                ],
                'answer' => 'include "file.php"',
                'explanation' => 'The include statement is used to include and evaluate the specified file.'
            ],
            [
                'question' => 'What does the NULL value represent in PHP?',
                'options' => [
                    'A variable with no value assigned',
                    'A variable set to 0',
                    'A variable set to an empty string',
                    'A variable that has not been declared'
                ],
                'answer' => 'A variable with no value assigned',
                'explanation' => 'NULL in PHP represents a variable with no value or a variable that has been unset.'
            ]
        ];
        
        // Initialize variables
        $totalQuestions = count($quiz);
        $currentQuestion = 0;
        $showSummary = false;
        $showCertificate = false;
        $userResponses = [];
        $score = 0;
        
        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle quiz submission
            if (isset($_POST['action'])) {
                if ($_POST['action'] === 'next') {
                    $currentQuestion = min((int)$_POST['current_question'] + 1, $totalQuestions - 1);
                    $userResponses = $_POST['answers'] ?? [];
                } elseif ($_POST['action'] === 'prev') {
                    $currentQuestion = max((int)$_POST['current_question'] - 1, 0);
                    $userResponses = $_POST['answers'] ?? [];
                } elseif ($_POST['action'] === 'submit') {
                    $userResponses = $_POST['answers'] ?? [];
                    $showSummary = true;
                    $score = calculateScore($quiz, $userResponses);
                    if ($score / $totalQuestions >= 0.75) {
                        $showCertificate = true;
                    }
                }
            }
        }
        
        // Function to calculate the score
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
        
        <form method="POST" id="quiz-form">
            <input type="hidden" name="current_question" id="current-question" value="<?php echo $currentQuestion; ?>">
            <input type="hidden" name="action" id="action" value="">
            
            <?php if (!$showSummary): ?>
                <div class="timer-container">
                    <div>Time remaining:</div>
                    <div class="timer" id="timer">30:00</div>
                </div>
                
                <?php foreach ($quiz as $index => $question): ?>
                    <div class="question-container <?php echo $index === $currentQuestion ? 'active' : ''; ?>" id="question-<?php echo $index; ?>">
                        <div class="question">
                            <div class="question-number"><?php echo $index + 1; ?></div>
                            <h3><?php echo $question['question']; ?></h3>
                            <div class="options">
                                <?php foreach ($question['options'] as $option): ?>
                                    <div class="option">
                                        <input type="radio" 
                                               id="q<?php echo $index; ?>-<?php echo md5($option); ?>" 
                                               name="answers[<?php echo $index; ?>]" 
                                               value="<?php echo htmlspecialchars($option); ?>"
                                               <?php echo isset($userResponses[$index]) && $userResponses[$index] === $option ? 'checked' : ''; ?>>
                                        <label for="q<?php echo $index; ?>-<?php echo md5($option); ?>">
                                            <span class="custom-radio"></span>
                                            <?php echo htmlspecialchars($option); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="navigation-buttons">
                            <button type="button" class="prev-btn" <?php echo $currentQuestion === 0 ? 'disabled' : ''; ?>>
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
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="result-container show <?php 
                    echo $score / $totalQuestions >= 0.8 ? 'success' : 
                    ($score / $totalQuestions >= 0.6 ? 'info' : 'warning-result'); ?>">
                    <h2 class="result-title">
                        <?php 
                        if ($score / $totalQuestions >= 0.8) {
                            echo "Excellent Work!";
                        } elseif ($score / $totalQuestions >= 0.6) {
                            echo "Good Job!";
                        } else {
                            echo "Keep Learning!";
                        }
                        ?>
                    </h2>
                    <div class="score"><?php echo $score; ?>/<?php echo $totalQuestions; ?></div>
                    <div class="feedback">
                        <?php 
                        $percentage = ($score / $totalQuestions) * 100;
                        echo "You scored " . number_format($percentage, 1) . "%";
                        ?>
                    </div>
                    
                    <div class="answer-summary">
                        <h3 class="summary-title">Answer Summary</h3>
                        <?php foreach ($quiz as $index => $question): ?>
                            <div class="answer-item <?php 
                                echo isset($userResponses[$index]) && $userResponses[$index] === $question['answer'] ? 'correct' : 'incorrect'; ?>">
                                <p><strong>Question <?php echo $index + 1; ?>:</strong> <?php echo $question['question']; ?></p>
                                <p><strong>Your answer:</strong> 
                                    <?php echo isset($userResponses[$index]) ? htmlspecialchars($userResponses[$index]) : 'Not answered'; ?>
                                </p>
                                <p><strong>Correct answer:</strong> <?php echo htmlspecialchars($question['answer']); ?></p>
                                <p><strong>Explanation:</strong> <?php echo $question['explanation']; ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if ($showCertificate): ?>
                        <button type="button" class="certificate-btn show" id="show-certificate">
                            🏆 Get Your Certificate
                        </button>
                    <?php endif; ?>
                    
                    <button type="button" id="restart-quiz" style="background: var(--gray);">
                        ↻ Take Quiz Again
                    </button>
                </div>
                
                <?php if ($showCertificate): ?>
                    <div class="certificate-container" id="certificate-container">
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
                                    <?php echo $score; ?> out of <?php echo $totalQuestions; ?> 
                                    (<?php echo number_format(($score / $totalQuestions) * 100, 1); ?>%)
                                </div>
                                <div class="certificate-date">
                                    <?php echo date('F j, Y'); ?>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="print-btn" id="print-certificate">
                            🖨️ Print Certificate
                        </button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </form>
    </div>

    <script>
        // Quiz functionality
        document.addEventListener('DOMContentLoaded', function() {
            const quizForm = document.getElementById('quiz-form');
            const currentQuestionInput = document.getElementById('current-question');
            const actionInput = document.getElementById('action');
            const progressBar = document.getElementById('progress-bar');
            const totalQuestions = <?php echo $totalQuestions; ?>;
            let currentQuestion = <?php echo $currentQuestion; ?>;
            let timerInterval;
            
            // Initialize progress bar
            updateProgressBar();
            
            // Timer functionality
            if (!<?php echo $showSummary ? 'true' : 'false'; ?>) {
                startTimer(30 * 60); // 30 minutes timer
            }
            
            // Navigation buttons
            document.querySelectorAll('.prev-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    actionInput.value = 'prev';
                    quizForm.submit();
                });
            });
            
            document.querySelectorAll('.next-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const questionIndex = parseInt(currentQuestionInput.value);
                    const selectedOption = document.querySelector(`input[name="answers[${questionIndex}]"]:checked`);
                    
                    if (!selectedOption) {
                        alert('Please select an answer before proceeding.');
                        return;
                    }
                    
                    actionInput.value = 'next';
                    quizForm.submit();
                });
            });
            
            document.querySelectorAll('.submit-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const questionIndex = parseInt(currentQuestionInput.value);
                    const selectedOption = document.querySelector(`input[name="answers[${questionIndex}]"]:checked`);
                    
                    if (!selectedOption) {
                        alert('Please select an answer before submitting.');
                        return;
                    }
                    
                    if (confirm('Are you sure you want to submit your quiz?')) {
                        actionInput.value = 'submit';
                        clearInterval(timerInterval);
                        quizForm.submit();
                    }
                });
            });
            
            // Restart quiz button
            if (document.getElementById('restart-quiz')) {
                document.getElementById('restart-quiz').addEventListener('click', function() {
                    window.location.href = window.location.href.split('?')[0];
                });
            }
            
            // Certificate button
            if (document.getElementById('show-certificate')) {
                document.getElementById('show-certificate').addEventListener('click', function() {
                    const name = prompt('Enter your name for the certificate:', 'Quiz Participant');
                    if (name !== null) {
                        document.getElementById('certificate-name').textContent = name || 'Quiz Participant';
                        document.getElementById('certificate-container').style.display = 'block';
                        this.style.display = 'none';
                    }
                });
            }
            
            // Print certificate button
            if (document.getElementById('print-certificate')) {
                document.getElementById('print-certificate').addEventListener('click', function() {
                    window.print();
                });
            }
            
            // Update progress bar
            function updateProgressBar() {
                const progress = ((currentQuestion + 1) / totalQuestions) * 100;
                progressBar.style.width = `${progress}%`;
            }
            
            // Timer function
            function startTimer(durationInSeconds) {
                let timeLeft = durationInSeconds;
                const timerElement = document.getElementById('timer');
                
                function updateTimer() {
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    
                    timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    
                    // Change color when time is running low
                    if (timeLeft <= 60) {
                        timerElement.classList.add('danger');
                        timerElement.classList.remove('warning');
                    } else if (timeLeft <= 300) {
                        timerElement.classList.add('warning');
                        timerElement.classList.remove('danger');
                    } else {
                        timerElement.classList.remove('warning', 'danger');
                    }
                    
                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        alert('Time is up! Your quiz will be automatically submitted.');
                        actionInput.value = 'submit';
                        quizForm.submit();
                    } else {
                        timeLeft--;
                    }
                }
                
                updateTimer();
                timerInterval = setInterval(updateTimer, 1000);
            }
        });
    </script>
</body>
</html>