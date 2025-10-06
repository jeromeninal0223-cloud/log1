<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .otp-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin: 20px 0;
        }
        .security-tips {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .security-tips h4 {
            color: #495057;
            margin-top: 0;
        }
        .security-tips ul {
            margin: 0;
            padding-left: 20px;
        }
        .security-tips li {
            margin: 5px 0;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Password Reset Request</h1>
            <p>Jetlouge Travels Staff Portal</p>
        </div>
        
        <div class="content">
            <h2>Hello{{ $userName ? ', ' . $userName : '' }}!</h2>
            
            <p>We received a request to reset your password for your Jetlouge Travels staff account. Use the OTP code below to proceed with resetting your password.</p>
            
            <div class="otp-box">
                <p style="margin: 0; font-size: 16px;">Your OTP Code:</p>
                <div class="otp-code">{{ $otp }}</div>
                <p style="margin: 0; font-size: 14px; opacity: 0.9;">Valid for 10 minutes</p>
            </div>
            
            <div class="warning">
                <strong>⚠️ Important Security Notice:</strong>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    <li>This OTP will expire in <strong>10 minutes</strong></li>
                    <li>Do not share this code with anyone</li>
                    <li>If you didn't request this reset, please ignore this email</li>
                </ul>
            </div>
            
            <p>To complete your password reset:</p>
            <ol>
                <li>Return to the login page</li>
                <li>Click "Forgot Password?"</li>
                <li>Enter this OTP code: <strong>{{ $otp }}</strong></li>
                <li>Create your new password</li>
            </ol>
            
            <div class="security-tips">
                <h4>🛡️ Security Tips for Your New Password:</h4>
                <ul>
                    <li>Use at least 8 characters</li>
                    <li>Include uppercase and lowercase letters</li>
                    <li>Add numbers and special characters</li>
                    <li>Avoid using personal information</li>
                    <li>Don't reuse old passwords</li>
                </ul>
            </div>
            
            <p>If you're having trouble with the password reset process, please contact our IT support team.</p>
        </div>
        
        <div class="footer">
            <p><strong>Jetlouge Travels</strong> - Staff Portal Security</p>
            <p>This is an automated message. Please do not reply to this email.</p>
            <p style="font-size: 12px; margin-top: 15px;">
                If you didn't request this password reset, please contact IT support immediately.
            </p>
        </div>
    </div>
</body>
</html>
