<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register</title>
    <!-- Include Bootstrap CSS from a CDN for styling -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%; /* Make sure the body takes up the full height */
            margin: 0; /* Remove default margin */
        }
        body {
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            background-color: #f8f9fa;
        }
        .card {
            width: 100%;
            max-width: 360px;
            padding: 20px;
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center; /* Ensure content is centered */
        }
        .card img {
            max-width: 100px; /* Adjust size as needed */
            margin-bottom: 20px; /* Space between logo and buttons */
            display: block; /* Ensures the image is a block element */
            margin-left: auto; /* Together with margin-right, centers the image */
            margin-right: auto;
        }
        .btn-primary, .btn-secondary {
            width: 100%;
            margin-bottom: 10px; /* Add bottom margin to buttons */
        }
        .btn-secondary {
            margin-bottom: 0; /* Remove bottom margin from last button */
        }
    </style>
</head>
<body>
    <div class="card">
        <img src="{{ asset('img/dashboard/assets.png') }}" alt="Logo"> <!-- Logo added here -->
        @if (Route::has('login'))
            <div class="text-center">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-secondary">Register</a>
                    @endif
                @endauth
            </div>
        @endif
    </div>

    <!-- Include Bootstrap JavaScript and jQuery from a CDN for the responsive features -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
