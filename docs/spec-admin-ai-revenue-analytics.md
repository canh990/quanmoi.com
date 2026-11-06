# TÀI LIỆU ĐẶC TẢ YÊU CẦU CHỨC NĂNG (SPECIFICATION 2)
## Phân Tích Doanh Thu Toàn Sàn & Gợi Ý Chiến Lược Tăng Trưởng Bằng AI
### Dành Riêng Cho Ban Quản Trị (ADMIN / PLATFORM OWNER)

* **Dự án:** QuanMoi.com
* **Phân hệ:** Quản trị toàn hệ thống (Admin Portal)
* **Phiên bản:** 1.0 (Admin Spec)
* **Trạng thái:** Sẵn sàng triển khai

---

## 1. TỔNG QUAN & MỤC TIÊU DÀNH CHO ADMIN

### 1.1. Bối cảnh & Thách thức của Admin Sàn QuanMoi
* Hệ thống QuanMoi vận hành với nguồn thu từ:
  1. Giao dịch nạp tiền tự động qua cổng SePay / Chuyển khoản ngân hàng.
  2. Phí mua các dịch vụ nâng cao của chủ quán: Ghim quán nổi bật (Hot badge), Phí xác minh tích xanh (Verified badge), Phí mở tính năng AI Promotion, Video review TikTok.
* **Vấn đề của Admin:**
  * Chỉ nhìn thấy các con số giao dịch khô khan từng ngày, chưa có bức tranh tổng thể về sức khỏe tài chính của sàn.
  * Không nắm được nhóm chủ quán nào đang mang lại doanh thu cao nhất (VIP Merchants) và nhóm nào sắp bỏ cuộc (Churn risk).
  * Chưa có công cụ dự báo doanh thu tháng tới để lập ngân sách marketing.

### 1.2. Mục tiêu chức năng (Objectives)
* **Giám sát dòng tiền thời gian thực (Real-time Cashflow):** Quản lý dòng tiền vào (Inflow qua SePay) và nghĩa vụ nợ số dư ví (`User.so_du`).
* **AI Cố vấn Tài chính & Tăng trưởng ảo (Virtual CFO & Growth AI):** Phân tích xu hướng nạp tiền, tự động tìm ra khu vực/ngành hàng có tiềm năng thu phí cao và đề xuất chiến dịch thúc đẩy chủ quán nạp tiền.
* **Phát hiện giao dịch bất thường (Anomaly & Risk Detection):** Cảnh báo khi có đột biến giao dịch thất bại, nghi vấn nạp tiền ảo hoặc rút ruột tài khoản.

---

## 2. THIẾT KẾ DỮ LIỆU BỔ SUNG (DATABASE SCHEMA)

Hệ thống tận dụng bảng `thanh_toan`, `users`, `quan` hiện có và bổ sung bảng lưu trữ phân tích của Admin:

### 2.1. Bảng `admin_ai_growth_insights` (Lưu khuyến nghị AI cho Admin)
| Cột | Kiểu | Mô tả |
| :--- | :--- | :--- |
| `id` | BIGINT (PK) | Khóa chính |
| `ky_phan_tich` | VARCHAR(20) | `daily`, `weekly`, `monthly` |
| `loai_insight` | ENUM | `forecast` (Dự báo), `churn_risk` (Cảnh báo rời bỏ), `revenue_boost` (Kích cầu), `anomaly` (Bất thường) |
| `tieu_de` | VARCHAR(255) | Tóm tắt nhận định chiến lược |
| `chi_so_lien_quan` | JSON | Dữ liệu gốc: MRR, tỷ lệ tăng trưởng %, top khu vực |
| `noi_dung_phan_tich` | TEXT | Đánh giá chuyên sâu từ góc độ quản trị sàn F&B |
| `de_xuat_hanh_dong` | JSON | Các chiến dịch Admin nên thực thi ngay |
| `muc_do_uu_tien` | ENUM | `thap`, `trung_binh`, `cao`, `khan_cap` |
| `created_at` | TIMESTAMP | Thời gian AI tạo |

---

## 3. ĐẶC TẢ CHỨC NĂNG CHI TIẾT (FUNCTIONAL REQUIREMENTS)

