# Chạy QuanMoi bằng Docker Desktop trên Windows

Docker Compose tạo các dịch vụ riêng cho Laravel, MySQL và Elasticsearch. MySQL hiện tại của WAMP được giữ nguyên; dữ liệu Docker nằm trong volume riêng `quanmoi_db_data`.

## Chuyển dữ liệu từ WAMP

1. Trong phpMyAdmin của WAMP, chọn database `quanmoi` và xuất toàn bộ cấu trúc cùng dữ liệu thành file SQL. Giữ lại một bản sao lưu.
2. Mở Docker Desktop, vào thư mục dự án trong PowerShell rồi chạy:

   ```powershell
   docker compose up -d --build
   ```

3. Mở phpMyAdmin của Docker tại [http://localhost:8082](http://localhost:8082), chọn database `quanmoi`, vào **Import** và nạp file SQL đã xuất từ WAMP.
4. Truy cập ứng dụng tại [http://localhost:8002](http://localhost:8002). Nếu chưa nhập dữ liệu WAMP, website vẫn mở nhưng chưa có dữ liệu quán/người dùng cũ.

Nếu dự án dùng schema cũ hơn code hiện tại, chạy migration bổ sung trong Docker:

```powershell
docker compose exec app php artisan migrate --force
```

Với database Docker mới hoàn toàn, dùng lệnh sau để chạy migrations trực tiếp thay vì tải schema dump bằng MySQL client:

```powershell
docker compose exec app php artisan migrate --force --schema-path=/tmp/skip-schema-dump.sql
```

Không chạy `db:seed` khi đã nhập dữ liệu WAMP, vì seeder có thể tạo lại dữ liệu mẫu. Chỉ chạy seeder khi khởi tạo database trống và bạn chủ động muốn dữ liệu mẫu.

phpMyAdmin WAMP vẫn dùng cổng cũ. Docker phpMyAdmin dùng cổng 8082; MySQL Docker mở cổng 3307 để tránh cổng MySQL WAMP 3306.

## Lệnh thường dùng

```powershell
docker compose ps
docker compose logs -f app
docker compose down
```

`down` dừng dịch vụ nhưng giữ dữ liệu. Không dùng `docker compose down -v` nếu muốn giữ database và chỉ mục Elasticsearch trong Docker.

Các cổng có thể đổi bằng biến môi trường trong `.env`: `APP_PORT`, `DB_PUBLISHED_PORT`, `PHPMYADMIN_PORT`, `ELASTIC_PORT`. Cấu hình trong Compose tự trỏ Laravel vào MySQL/Elasticsearch theo tên dịch vụ Docker, không cần sửa `.env` WAMP.
