---
description: Debug lỗi có hệ thống, tìm nguyên nhân gốc và sửa code an toàn cho người mới.
---

## Mục tiêu
Tìm nguyên nhân gốc của lỗi, sửa tối thiểu và giúp người dùng hiểu cách tự debug.

## Bước 1: Thu thập thông tin
- Đọc thông báo lỗi đầy đủ, stack trace và log liên quan.
- Xác định lỗi xảy ra khi nào, ở chức năng nào và có thể tái hiện không.
- Kiểm tra cấu trúc dự án, trạng thái Git và các file liên quan.
- Với Docker, kiểm tra tên service thực tế trước khi chạy lệnh.
- Không tự giả định cấu hình, phiên bản hoặc tên file.

## Bước 2: Phân tích nguyên nhân
- Truy vết luồng dữ liệu và các hàm liên quan.
- Phân biệt nguyên nhân đã xác minh với giả thuyết.
- Kiểm tra các nguyên nhân có khả năng cao trước.
- Không sửa nhiều vị trí cùng lúc khi chưa cần thiết.

## Bước 3: Giải thích cho người mới
Trình bày bằng tiếng Việt:
1. Lỗi có nghĩa là gì.
2. Nguyên nhân gốc hoặc những giả thuyết cần kiểm tra.
3. Vì sao lỗi xảy ra.
4. File và đoạn code có liên quan.
5. Cách sửa được đề xuất.

Dùng ví dụ ngắn nếu giúp người dùng hiểu rõ hơn.

## Bước 4: Đề xuất cách sửa
- Ưu tiên thay đổi nhỏ nhất có thể giải quyết lỗi.
- Giữ nguyên kiến trúc và chức năng không liên quan.
- Kiểm tra ảnh hưởng đến validation, authorization,
  database và các chức năng phụ thuộc.
- Trình bày kế hoạch trước khi sửa các thay đổi có rủi ro cao.
- Không tự ý thực hiện thay đổi lớn hoặc phá hủy dữ liệu.

## Bước 5: Thực hiện sửa lỗi
- Nếu người dùng yêu cầu phân tích בלבד, không chỉnh sửa file.
- Với thay đổi thông thường, chỉ sửa trong phạm vi nhiệm vụ.
- Không tự ý xóa file, reset database, chạy migrate:fresh,
  db:wipe, DROP TABLE hoặc xóa Docker volumes.
- Những hành động phá hủy dữ liệu phải được người dùng
  chấp thuận rõ ràng trước khi thực hiện.
- Không sửa bằng cách che giấu lỗi, bỏ qua kiểm tra bảo mật
  hoặc xóa test đang thất bại.

## Bước 6: Kiểm tra sau sửa
- Chạy test hoặc lệnh kiểm tra phù hợp với dự án.
- Kiểm tra cú pháp, log và các chức năng liên quan.
- Xem Git diff để phát hiện thay đổi ngoài ý muốn.
- Không tuyên bố đã sửa thành công nếu chưa có bằng chứng.
- Nếu không thể chạy test, ghi rõ phần chưa xác minh.

## Bước 7: Báo cáo kết quả
Tóm tắt:
- Nguyên nhân.
- Giải pháp.
- Các file đã thay đổi.
- Lệnh kiểm tra đã chạy và kết quả.
- Rủi ro hoặc phần còn chưa xác minh.
- Bài học giúp người dùng tự xử lý lỗi tương tự.

## Nguyên tắc
An toàn dữ liệu > sửa đúng nguyên nhân > thay đổi tối thiểu
> kiểm thử > giải thích dễ hiểu.