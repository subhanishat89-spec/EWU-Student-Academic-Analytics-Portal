<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | EWU Academic Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ewu-green: #006a4e;
            --ewu-gold: #f2a900;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--ewu-green) 0%, #004d39 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: var(--white);
        }

        /* Decorative background circles */
        body::before, body::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(242, 169, 0, 0.1);
            z-index: -1;
        }
        body::before { top: -50px; left: -50px; }
        body::after { bottom: -50px; right: -50px; }

        .welcome-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 50px 40px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 90%;
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo {
            width: 100px;
            margin-bottom: 20px;
            filter: drop-shadow(0 0 10px rgba(242, 169, 0, 0.5));
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        h1 span {
            color: var(--ewu-gold);
        }

        p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            opacity: 0.9;
            line-height: 1.6;
        }

        .start-btn {
            display: inline-block;
            text-decoration: none;
            background: var(--ewu-gold);
            color: var(--ewu-green);
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(242, 169, 0, 0.3);
            border: 2px solid transparent;
        }

        .start-btn:hover {
            background: transparent;
            color: var(--ewu-gold);
            border-color: var(--ewu-gold);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(242, 169, 0, 0.4);
        }

        .footer-text {
            margin-top: 30px;
            font-size: 0.8rem;
            opacity: 0.6;
        }
    </style>
</head>
<body>

    <div class="welcome-card">
       
        
        <h1>EWU <span>Academic Portal</span></h1>
        <p>Your all-in-one destination for tracking semester grades, calculating weighted CGPA, and monitoring academic growth.</p>
        
        <a href="index.php" class="start-btn">Go to Calculator →</a>
        
        <div class="footer-text">
            &copy; 2026 East West University | Designed for Excellence
        </div>
    </div>

</body>
</html>