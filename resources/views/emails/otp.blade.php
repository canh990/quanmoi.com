<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mã xác thực đăng ký</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <div style="text-align: center; margin-bottom: 20px;">
            <h1 style="color: #a04100; margin: 0;">Quán Mới</h1>
        </div>
        
        <h2 style="color: #333333; font-size: 20px; text-align: center;">Xác thực tài khoản của bạn</h2>
        
        <p style="color: #666666; line-height: 1.6;">Xin chào,</p>
        <p style="color: #666666; line-height: 1.6;">Bạn vừa yêu cầu đăng ký tài khoản tại <strong>Quán Mới</strong>. Vui lòng sử dụng mã xác thực gồm 6 chữ số dưới đây để hoàn tất quá trình đăng ký:</p>
        
        <div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px; text-align: center; margin: 25px 0;">
            <span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #a04100;">{{ $otp }}</span>
        </div>
        
        <p style="color: #666666; line-height: 1.6; font-size: 14px;">Mã này sẽ hết hạn trong vòng 3 phút.</p>
        <p style="color: #666666; line-height: 1.6; font-size: 14px;">Nếu bạn không yêu cầu đăng ký tài khoản, vui lòng bỏ qua email này.</p>
        
        <hr style="border: none; border-top: 1px solid #eeeeee; margin: 30px 0;">
        
        <p style="color: #999999; font-size: 12px; text-align: center;">&copy; {{ date('Y') }} Quán Mới. Tất cả các quyền được bảo lưu.</p>
    </div>
</body>
</html>
