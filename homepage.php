<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ressiment - Residency Management Software</title>
    <!-- Google Fonts for custom typography -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Roboto:wght@300;500&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        /* Body Styling */
        body {
            background-color: #FFFFFF; /* White Background */
            color: #333333; /* Dark Gray Text */
            line-height: 1.6;
            padding-top: 80px; /* Account for fixed header */
        }

        /* Header Section */
        header {
            background-color: #008080; /* Teal Background */
            color: white;
            padding: 20px;
            position: fixed;
            width: 100%;
            z-index: 10;
            top: 0;
            left: 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        header nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header .logo h1 {
            font-size: 28px;
            font-weight: 600;
            color: white;
        }

        header .nav-links {
            list-style: none;
            display: flex;
        }

        header .nav-links li {
            margin-left: 20px;
        }

        header .nav-links li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        header .nav-links li a:hover {
            color: #0A0A2A; /* Light Blue on hover */
            transform: scale(1.1);
        }

        /* Authentication Links (Login/Sign Up) Styled as Buttons */
        header .auth-links {
            display: flex;
            gap: 15px;
        }

        header .auth-links a {
            background-color: #000080; /* Navy Blue */
            color: white;
            text-decoration: none;
            font-size: 16px;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        header .auth-links a:hover {
            background-color: #1E3A5F; /* Darker Navy Blue on hover */
            color: white;
        }

        header .auth-links a i {
            margin-right: 8px; /* Space between icon and text */
        }

        /* Search Bar in Navbar */
        header .search-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 40%;
            background-color: #F8F8F8; /* Light Gray */
            padding: 5px;
            border-radius: 25px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        header .search-bar input {
            width: 85%;
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            border-radius: 25px;
            outline: none;
            color: #333;
        }

        header .search-bar button {
            background-color: #333333; /* Teal */
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        header .search-bar button:hover {
            background-color: #1E3A5F; /* Darker Navy Blue on hover */
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <h1>Ressiment</h1>
            </div>
            <!-- Search Bar Section -->
            <div class="search-bar">
                <input type="text" placeholder="Search...">
                <button>Search</button>
            </div>
            <ul class="nav-links">
                <li><a href="#features"><i class="fas fa-cogs"></i> Features</a></li>
                <li><a href="#pricing"><i class="fas fa-tags"></i> Pricing</a></li>
                <li><a href="#demo"><i class="fas fa-video"></i> Request Demo</a></li>
                <li><a href="loginpage.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                <li><a href="registerpage.php"><i class="fas fa-user-plus"></i> Sign Up</a></li>
            </ul>
        </nav>
    </header>

    

</body>
</html>