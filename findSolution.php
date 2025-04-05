<!--This HTML and PHP code creates a web page for a "Find Car Problem Solution" feature in a car service tracking system, 
allowing logged-in users to search for solutions to vehicle problems, 
display suggested services from a database or via Google Custom Search API, 
and view their previously searched problems, all while utilizing Bootstrap for styling and responsive design.-->

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Find Car Problem Solution</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
        <style>
            .container {
                max-width: 1350px; /* Adjust width as needed */
                background: rgba(255, 255, 255, 0.1); /* Semi-transparent white background */
                backdrop-filter: blur(10px); /* Apply blur effect */
                -webkit-backdrop-filter: blur(10px); /* For Safari support */
                padding: 20px;
                border-radius: 15px; /* Rounded corners */
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Subtle shadow */
                color: white; /* Text color */
            }
        </style>
    </head>
    <body>
        <?php
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php'); // Redirect to login if not logged in
            exit();
        }
        $user_id = $_SESSION['user_id'];
        ?>
        <div class="background"></div>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href=""><strong>Car Service Tracking System</strong></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="btn btn-danger" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mt-5">
            <h1 class="text-center">Find Solutions For Vehicle Problems</h1>
            <form method="POST" action="" class="d-flex justify-content-center mt-4">
                <input type="text" name="problem" class="form-control w-50" placeholder="Enter your car problem" required>
                <button type="submit" class="btn btn-primary ms-2">Search</button>
            </form>

            <div id="solution" class="mt-4">
                <?php
                $conn = new mysqli("localhost", "root", "", "carservicetrackingsystem");
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                if (isset($_POST['problem'])) {
                    $problem = $conn->real_escape_string($_POST['problem']);
                    $sql = "SELECT suggested_service FROM problems WHERE issue_description = '$problem' AND user_id = '$user_id'";
                    $result = $conn->query($sql);

                    echo "<h3>Current Search</h3><div class='row'>";
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='col-md-4'>
                                    <div class='card mb-3'>
                                        <div class='card-body'>
                                            <h5 class='card-title'>Problem: " . htmlspecialchars($problem) . "</h5>
                                            <p class='card-text'>Solution: " . htmlspecialchars($row['suggested_service']) . "</p>
                                        </div>
                                    </div>
                                </div>";
                        }
                    } else {
                        // Google Custom Search API Integration
                        $apiKey = 'ADD_API_KEY';
                        $searchEngineId = '70c6b90195876406d';
                        $searchUrl = "https://www.googleapis.com/customsearch/v1?key=$apiKey&cx=$searchEngineId&q=" . urlencode($problem);

                        $response = file_get_contents($searchUrl);
                        $data = json_decode($response, true);

                        if (isset($data['items']) && count($data['items']) > 0) {
                            $solution = $data['items'][0]['snippet'];
                            $stmt = $conn->prepare("INSERT INTO problems (user_id, issue_description, suggested_service) VALUES (?, ?, ?)");
                            $stmt->bind_param("iss", $user_id, $problem, $solution);
                            if ($stmt->execute()) {
                                echo "<div class='col-md-4'>
                                        <div class='card mb-3'>
                                            <div class='card-body'>
                                                <h5 class='card-title'>Problem: " . htmlspecialchars($problem) . "</h5>
                                                <p class='card-text'>Solution: " . htmlspecialchars($solution) . "</p>
                                            </div>
                                        </div>
                                    </div>";
                            }
                            $stmt->close();
                        } else {
                            $solution = "No specific solution found. Please consult a professional mechanic.";
                            $stmt = $conn->prepare("INSERT INTO problems (user_id, issue_description, suggested_service) VALUES (?, ?, ?)");
                            $stmt->bind_param("iss", $user_id, $problem, $solution);
                            $stmt->execute();
                            echo "<p class='text-warning'>No solution found for " . htmlspecialchars($problem) . ". Default suggestion provided.</p>";
                            $stmt->close();
                        }
                    }
                    echo "</div>";
                }

                echo "<h3>Previously Searched Problems</h3><div class='row'>";
                $sql = "SELECT * FROM problems WHERE user_id = '$user_id' ORDER BY id DESC";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='col-md-4'>
                                <div class='card mb-3'>
                                    <div class='card-body'>
                                        <h5 class='card-title'>Problem: " . htmlspecialchars($row['issue_description']) . "</h5>
                                        <p class='card-text'>Solution: " . htmlspecialchars($row['suggested_service']) . "</p>
                                    </div>
                                </div>
                            </div>";
                    }
                } else {
                    echo "<p class='text-warning'>No previous searches found.</p>";
                }
                echo "</div>";

                $conn->close();
                ?>
            </div>
        </div>
        <!-- Footer -->
        <footer class="text-center py-3">
            &copy; 2024 Car Service Tracking System. All Rights Reserved.
            Contact Us: 0112785623 / info@carServiceTracking.lk
        </footer>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
