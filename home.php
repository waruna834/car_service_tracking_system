<!--This HTML document creates a responsive landing page for a Car Service Tracking System, 
featuring a navigation bar, a hero section with a call-to-action, sections highlighting the system's features, 
an about us description, a frequently asked questions (FAQ) section, and a footer, all styled with Bootstrap and custom CSS, 
while also checking for user cookies to enable automatic login.-->

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Car Service Tracking System</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/style.css">
        <style>
            :root {
                --primary-color: #2c3e50;
                --secondary-color: #3498db;
                --accent-color: #e74c3c;
                --light-color: #ecf0f1;
                --dark-color: #2c3e50;
            }

            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                background-color: var(--light-color);
                color: var(--dark-color);
            }

            .hero-image {
                background: linear-gradient(rgba(44, 62, 80, 0.7), rgba(44, 62, 80, 0.7)), 
                            url('css/3.jpg') no-repeat center center/cover;
                height: 400px;
                color: white;
                display: flex;
                justify-content: center;
                align-items: center;
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
            }

            .hero-text {
                text-align: center;
            }

            .features {
                padding: 50px 20px;
                background-color: #566573;
            }

            .features .icon {
                font-size: 40px;
                color: var(--secondary-color);
            }

            .navbar {
                background-color: black;
            }

            .btn-primary {
                background-color: var(--secondary-color);
                border-color: var(--secondary-color);
            }

            .btn-primary:hover {
                background-color: #2980b9;
                border-color: #2980b9;
            }

            footer {
                background-color: black;
                color: white;
            }

            .nav-link:hover {
                color: var(--secondary-color) !important;
            }

            h2, h4 {
                color: black;
            }

            .features .col-md-4 {
                background-color:rgb(171, 203, 230);
                padding: 20px;
                border-radius: 50px;
                transition: transform 0.3s ease;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }

            .features .col-md-4:hover {
                transform: translateY(-10px);
                transition: transform 0.3s ease;
            }

            .features .col-md-4:hover .icon {
                color: var(--accent-color);
            }
        </style>
    </head>
    <body>

        <?php
        session_start();

        // Check if cookies are set for automatic login
        if (isset($_COOKIE['user_id'])) {
            // Set session variables from cookies
            $_SESSION['user_id'] = $_COOKIE['user_id'];
            $_SESSION['user_email'] = $_COOKIE['user_email'];

            // Redirect to the dashboard or another page
            header("Location: dashboard.php");
            exit();
        }
        ?>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Car Service Tracking System</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="contacts.php">Contact Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary" href="login.php">Login</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Hero Section -->
        <div class="hero-image">
            <div class="hero-text">
                <h1 class="display-4">Your Vehicle, Our Priority</h1>
                <p>Keep track of your car services and ensure a smooth ride!</p><br>
                <a href="register.php" class="btn btn-primary btn-lg">Get Started</a><br>
                <p>If your new to this site click this!</p>
            </div>
        </div>
        <!-- Features Section -->
        <section class="features text-center">
            <div class="container">
                <h2 class="my-4">Why Choose Us?</h2>
                <div class="row g-4"> <!-- Add g-4 for gutter spacing -->
                    <div class="col-md-4">
                        <span class="icon">🚗</span>
                        <h4>Track Services</h4>
                        <p>Monitor your vehicle's service history and upcoming service dates with ease.</p>
                    </div>
                    <div class="col-md-4">
                        <span class="icon">📍</span>
                        <h4>Locate Service Centers</h4>
                        <p>Find the nearest car service centers and accessories shops on the map.</p>
                    </div>
                    <div class="col-md-4">
                        <span class="icon">📱</span>
                        <h4>Real-Time Alerts</h4>
                        <p>Get timely SMS and email reminders for your next service date.</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- About Section -->
        <section class="container my-5">
            <h2 class="text-center mb-4">About Us</h2>
            <p>
                The Car Service Tracking System is a one-stop solution for car owners to keep their vehicles in perfect condition. 
                We provide an intuitive platform to manage service schedules, track service history, and locate service centers. 
                Our mission is to make vehicle maintenance hassle-free, ensuring your car performs at its best for years to come.
            </p>
            <p>
                With real-time notifications and advanced tracking features, our system is designed to give car owners peace of mind. 
                Whether it's an oil change or a full service, we help you stay on top of it all.
            </p>
        </section>
        <!-- FAQ Section -->
        <section class="faq container my-5">
            <h2 class="text-center mb-4">Frequently Asked Questions</h2>
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">How do I add my car & service detail for tracking service dates?</button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">After login goto service prediction tab and enter detail and click "Predict Next Service Date" button.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">Will I get reminders for service?</button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">Yes! Our system sends email reminders before your next service.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">Can I get overview of my car service detail after adding details?</button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">Yes! Our system show overview data in dashboard tab.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">Can I book service with this site?</button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">Yes! You can go to service map tab and select location and then book the service.</div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Footer -->
        <footer class="text-center py-3">
            &copy; 2024 Car Service Tracking System. All Rights Reserved.
            Contact Us: 0112785623 / info@carServiceTracking.lk
        </footer>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>