```
+-----------------------------------------------------------------------------------------+
|                        ADMIN: TỔNG QUAN TÀI CHÍNH TOÀN SÀN & AI                         |
+-----------------------------------------------------------------------------------------+
| [ Lọc: 30 ngày qua | Quý này | Năm nay | Tùy chọn ] [ Xuất Excel / Báo cáo PDF ]        |
|                                                                                         |
| [ Doanh thu SePay: 185.000.000đ ] [ Số dư ví sàn nợ: 42.000.000đ ] [ Chủ quán nạp: 312 ]|
|                                                                                         |
| +--------------------------------------+ +--------------------------------------------+ |
| | Biểu đồ MRR & Tăng trưởng doanh thu  | | Cơ cấu nguồn thu (Gói ghim / Tích xanh...) | |
| +--------------------------------------+ +--------------------------------------------+ |
|                                                                                         |
| +-------------------------------------------------------------------------------------+ |
| |  🤖 AI CHIẾN LƯỢC TĂNG TRƯỞNG & DỰ BÁO DOANH THU (AI CFO & GROWTH ENGINE)           | |
| |                                                                                     | |
| |  [Cảnh báo Rời Bỏ - Ưu tiên Cao]:                                                  | |
| |  -> Có 18 chủ quán VIP từng nạp > 2 triệu/tháng đã không phát sinh giao dịch 45 ngày | |
| |  -> AI gợi ý: Gửi Voucher "Tặng 20% giá trị nạp ví" để tái kích hoạt               | |
| |  [Nút: Gửi Chiến Dịch Email / Thông Báo 1-Click]                                    | |
| |                                                                                     | |
| |  [Cơ Hội Tăng Doanh Thu]:                                                           | |
| |  -> Quận Đống Đa có số lượng quán nướng tăng 35% nhưng tỷ lệ mua gói Ghim chỉ 8%    | |
| |  -> AI gợi ý: Mở gói Combo "Ghim Quán Nướng Đón Mùa Đông" giảm 15% trong 7 ngày    | |
| |  [Nút: Tạo Gói Khuyến Mãi Mới]                                                     | |
| +-------------------------------------------------------------------------------------+ |
|                                                                                         |
| [ Top Chủ Quán Nạp Tiền Nhiều Nhất ]    [ Bản đồ doanh thu theo Tỉnh / Quận Huyện ]     |
+-----------------------------------------------------------------------------------------+
```

### 3.1. Phân hệ 1: Dashboard Tài Chính Sàn (Financial Executive Dashboard - FR-01)
* **FR-01.1: Bộ chỉ số dòng tiền toàn sàn:**
  * **Tổng tiền thực thu (Net Inflow):** Tổng giá trị nạp tiền thành công qua SePay (`thanh_toan.trang_thai = 'thanh_cong'`).
  * **Tổng nghĩa vụ số dư ví (Total Wallet Liability):** Tổng số dư hiện tại của tất cả người dùng trong hệ thống (`SUM(users.so_du)`). Giúp Admin luôn biết sàn đang giữ bao nhiêu tiền của khách.
  * **Doanh thu định kỳ hàng tháng (MRR - Monthly Recurring Revenue):** Dự tính từ các gói ghim quán/hội viên gia hạn hàng tháng.
  * **ARPU (Doanh thu trung bình trên mỗi chủ quán trả phí):** `Tổng doanh thu / Số chủ quán có nạp tiền`.
* **FR-01.2: Phân tích cơ cấu nguồn thu (Revenue Breakdown):**
  * Tỷ trọng % theo dịch vụ: Nạp tiền ví, Mua gói ghim trang chủ, Mua tích xanh, Sử dụng gói AI.
  * Tỷ trọng % theo hình thức thanh toán: Chuyển khoản QR SePay, Thẻ tín dụng, Chuyển khoản thủ công.
* **FR-01.3: Doanh thu theo địa lý & Ngành hàng (Geo & Category Revenue):**
  * Xếp hạng tỉnh/thành phố và quận/huyện mang lại doanh thu nạp tiền cao nhất.
  * Xếp hạng ngành hàng (Lẩu nướng, Cà phê, Quán nhậu...) đóng góp doanh thu lớn nhất cho sàn.

---

### 3.2. Phân hệ 2: AI Cố Vấn Tăng Trưởng Toàn Sàn (AI Platform Growth Advisor - FR-02)

AI hoạt động dựa trên dữ liệu thống kê tổng hợp từ bảng `thanh_toan` và `users`:

#### A. Dự Báo Doanh Thu (Revenue Forecasting & Trend Analysis)
* **Cơ chế:** Phân tích tốc độ tăng trưởng chu kỳ 3-6 tháng gần nhất để dự báo doanh thu tháng tới.
* **Đầu ra của AI:**
  * *"Với đà tăng trưởng 12.5%/tháng hiện tại, dự kiến doanh thu nạp tiền tháng 11/2026 sẽ đạt từ 210.000.000đ - 225.000.000đ."*
  * Đưa ra khuyến nghị cần tập trung đẩy mạnh thêm bao nhiêu chủ quán mới để đạt mục tiêu KPI quý.

#### B. Cảnh Báo Nguy Cơ Rời Bỏ (Merchant Churn Warning)
* **Cơ chế:** Quét các chủ quán từng nạp tiền nhiều trong quá khứ nhưng không có giao dịch mới trong 30-60 ngày.
* **Đầu ra của AI:**
  * Phát hiện danh sách các chủ quán có nguy cơ ngừng sử dụng sàn.
  * AI đề xuất kịch bản chăm sóc: Gửi email nhắc nhở kèm ưu đãi tặng thêm credit khi nạp lại.

