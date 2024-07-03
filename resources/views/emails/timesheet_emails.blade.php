<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Timesheet</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin: 20px 0;
        }
        .header img {
            height: auto;
            width: 252px;
        }
        .content {
            padding: 20px;
            text-align: center;
        }
        .footer {
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #888888;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://globalxtreme.net/images/share/gx_new_logo.png" alt="Global Xtreme Logo">
        </div>
        <div class="content">
            <h1>Employee Timesheet</h1>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} GlobalXtreme. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
