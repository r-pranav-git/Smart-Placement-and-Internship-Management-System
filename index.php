<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Smart Placement & Internship Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #141e30, #243b55);
            color: white;
        }

        /* NAVBAR */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 8%;
            background: rgba(0,0,0,0.2);
        }

        nav h2 {
            font-weight: 600;
        }

        nav a {
            text-decoration: none;
            color: white;
            margin-left: 20px;
            font-size: 15px;
            transition: 0.3s;
        }

        nav a:hover {
            color: #00d4ff;
        }

        /* HERO SECTION */
        .hero {
            text-align: center;
            padding: 80px 20px;
        }

        .hero h1 {
            font-size: 40px;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 18px;
            opacity: 0.9;
            max-width: 700px;
            margin: auto;
        }

        .hero-buttons {
            margin-top: 30px;
        }

        .hero-buttons a {
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 30px;
            margin: 10px;
            font-weight: 600;
            transition: 0.3s;
            display: inline-block;
        }

        .btn-primary {
            background: #00d4ff;
            color: #000;
        }

        .btn-primary:hover {
            background: #00aacc;
        }

        .btn-secondary {
            border: 2px solid #00d4ff;
            color: #00d4ff;
        }

        .btn-secondary:hover {
            background: #00d4ff;
            color: #000;
        }

        /* FEATURES SECTION */
        .features {
            padding: 60px 8%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .card {
            background: rgba(255,255,255,0.1);
            padding: 30px;
            border-radius: 15px;
            backdrop-filter: blur(8px);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            background: rgba(255,255,255,0.2);
        }

        .card h3 {
            margin-bottom: 15px;
        }

        .card p {
            font-size: 15px;
            opacity: 0.9;
        }

        /* FOOTER */
        footer {
            text-align: center;
            padding: 20px;
            background: rgba(0,0,0,0.3);
            margin-top: 50px;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 28px;
            }
        }

    </style>
</head>
<body>

<!-- NAVBAR -->
<nav>
    <h2>SPIMS</h2>
    <div>
        <a href="auth/login.php">Login</a>
        <a href="auth/register.php">Register</a>
    </div>
</nav>

<!-- HERO SECTION -->
<section class="hero">
    <h1>Smart Placement & Internship Management System</h1>
    <p>
        A centralized web-based platform designed to streamline placement 
        and internship processes with automated eligibility filtering, 
        role-based access control, and real-time application tracking.
    </p>

    <div class="hero-buttons">
        <a href="auth/register.php" class="btn-primary">Student Register</a>
        <a href="auth/login.php" class="btn-secondary">Login</a>
    </div>
</section>

<!-- FEATURES SECTION -->
<section class="features">
    <div class="card">
        <h3>👨‍🎓 Student Module</h3>
        <p>
            Create academic profile, view eligible opportunities, 
            apply for placements and internships, and track application status.
        </p>
    </div>

    <div class="card">
        <h3>🏢 Recruiter Module</h3>
        <p>
            Post job and internship openings, define eligibility criteria,
            shortlist candidates, and update selection results.
        </p>
    </div>

    <div class="card">
        <h3>🛠 Admin Control Panel</h3>
        <p>
            Manage students and recruiters, monitor drives,
            generate reports, and ensure secure system operations.
        </p>
    </div>
</section>

<!-- FOOTER -->
<footer>
    © 2026 Smart Placement & Internship Management System | MCA Project
</footer>

</body>
</html>
