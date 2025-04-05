<!--This HTML document creates a web page for displaying a map of nearby car service centers using the Google Maps API, 
featuring a responsive layout with a navigation bar, a blurred background image, 
and functionality to locate the user's current position, search for nearby service centers, 
and display them as markers on the map with options to book services, all styled with Bootstrap and custom CSS.-->

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Map</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            html, body {
                height: 100%;
            }

            .background {
                position: fixed; /* Fixed position to cover the entire viewport */
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-image: url('css/3.jpg'); /* Replace with your image URL */
                background-size: cover;
                background-position: center;
                filter: blur(5px); /* Adjust the blur amount */
                z-index: -1; /* Send the background behind other content */
            }

            h2 {
                color: white; /* Change the color of the heading */
            }
            .container {
                max-width: 1000px; /* Adjust width as needed */
                background: rgba(255, 255, 255, 0.1); /* Semi-transparent white background */
                backdrop-filter: blur(10px); /* Apply blur effect */
                -webkit-backdrop-filter: blur(10px); /* For Safari support */
                padding: 20px;
                border-radius: 15px; /* Rounded corners */
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Subtle shadow */
                color: white; /* Text color */
            }
            .footer {
                background-color: #333;
                color: white;
                text-align: center;
                padding: 10px 0;
            }
            .black-text {
                color: black;
            }
        </style>
        
    </head>
    <body>
        <!--navigation bar-->
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
        <!--map container-->
        <div class="container my-5">
            <h2 class="text-center">Nearby Service Centers</h2>
            <div id="map" class="border" style="height: 500px;"></div>
        </div>
        <!--map configaratin and API connection-->
        <script>
            function initMap() {
                const defaultLocation = { lat: 7.8731, lng: 80.7718 };
                // Create the map
                const map = new google.maps.Map(document.getElementById("map"), {
                    center: defaultLocation,
                    zoom: 13,
                });
                // Get user's current location
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const userLocation = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude,
                            };
                            map.setCenter(userLocation);
                            // Add a marker for user's location
                            new google.maps.Marker({
                                position: userLocation,
                                map: map,
                                title: "You are here",
                            });
                            // Find nearby service centers
                            findNearbyPlaces(map, userLocation);
                        },
                        (error) => {
                            console.error("Geolocation error:", error);
                            alert("Unable to fetch location. Showing default location.");
                            findNearbyPlaces(map, defaultLocation);
                        }
                    );
                } else {
                    alert("Geolocation is not supported by your browser.");
                    findNearbyPlaces(map, defaultLocation);
                }
            }

            function findNearbyPlaces(map, location) {
                if (!map) {
                    console.error("Map not initialized.");
                    return;
                }

                const service = new google.maps.places.PlacesService(map);
                const request = {
                    location: location,
                    radius: 5000, 
                    keyword: "car service",
                };

                service.nearbySearch(request, (results, status) => {
                    if (status === google.maps.places.PlacesServiceStatus.OK) {
                        results.forEach((place) => {
                            const marker = new google.maps.Marker({
                                position: place.geometry.location,
                                map: map,
                                title: place.name,
                            });

                            const infoWindow = new google.maps.InfoWindow({
                                content: `
                                    <div>
                                        <h6 class="black-text">${place.name}</h6>
                                        <p class="black-text">${place.vicinity || "No address available"}</p>
                                        <a href="bookingSystem.php?center=${encodeURIComponent(place.name)}&location=${encodeURIComponent(place.vicinity)}" class="btn btn-primary btn-sm mt-2">Book Service</a>
                                    </div>
                                `,
                            });

                            marker.addListener("click", () => {
                                infoWindow.open(map, marker);
                            });
                        });
                    } else {
                        console.error("Places search failed:", status);
                    }
                });
            }
        </script>

        <script src="https://maps.googleapis.com/maps/api/js?key=<ADD_API_KEY>&libraries=places&callback=initMap" async defer></script>
        <!-- Footer -->
        <footer class="text-center py-3">
            &copy; 2024 Car Service Tracking System. All Rights Reserved.
            Contact Us: 0112785623 / info@carServiceTracking.lk
        </footer>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
