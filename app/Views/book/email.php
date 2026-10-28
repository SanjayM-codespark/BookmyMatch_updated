<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>OTP Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            padding: 20px;
        }
        .content {
            max-width: 600px;
            background-color: #ffffff;
            padding: 30px;
            margin: 0 auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333333;
        }
        .message {
            font-size: 16px;
            color: #666666;
            margin-bottom: 20px;
        }
        .otp {
            font-size: 24px;
            font-weight: bold;
            color: #333333;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            font-size: 14px;
            color: #999999;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <div class="header">
                <h1>Verify Your Account</h1>
            </div>
            <div class="message">
                Dear <?=$user;?>,
                <br><br>
                Thank you for choosing to verify your account with us. Please use the following OTP (One-Time Password) to complete your verification process:
            </div>
            <div class="otp">
                <?=$otp_code;?>
            </div>
            <div class="message">
                If you did not initiate this request, please disregard this email.
            </div>
            <div class="footer">
                Thank you<br>
            </div>
        </div>
    </div>
</body>
</html>
