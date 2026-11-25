<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-family: sans-serif;
        }

        a {
            padding: 12px 24px;
            border-radius: 6px;
            color: white;
            background: #696cff;
            text-decoration: none;
        }

        a:hover {
            background: #575ae8;
        }
    </style>
</head>
<body>
    <a href="{{route('login')}}">Login</a>
    <a href="{{route('register')}}">Register</a>
</body>
</html>
