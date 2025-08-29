<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Account Approved</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 10px;
        }
        .success-badge {
            background-color: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 30px;
        }
        .highlight {
            background-color: #e8f4f8;
            padding: 15px;
            border-left: 4px solid #2c5aa0;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background-color: #2c5aa0;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .vendor-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">
                <img src="{{ asset('assets/images/jetlouge_logo.png') }}" alt="JetLouge Travels" style="max-height: 60px; margin-bottom: 10px;">
            </div>
            <div class="success-badge">✅ ACCOUNT APPROVED</div>
        </div>

        <div class="content">
            <h2>Congratulations, {{ $vendor->name }}!</h2>
            
            <p>We're excited to inform you that your vendor account has been <strong>approved</strong> and is now active in our system.</p>

            <div class="vendor-info">
                <h3>Account Details:</h3>
                <ul>
                    <li><strong>Company:</strong> {{ $vendor->company_name }}</li>
                    <li><strong>Business Type:</strong> {{ $vendor->business_type }}</li>
                    <li><strong>Email:</strong> {{ $vendor->email }}</li>
                    <li><strong>Status:</strong> <span style="color: #28a745; font-weight: bold;">Active</span></li>
                </ul>
            </div>

            <div class="highlight">
                <h3>🎉 What's Next?</h3>
                <p>You can now access your vendor portal and start:</p>
                <ul>
                    <li>Viewing and bidding on opportunities</li>
                    <li>Managing your purchase orders</li>
                    <li>Tracking invoices and payments</li>
                    <li>Updating your profile and documents</li>
                </ul>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('vendor.login') }}" class="button">
                    🚀 Access Your Dashboard
                </a>
            </div>

            <div class="highlight">
                <h3>📋 Important Information:</h3>
                <ul>
                    <li>Your login credentials remain the same as when you registered</li>
                    <li>All uploaded documents have been verified and approved</li>
                    <li>You'll receive notifications about new bidding opportunities</li>
                    <li>Our team is available for support at any time</li>
                </ul>
            </div>

            <p>Thank you for choosing JetLouge Travels as your business partner. We look forward to a successful collaboration!</p>

            <p>Best regards,<br>
            <strong>The JetLouge Travels Team</strong><br>
            Procurement & Vendor Management</p>
        </div>

        <div class="footer">
            <p>This is an automated notification from JetLouge Travels Vendor Management System.</p>
            <p>If you have any questions, please contact our support team.</p>
            <p>&copy; {{ date('Y') }} JetLouge Travels. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
