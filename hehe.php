<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Love Games for Us 💕🎮</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&family=Poppins:wght@300;400;600&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(-45deg, #ff6b6b, #feca57, #ff9ff3, #54a0ff);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
        }
        
        .heart-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }
        
        .floating-heart {
            position: absolute;
            color: rgba(255, 255, 255, 0.8);
            font-size: 20px;
            animation: floatUp 6s linear infinite;
        }
        
        @keyframes floatUp {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(-100px) rotate(360deg);
                opacity: 0;
            }
        }
        
        .game-menu {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 30px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            animation: slideIn 2s ease-out;
            position: relative;
            z-index: 10;
            max-width: 800px;
        }
        
        .game-container {
            display: none;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 30px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            position: relative;
            z-index: 10;
            max-width: 800px;
        }
        
        @keyframes slideIn {
            0% {
                transform: translateY(100px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .title {
            font-family: 'Dancing Script', cursive;
            font-size: 3.5rem;
            color: #e74c3c;
            margin-bottom: 20px;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .message {
            font-size: 1.2rem;
            color: #2c3e50;
            line-height: 1.8;
            margin-bottom: 30px;
            animation: fadeInUp 2s ease-out 0.5s both;
        }
        
        @keyframes fadeInUp {
            0% {
                transform: translateY(30px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .love-button {
            background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            box-shadow: 0 10px 30px rgba(255, 107, 107, 0.4);
            animation: bounceIn 2s ease-out 1s both;
        }
        
        @keyframes bounceIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .love-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(255, 107, 107, 0.6);
            background: linear-gradient(45deg, #ff5252, #ff7979);
        }
        
        .game-buttons {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin: 30px 0;
        }
        
        .memory-board {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            max-width: 400px;
            margin: 20px auto;
        }
        
        .memory-card {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #ff9ff3, #54a0ff);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 3px solid transparent;
        }
        
        .memory-card:hover {
            transform: scale(1.05);
        }
        
        .memory-card.flipped {
            background: white;
            border-color: #ff6b6b;
        }
        
        .game-area {
            width: 100%;
            height: 400px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            position: relative;
            overflow: hidden;
            margin: 20px 0;
        }
        
        .falling-heart {
            position: absolute;
            font-size: 30px;
            cursor: pointer;
            animation: fall 3s linear infinite;
        }
        
        @keyframes fall {
            from { top: -50px; }
            to { top: 450px; }
        }
        
        .tic-board {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            max-width: 300px;
            margin: 20px auto;
        }
        
        .tic-cell {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #ff9ff3, #54a0ff);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .tic-cell:hover {
            transform: scale(1.05);
        }
        
        .sparkle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: white;
            border-radius: 50%;
            animation: sparkle 2s linear infinite;
        }
        
        @keyframes sparkle {
            0%, 100% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .photo-frame {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin: 20px auto;
            background: linear-gradient(45deg, #ff9a9e, #fecfef);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            animation: rotate 10s linear infinite;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .music-note {
            position: absolute;
            font-size: 24px;
            color: rgba(255, 255, 255, 0.7);
            animation: musicFloat 4s ease-in-out infinite;
        }
        
        @keyframes musicFloat {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }
        
        .celebration {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 100;
        }
        
        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #ff6b6b;
            animation: confettiFall 3s linear infinite;
        }
        
        @keyframes confettiFall {
            0% {
                transform: translateY(-100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
        
        .love-quote {
            font-family: 'Dancing Script', cursive;
            font-size: 1.5rem;
            color: #8e44ad;
            margin-top: 20px;
            font-style: italic;
            animation: fadeIn 3s ease-out 2s both;
        }
        
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="heart-container" id="heartContainer"></div>
    
    <div class="container">
        <div class="game-menu" id="gameMenu">
            <h1 class="title">Love Games for Us 💕🎮</h1>
            
            <div class="photo-frame">
                🎯
            </div>
            
            <p class="message">
                Let's play some fun games together! 💖<br>
                Each game is made with love just for us ✨
            </p>
            
            <div class="game-buttons">
                <button class="love-button" onclick="startGame('memory')">
                    💕 Memory Game
                </button>
                <button class="love-button" onclick="startGame('catch')">
                    💖 Catch Hearts
                </button>
                <button class="love-button" onclick="startGame('quiz')">
                    🌟 Love Quiz
                </button>
                <button class="love-button" onclick="startGame('tic')">
                    💝 Tic Tac Love
                </button>
            </div>
        </div>
        
        <!-- Game Containers -->
        <div class="game-container" id="memoryGame">
            <h2 class="title">💕 Memory Game</h2>
            <p>Match the love pairs!</p>
            <div id="memoryBoard" class="memory-board"></div>
            <button class="love-button" onclick="backToMenu()">Back to Menu</button>
        </div>
        
        <div class="game-container" id="catchGame">
            <h2 class="title">💖 Catch Hearts</h2>
            <p>Score: <span id="score">0</span></p>
            <div id="gameArea" class="game-area"></div>
            <button class="love-button" onclick="backToMenu()">Back to Menu</button>
        </div>
        
        <div class="game-container" id="quizGame">
            <h2 class="title">🌟 Love Quiz</h2>
            <div id="quizContent"></div>
            <button class="love-button" onclick="backToMenu()">Back to Menu</button>
        </div>
        
        <div class="game-container" id="ticGame">
            <h2 class="title">💝 Tic Tac Love</h2>
            <div id="ticBoard" class="tic-board"></div>
            <button class="love-button" onclick="resetTic()">New Game</button>
            <button class="love-button" onclick="backToMenu()">Back to Menu</button>
        </div>
    </div>
    
    <div class="celebration" id="celebration"></div>
    
    <!-- Music Notes -->
    <div class="music-note" style="top: 10%; left: 10%;">🎵</div>
    <div class="music-note" style="top: 20%; right: 15%; animation-delay: -1s;">🎶</div>
    <div class="music-note" style="bottom: 30%; left: 20%; animation-delay: -2s;">🎵</div>
    <div class="music-note" style="bottom: 10%; right: 10%; animation-delay: -3s;">🎶</div>
    
    <script>
        let currentGame = null;
        let score = 0;
        let memoryCards = [];
        let flippedCards = [];
        let ticBoard = ['', '', '', '', '', '', '', '', ''];
        let currentPlayer = '💖';
        
        // Game navigation
        function startGame(gameType) {
            document.getElementById('gameMenu').style.display = 'none';
            document.querySelectorAll('.game-container').forEach(g => g.style.display = 'none');
            
            currentGame = gameType;
            document.getElementById(gameType + 'Game').style.display = 'block';
            
            if (gameType === 'memory') initMemoryGame();
            if (gameType === 'catch') initCatchGame();
            if (gameType === 'quiz') initQuizGame();
            if (gameType === 'tic') initTicGame();
        }
        
        function backToMenu() {
            document.querySelectorAll('.game-container').forEach(g => g.style.display = 'none');
            document.getElementById('gameMenu').style.display = 'block';
            currentGame = null;
        }
        
        // Memory Game
        function initMemoryGame() {
            const emojis = ['💕', '💖', '💝', '💗', '🌹', '💐', '✨', '🌟'];
            memoryCards = [...emojis, ...emojis].sort(() => Math.random() - 0.5);
            const board = document.getElementById('memoryBoard');
            board.innerHTML = '';
            
            memoryCards.forEach((emoji, index) => {
                const card = document.createElement('div');
                card.className = 'memory-card';
                card.dataset.index = index;
                card.innerHTML = '💭';
                card.onclick = () => flipCard(index);
                board.appendChild(card);
            });
        }
        
        function flipCard(index) {
            if (flippedCards.length >= 2) return;
            const card = document.querySelector(`[data-index="${index}"]`);
            if (card.classList.contains('flipped')) return;
            
            card.classList.add('flipped');
            card.innerHTML = memoryCards[index];
            flippedCards.push(index);
            
            if (flippedCards.length === 2) {
                setTimeout(() => {
                    if (memoryCards[flippedCards[0]] === memoryCards[flippedCards[1]]) {
                        celebrate();
                        if (document.querySelectorAll('.memory-card.flipped').length === 16) {
                            setTimeout(() => alert('🎉 You won! Perfect match! 💕'), 500);
                        }
                    } else {
                        flippedCards.forEach(i => {
                            const c = document.querySelector(`[data-index="${i}"]`);
                            c.classList.remove('flipped');
                            c.innerHTML = '💭';
                        });
                    }
                    flippedCards = [];
                }, 1000);
            }
        }
        
        // Catch Hearts Game
        function initCatchGame() {
            score = 0;
            document.getElementById('score').textContent = score;
            const gameArea = document.getElementById('gameArea');
            gameArea.innerHTML = '';
            
            setInterval(() => {
                if (currentGame === 'catch') createFallingHeart();
            }, 1000);
        }
        
        function createFallingHeart() {
            const heart = document.createElement('div');
            heart.className = 'falling-heart';
            heart.innerHTML = ['💖', '💕', '💝', '💗'][Math.floor(Math.random() * 4)];
            heart.style.left = Math.random() * 90 + '%';
            heart.onclick = () => {
                score++;
                document.getElementById('score').textContent = score;
                heart.remove();
                celebrate();
            };
            
            document.getElementById('gameArea').appendChild(heart);
            setTimeout(() => heart.remove(), 3000);
        }
        
        // Love Quiz Game
        function initQuizGame() {
            const questions = [
                { q: "What makes our love special?", a: ["Your beautiful smile", "Your kind heart", "Everything about you", "All of the above"], correct: 3 },
                { q: "How much do I love you?", a: ["A lot", "So much", "More than words", "To infinity and beyond"], correct: 3 },
                { q: "What's our favorite thing to do together?", a: ["Laugh together", "Make memories", "Just be together", "All of the above"], correct: 3 }
            ];
            
            let currentQ = 0;
            
            function showQuestion() {
                const content = document.getElementById('quizContent');
                if (currentQ >= questions.length) {
                    content.innerHTML = '<h3>🎉 Perfect score! You know our love so well! 💕</h3>';
                    celebrate();
                    return;
                }
                
                const q = questions[currentQ];
                content.innerHTML = `
                    <h3>${q.q}</h3>
                    ${q.a.map((answer, i) => `
                        <button class="love-button" onclick="answerQuiz(${i})" style="display: block; margin: 10px auto;">
                            ${answer}
                        </button>
                    `).join('')}
                `;
            }
            
            window.answerQuiz = (answer) => {
                if (answer === questions[currentQ].correct) {
                    celebrate();
                    setTimeout(() => {
                        currentQ++;
                        showQuestion();
                    }, 1000);
                } else {
                    alert('💕 Try again, my love!');
                }
            };
            
            showQuestion();
        }
        
        // Tic Tac Love Game
        function initTicGame() {
            ticBoard = ['', '', '', '', '', '', '', '', ''];
            currentPlayer = '💖';
            const board = document.getElementById('ticBoard');
            board.innerHTML = '';
            
            for (let i = 0; i < 9; i++) {
                const cell = document.createElement('div');
                cell.className = 'tic-cell';
                cell.onclick = () => makeMove(i);
                board.appendChild(cell);
            }
        }
        
        function makeMove(index) {
            if (ticBoard[index] !== '') return;
            
            ticBoard[index] = currentPlayer;
            document.querySelectorAll('.tic-cell')[index].innerHTML = currentPlayer;
            
            if (checkWin()) {
                setTimeout(() => {
                    alert(`🎉 ${currentPlayer} wins! 💕`);
                    celebrate();
                }, 100);
                return;
            }
            
            currentPlayer = currentPlayer === '💖' ? '💝' : '💖';
        }
        
        function checkWin() {
            const wins = [[0,1,2],[3,4,5],[6,7,8],[0,3,6],[1,4,7],[2,5,8],[0,4,8],[2,4,6]];
            return wins.some(combo => 
                combo.every(i => ticBoard[i] === currentPlayer && ticBoard[i] !== '')
            );
        }
        
        function resetTic() {
            initTicGame();
        }
        
        // Celebration effects
        function celebrate() {
            for (let i = 0; i < 20; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    confetti.style.left = Math.random() * 100 + '%';
                    confetti.style.backgroundColor = ['#ff6b6b', '#feca57', '#ff9ff3', '#54a0ff'][Math.floor(Math.random() * 4)];
                    document.getElementById('celebration').appendChild(confetti);
                    setTimeout(() => confetti.remove(), 3000);
                }, i * 100);
            }
        }
        
        // Background effects
        function createHeart() {
            const heart = document.createElement('div');
            heart.className = 'floating-heart';
            heart.innerHTML = '❤️';
            heart.style.left = Math.random() * 100 + '%';
            heart.style.animationDuration = (Math.random() * 3 + 3) + 's';
            document.getElementById('heartContainer').appendChild(heart);
            setTimeout(() => heart.remove(), 6000);
        }
        
        function createSparkle() {
            const sparkle = document.createElement('div');
            sparkle.className = 'sparkle';
            sparkle.style.left = Math.random() * 100 + '%';
            sparkle.style.top = Math.random() * 100 + '%';
            document.body.appendChild(sparkle);
            setTimeout(() => sparkle.remove(), 2000);
        }
        
        setInterval(createHeart, 800);
        setInterval(createSparkle, 300);
        
        console.log('💕 Love Games ready! Have fun together! 💕');
    </script>
</body>
</html>