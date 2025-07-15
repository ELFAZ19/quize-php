document.addEventListener("DOMContentLoaded", function () {
    // Theme toggle functionality
    const themeToggle = document.getElementById("theme-toggle");
    const currentTheme = localStorage.getItem("theme") || "light";
    document.documentElement.setAttribute("data-theme", currentTheme);

    if (themeToggle) {
        themeToggle.addEventListener("click", function () {
            const newTheme = currentTheme === "light" ? "dark" : "light";
            document.documentElement.setAttribute("data-theme", newTheme);
            localStorage.setItem("theme", newTheme);
        });
    }

    // Quiz functionality
    const quizForm = document.getElementById("quiz-form");
    if (quizForm) {
        const currentQuestionInput = document.getElementById("current-question");
        const actionInput = document.getElementById("action");
        const progressBar = document.getElementById("progress-bar");
        const progressText = document.getElementById("progress-text");
        const timeoutFlag = document.getElementById("timeout-flag");
        const pauseBtn = document.getElementById("pause-btn");
        const totalQuestions = parseInt(progressText.textContent.split("/")[1]);
        let currentQuestion = parseInt(currentQuestionInput.value);
        let timerInterval;
        let timeLeft = 60; // 1 minute per question
        let isPaused = false;

        // Start timer
        startTimer(timeLeft);

        // Navigation buttons
        document.querySelectorAll(".prev-btn").forEach((btn) => {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                if (!isPaused) {
                    clearInterval(timerInterval);
                }
                actionInput.value = "prev";
                quizForm.submit();
            });
        });

        document.querySelectorAll(".next-btn").forEach((btn) => {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                const questionIndex = parseInt(currentQuestionInput.value);
                const selectedOption = document.querySelector(
                    `input[name="answers[${questionIndex}]"]:checked`
                );

                if (!selectedOption && timeoutFlag.value !== "true") {
                    showCustomPopup("Please select an answer before proceeding.", true);
                    return false;
                }

                if (!isPaused) {
                    clearInterval(timerInterval);
                }
                actionInput.value = "next";
                quizForm.submit();
            });
        });

        document.querySelectorAll(".submit-btn").forEach((btn) => {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                const questionIndex = parseInt(currentQuestionInput.value);
                const selectedOption = document.querySelector(
                    `input[name="answers[${questionIndex}]"]:checked`
                );

                if (!selectedOption && timeoutFlag.value !== "true") {
                    showCustomPopup("Please select an answer before submitting.", true);
                    return false;
                }

                showCustomPopup("Are you sure you want to submit your quiz?", false, () => {
                    if (!isPaused) {
                        clearInterval(timerInterval);
                    }
                    actionInput.value = "submit";
                    quizForm.submit();
                });
            });
        });

        // Pause button functionality
        if (pauseBtn) {
            pauseBtn.addEventListener("click", function (e) {
                e.preventDefault();
                isPaused = !isPaused;

                if (isPaused) {
                    clearInterval(timerInterval);
                    pauseBtn.textContent = "▶ Resume";
                    pauseBtn.style.backgroundColor = "#2ecc71";

                    // Disable all radio buttons while paused
                    document.querySelectorAll('input[type="radio"]').forEach((radio) => {
                        radio.disabled = true;
                    });

                    // Show notification
                    const notification = document.createElement("div");
                    notification.className = "notification";
                    notification.textContent = "Quiz is paused. Click Resume to continue.";
                    document.querySelector(".quiz-container").prepend(notification);

                    // Remove notification after 3 seconds
                    setTimeout(() => {
                        notification.remove();
                    }, 3000);
                } else {
                    startTimer(timeLeft);
                    pauseBtn.textContent = "⏸ Pause";
                    pauseBtn.style.backgroundColor = "";

                    // Re-enable radio buttons
                    document.querySelectorAll('input[type="radio"]').forEach((radio) => {
                        radio.disabled = false;
                    });
                }
            });
        }

        // Timer function
        function startTimer(durationInSeconds) {
            let timeLeft = durationInSeconds;
            const timerElement = document.getElementById("timer");

            function updateTimer() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;

                timerElement.textContent = `${minutes.toString().padStart(2, "0")}:${seconds.toString().padStart(2, "0")}`;

                // Visual feedback when time is running low
                if (timeLeft <= 10) {
                    timerElement.classList.add("danger");
                    timerElement.classList.remove("warning");
                } else if (timeLeft <= 30) {
                    timerElement.classList.add("warning");
                    timerElement.classList.remove("danger");
                }

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    timeoutFlag.value = "true";
                    showCustomPopup("Time's up! Moving to next question...", true);
                    setTimeout(() => {
                        actionInput.value = "next";
                        quizForm.submit();
                    }, 1500);
                } else if (!isPaused) {
                    timeLeft--;
                }
            }

            // Clear any existing timer
            if (timerInterval) {
                clearInterval(timerInterval);
            }
            
            // Start new timer
            updateTimer();
            timerInterval = setInterval(updateTimer, 1000);
        }
    }

    // Restart quiz button
    const restartBtn = document.getElementById("restart-quiz");
    if (restartBtn) {
        restartBtn.addEventListener("click", function () {
            window.location.href = window.location.href.split("?")[0];
        });
    }

    // Certificate button
    const showCertBtn = document.getElementById("show-certificate");
    if (showCertBtn) {
        showCertBtn.addEventListener("click", function () {
            const name = prompt("Enter your name for the certificate:", "Quiz Participant");
            if (name !== null) {
                document.getElementById("certificate-name").textContent = name || "Quiz Participant";
                document.getElementById("certificate-container").style.display = "block";
                this.style.display = "none";
            }
        });
    }

    // Download certificate button
    const downloadCertBtn = document.getElementById("download-certificate");
    if (downloadCertBtn) {
        downloadCertBtn.addEventListener("click", function () {
            const certificate = document.querySelector(".certificate");

            html2canvas(certificate).then((canvas) => {
                const link = document.createElement("a");
                link.download = "PHP-Quiz-Certificate.png";
                link.href = canvas.toDataURL("image/png");
                link.click();
            });
        });
    }

    // Export results button
    const exportResultsBtn = document.getElementById("export-results");
    if (exportResultsBtn) {
        exportResultsBtn.addEventListener("click", function () {
            const allResults = JSON.parse(localStorage.getItem("quizResults") || "[]");
            if (allResults.length === 0) {
                showCustomPopup("No quiz results to export.", true);
                return;
            }

            // Convert to CSV
            let csv = "Date,Score,Total Questions\n";
            allResults.forEach((result) => {
                csv += `${new Date(result.date).toLocaleDateString()},${result.score},${result.totalQuestions}\n`;
            });

            // Create download link
            const blob = new Blob([csv], { type: "text/csv" });
            const url = URL.createObjectURL(blob);
            const link = document.createElement("a");
            link.href = url;
            link.download = "php-quiz-results.csv";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }
});

