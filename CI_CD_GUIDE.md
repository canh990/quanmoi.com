# Hướng Dẫn Thiết Lập CI/CD Cho quanmoi.com (GitHub Actions)

Dự án đã được tích hợp sẵn 2 luồng tự động hóa trong `.github/workflows/`:
1. **`ci.yml`**: Tự động build frontend Vite, cài đặt Composer, kiểm tra code style (Pint) và chạy Migration + Test mỗi khi có code mới đẩy lên `master` hoặc `main`.
2. **`deploy.yml`**: Tự động SSH vào máy chủ VPS, kéo code mới, cập nhật Docker container và tối ưu cache.

---

## 1. Cấu hình biến bí mật (Secrets) trên GitHub

Truy cập vào GitHub:
👉 **`https://github.com/canh990/quanmoi.com`** → **Settings** → **Secrets and variables** → **Actions** → Bấm **"New repository secret"**.

Thêm các Secret sau:

| Tên Secret | Ý nghĩa | Ví dụ |
| :--- | :--- | :--- |
| `SSH_HOST` | Địa chỉ IP hoặc tên miền của máy chủ VPS | `103.153.72.10` |
| `SSH_USER` | Tên tài khoản SSH trên server | `root` hoặc `ubuntu` |
| `SSH_KEY` | Nội dung Private Key để SSH vào server (xem cách tạo bên dưới) | `-----BEGIN OPENSSH PRIVATE KEY----- ...` |
| `SSH_PORT` | Cổng SSH của server (thường là 22) | `22` |
| `DEPLOY_PATH` | Đường dẫn thư mục chứa dự án trên server | `/var/www/quanmoi.com` |

---

## 2. Cách tạo SSH Key trên VPS để kết nối GitHub Actions

Trên cửa sổ dòng lệnh (Terminal) của máy chủ VPS:

```bash
# 1. Tạo cặp key riêng cho việc deploy
ssh-keygen -t ed25519 -C "github-actions-deploy" -f ~/.ssh/github_deploy -N ""

# 2. Cho phép key này đăng nhập vào VPS
cat ~/.ssh/github_deploy.pub >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys

# 3. Lấy nội dung Private Key để copy vào Secret SSH_KEY trên GitHub
cat ~/.ssh/github_deploy
```

> **Lưu ý**: Copy toàn bộ nội dung hiển thị ra (bao gồm cả dòng `-----BEGIN OPENSSH PRIVATE KEY-----` và `-----END OPENSSH PRIVATE KEY-----`) dán vào secret `SSH_KEY`.

---

## 3. Cách kích hoạt và kiểm tra

- Khi bạn commit và push code lên GitHub:
  ```bash
  git add .
  git commit -m "feat: setup CI/CD workflows"
  git push origin master
  ```
- Mở tab **Actions** trên GitHub repository để xem tiến trình chạy:
  - **Laravel CI**: Kiểm tra build và test tự động.
  - **Deploy Production**: Tự động triển khai lên VPS khi merge vào branch chính. Bạn cũng có thể bấm nút **"Run workflow"** thủ công trên giao diện GitHub bất cứ lúc nào.