#### C. Khai Thác Thị Trường Tiềm Năng (Market Opportunity Spotting)
* **Cơ chế:** Đối chiếu giữa số lượng quán đăng ký tại một khu vực với tỷ lệ quán mua dịch vụ trả phí.
* **Đầu ra của AI:**
  * *"Khu vực Quận 1 (TP.HCM) có 240 quán cà phê nhưng chỉ 5% mua gói nổi bật. Đây là thị trường tiềm năng để chạy chiến dịch giảm giá gói hiển thị dành riêng cho quán cà phê."*

#### D. Giám Sát Rủi Ro & Bất Thường (Fraud & Anomaly Detection)
* **Cơ chế:** Quét các giao dịch nạp tiền thất bại liên tiếp hoặc chênh lệch bất thường giữa tiền SePay gửi về và số dư được cộng.
* **Đầu ra của AI:**
  * Cảnh báo ngay cho Admin các tài khoản có dấu hiệu cố tình spam webhook hoặc nạp tiền sai cú pháp để Admin kịp thời xử lý.

---

### 3.3. Phân hệ 3: Hành Động 1-Chạm Dành Cho Admin (FR-03)
* **Nút "Kích hoạt Chiến dịch Khuyến mãi Nạp tiền":**
  * Tự động gửi thông báo hệ thống (Notification) và Email tới toàn bộ chủ quán trong nhóm mục tiêu (VD: Tặng 10% khi nạp từ 200.000đ trong 3 ngày cuối tuần).
* **Nút "Tạo Gói Dịch Vụ Ưu Đãi Mới":**
  * Chuyển thẳng sang trang cấu hình gói ghim quán nổi bật với mức giá ưu đãi do AI tính toán sẵn.

---

## 4. QUY TRÌNH TÍCH HỢP & PROMPT CHO ADMIN AI

### 4.1. Dữ liệu đầu vào gửi sang AI (Input Context)
```json
{
  "ky_thong_ke": "Tháng 10/2026 so với Tháng 9/2026",
  "tong_doanh_thu_sepay": 185000000,
  "tang_truong_doanh_thu_pt": 14.2,
  "so_chu_quan_nap_tien": 312,
  "tong_so_du_vi_toan_san": 42500000,
  "co_cau_doanh_thu": {
    "goi_ghim_noi_bat": "52%",
    "phi_xac_thuc_tich_xanh": "28%",
    "goi_ai_marketing": "20%"
  },
  "top_khu_vuc_doanh_thu": ["Cầu Giấy - Hà Nội", "Quận 1 - TP.HCM", "Hải Châu - Đà Nẵng"],
  "so_chu_quan_khong_nap_lai_45_ngay": 18
}
```

### 4.2. Cấu trúc AI Trả về (Structured JSON Output)
```json
{
  "nhan_dinh_cfo": "Sức khỏe tài chính nền tảng rất tốt, tăng trưởng 14.2% nhờ nhu cầu ghim quán mùa cao điểm. Tuy nhiên nghĩa vụ số dư ví đang chiếm 23% tổng thu, cần lưu ý.",
  "du_bao_thang_toi": {
    "doanh_thu_uoc_tinh": "210.000.000đ - 225.000.000đ",
    "rui_ro_tiem_an": "Tốc độ chủ quán mới nạp tiền đang chậm lại ở khu vực miền Trung."
  },
  "khuyen_nghi_chien_luoc": [
    {
      "loai": "churn_prevention",
      "uu_tien": "cao",
      "tieu_de": "Tái kích hoạt 18 chủ quán VIP ngừng nạp tiền",
      "giai_phap": "Gửi email cá nhân hóa tặng voucher +15% giá trị nạp ví trong 48h.",
      "hanh_dong_admin": "gui_thong_bao_hang_loat"
    },
    {
      "loai": "package_promo",
      "uu_tien": "trung_binh",
      "tieu_de": "Ra mắt gói 'Combo Mùa Lễ Hội' cho ngành Lẩu Nướng",
      "giai_phap": "Ghép gói Ghim Trang Chủ + Tích Xanh giảm 20% trong tháng 11.",
      "hanh_dong_admin": "tao_goi_dich_vu"
    }
  ]
}
```

---

## 5. SO SÁNH: SPEC 1 (CHỦ QUÁN) vs SPEC 2 (ADMIN)

| Tiêu chí | Spec 1: Dành cho Chủ Quán | Spec 2: Dành cho Admin |
| :--- | :--- | :--- |
| **Góc nhìn** | Một quán ăn cụ thể | Toàn bộ nền tảng QuanMoi |
| **Nguồn tiền** | Doanh số bán đồ ăn, nước uống | Tiền nạp qua SePay, phí gói dịch vụ sàn |
| **Mục tiêu của AI** | Giúp quán kéo khách, lấp giờ vắng, bán combo | Giúp sàn tăng doanh thu, giảm chủ quán bỏ đi, phát hiện gian lận |
| **Hành động 1-click** | Tạo voucher đồ ăn, thêm combo vào menu | Gửi khuyến mãi nạp tiền cho chủ quán, tạo gói dịch vụ mới |

