<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đặt lại mật khẩu — Quán Mới</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 520px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #a04100 0%, #c95200 100%);
            padding: 32px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 22px;
            font-weight: 800;
            margin: 0 0 4px 0;
        }
        .header p {
            color: rgba(255,255,255,0.8);
            font-size: 13px;
            margin: 0;
        }
        .body {
            padding: 32px;
        }
        .greeting {
            font-size: 16px;
            color: #1a1a1a;
            font-weight: 600;
            margin: 0 0 8px 0;
        }
        .text {
            font-size: 14px;
            color: #555;
            line-height: 1.6;
            margin: 0 0 24px 0;
        }
        .btn {
            display: block;
            background: linear-gradient(135deg, #a04100, #c95200);
            color: #ffffff;
            text-decoration: none;
            text-align: center;
            padding: 15px 32px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            margin: 0 0 24px 0;
        }
        .warning {
            background: #fff8f2;
            border-left: 3px solid #a04100;
            border-radius: 8px;
            padding: 12px 16px;
            margin: 0 0 24px 0;
        }
        .warning p {
            font-size: 12px;
            color: #666;
            margin: 0;
        }
        .link-text {
            font-size: 12px;
            color: #888;
            word-break: break-all;
        }
        .link-text a {
            color: #a04100;
        }
        .footer {
            background: #f9f9f9;
            border-top: 1px solid #eee;
            padding: 20px 32px;
            text-align: center;
        }
        .footer p {
            font-size: 12px;
            color: #999;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍜 Quán Mới</h1>
            <p>Cộng đồng ẩm thực Việt Nam</p>
        </div>
        <div class="body">
            <p class="greeting">Xin chào {{ $user->ho_ten }},</p>
            <p class="text">
                Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản <strong>{{ $user->email }}</strong>
                trên Quán Mới. Nhấn vào nút bên dưới để đặt mật khẩu mới:
            </p>

            <a href="{{ $resetUrl }}" class="btn">🔐 Đặt lại mật khẩu ngay</a>

            <div class="warning">
                <p>⏱️ Link này chỉ có hiệu lực trong <strong>60 phút</strong>. Sau đó, bạn sẽ cần yêu cầu lại.</p>
            </div>

            <p class="text">Nếu bạn không yêu cầu đặt lại mật khẩu, hãy bỏ qua email này. Tài khoản của bạn vẫn an toàn.</p>

            <p class="link-text">Nếu nút không hoạt động, copy link sau vào trình duyệt:<br />
                <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
            </p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Quán Mới • Email này được gửi tự động, vui lòng không reply.</p>
        </div>
    </div>
</body>
</html>