// Custom popup function
function showCustomPopup(message, isError = false, callback = null) {
    const popup = document.createElement('div');
    popup.className = `custom-popup ${isError ? 'error' : 'info'}`;
    popup.innerHTML = `
        <div class="popup-content">
            <p>${message}</p>
            <button class="popup-close">OK</button>
        </div>
    `;
    
    document.body.appendChild(popup);
    
    // Close button functionality
    popup.querySelector('.popup-close').addEventListener('click', () => {
        popup.remove();
        if (callback && typeof callback === 'function') {
            callback();
        }
    });
    
    // Auto-close after 3 seconds if no callback
    if (!callback) {
        setTimeout(() => {
            if (document.body.contains(popup)) {
                popup.remove();
            }
        }, 3000);
    }
}

// Check answer function for immediate feedback
function checkAnswer(radio) {
    const questionContainer = radio.closest('.question-container');
    const questionIndex = parseInt(questionContainer.id.split('-')[1]);
    const feedbackDiv = questionContainer.querySelector('.immediate-feedback');
    
    // Get the correct answer from the server-side data
    const correctAnswer = <?= json_encode(array_column($quiz, 'answer')) ?>[questionIndex];
    const userAnswer = radio.value;
    
    // Mark question as answered
    questionContainer.classList.add('answered');
    
    // Disable all options in this question
    questionContainer.querySelectorAll('input[type="radio"]').forEach(option => {
        option.disabled = true;
    });
    
    // Create or update feedback
    if (!feedbackDiv) {
        const newFeedback = document.createElement('div');
        newFeedback.className = `immediate-feedback ${userAnswer === correctAnswer ? 'correct' : 'incorrect'}`;
        
        if (userAnswer === correctAnswer) {
            newFeedback.innerHTML = `
                <p>✅ Correct!</p>
                <p class="explanation">${<?= json_encode(array_column($quiz, 'explanation')) ?>[questionIndex]}</p>
            `;
        } else {
            newFeedback.innerHTML = `
                <p>❌ Incorrect</p>
                <p>Your answer: <span class="user-answer">${userAnswer}</span></p>
                <p>Correct answer: <span class="correct-answer">${correctAnswer}</span></p>
                <p class="explanation">${<?= json_encode(array_column($quiz, 'explanation')) ?>[questionIndex]}</p>
            `;
        }
        
        questionContainer.querySelector('.question').appendChild(newFeedback);
    }
    
    // Scroll to feedback if it's not fully visible
    feedbackDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}