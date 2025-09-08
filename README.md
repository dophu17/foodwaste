# 🍽️ Hệ Thống Quản Lý Lãng Phí Thực Phẩm

Ứng dụng web toàn diện được xây dựng bằng Laravel để quản lý lãng phí thực phẩm trong nhà hàng với các tính năng phân tích và thông tin thông minh được hỗ trợ bởi AI.

## 📋 Giới Thiệu

Dự án này được thiết kế để giúp các nhà hàng theo dõi, quản lý và giảm thiểu lãng phí thực phẩm thông qua một hệ thống web toàn diện được xây dựng bằng framework Laravel. Hệ thống tích hợp Google Gemini AI để cung cấp các thông tin thông minh, dự báo nhu cầu và khuyến nghị giảm thiểu lãng phí.

## ✨ Tính Năng Chính

### 🏪 Quản Lý Nhà Hàng
- Quản lý hồ sơ nhà hàng hoàn chỉnh
- Hỗ trợ đa nhà hàng
- Theo dõi giờ hoạt động và sức chứa
- Phân loại loại hình ẩm thực

### 📊 Quản Lý Thực Đơn & Món Ăn
- Tạo và quản lý thực đơn động
- Danh mục món ăn với thông tin chi tiết
- Theo dõi số lượng tồn kho và cảnh báo hết hàng
- Tổ chức theo danh mục
- Quản lý giá cả và thời gian chế biến
- Hỗ trợ tải lên hình ảnh món ăn

### 📈 Quản Lý Đơn Hàng
- Tạo và theo dõi đơn hàng
- Thống kê và phân tích đơn hàng
- Theo dõi điều kiện thời tiết
- Ghi nhận sự kiện đặc biệt
- Phân tích theo ngày trong tuần

### 🤖 Phân Tích Được Hỗ Trợ Bởi AI
- **Tích hợp Google Gemini AI** cho thông tin thông minh
- **Dự Báo Nhu Cầu** - Dự đoán nhu cầu khách hàng và yêu cầu thực phẩm
- **Phân Tích Lãng Phí** - Phân tích mô hình lãng phí và khuyến nghị được hỗ trợ bởi AI
- **Tối Ưu Hóa Thực Đơn** - Gợi ý thông minh để cải thiện thực đơn và giá cả
- **Thông Tin AI Thời Gian Thực** - Nhận khuyến nghị tức thì cho các món ăn
- **Dự Đoán Lãng Phí** - Dự đoán phần trăm lãng phí được hỗ trợ bởi AI

### 📊 Theo Dõi & Phân Tích Lãng Phí
- Quản lý hồ sơ lãng phí toàn diện
- Theo dõi và phân tích chi phí
- Phân loại lý do lãng phí
- Theo dõi độ chính xác dự đoán AI
- Phân tích mô hình lãng phí lịch sử
- Khuyến nghị giảm thiểu lãng phí

### 📧 Báo Cáo Tự Động
- **Tích hợp N8N Workflow** cho báo cáo hàng ngày tự động
- Thông báo email với tóm tắt lãng phí chi tiết
- Báo cáo nhà hàng cá nhân hóa
- Hỗ trợ đa ngôn ngữ (Tiếng Nhật, Tiếng Việt)

### 🌐 Đa Ngôn Ngữ
- Hỗ trợ đa ngôn ngữ (Tiếng Nhật, Tiếng Việt)
- Định dạng tiền tệ theo khu vực
- Chức năng chuyển đổi ngôn ngữ
- Thích ứng văn hóa cho các thị trường khác nhau

### 📱 Trải Nghiệm Người Dùng
- Giao diện web phản hồi
- Bảng điều khiển trực quan với thống kê thời gian thực
- Khả năng tìm kiếm và lọc nâng cao
- Kiểm soát truy cập dựa trên vai trò
- Thiết kế thân thiện với thiết bị di động

## 🛠️ Công Nghệ Sử Dụng

- **Backend**: Laravel 12.x (PHP Framework)
- **Database**: MySQL
- **Frontend**: Blade templates với Bootstrap CSS/JavaScript
- **Authentication**: Laravel UI với xác thực tùy chỉnh
- **AI Integration**: Google Gemini AI API
- **Workflow Automation**: N8N
- **Internationalization**: Laravel Localization
- **Currency Support**: Định dạng tiền tệ đa quốc gia
- **Email**: Tích hợp SMTP cho báo cáo tự động

## 📦 Dependencies

- **PHP**: ^8.2
- **Laravel Framework**: ^12.0
- **Laravel UI**: ^4.6
- **Laravel Tinker**: ^2.10.1
- **Faker**: ^1.23 (Development)
- **Laravel Pail**: ^1.2.2 (Development)
- **Laravel Pint**: ^1.13 (Code Style)
- **Laravel Sail**: ^1.41 (Development)
- **Mockery**: ^1.6 (Testing)
- **Nunomaduro Collision**: ^8.6 (Error Handling)
- **PHPUnit**: ^11.5.3 (Testing)

## ⚙️ Yêu Cầu Hệ Thống

- PHP >= 8.2
- Composer
- MySQL 5.7+ hoặc 8.0+
- Web server (Apache/Nginx)
- Google Gemini AI API Key (cho tính năng AI)
- Cấu hình SMTP (cho báo cáo email)

## 🚀 Cài Đặt

### 1. Clone Repository
```bash
git clone https://github.com/git-balocco/ai-food-waste-manager.git
cd foodwaste
```

### 2. Cài Đặt Dependencies
```bash
composer install
```

### 3. Cấu Hình Environment
```bash
cp .env.example .env
```

### 4. Cấu Hình Biến Môi Trường
Chỉnh sửa file `.env` với các cấu hình sau:

#### Cấu Hình Database
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=foodwaste
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### Cấu Hình Google Gemini AI
```env
GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_MODEL=gemini-1.5-flash
GEMINI_TEMPERATURE=0.7
GEMINI_MAX_TOKENS=2048
```

#### Cấu Hình Email (cho báo cáo tự động)
```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@example.com
MAIL_FROM_NAME="Food Waste Management"
```

### 5. Tạo Application Key
```bash
php artisan key:generate
```

### 6. Thiết Lập Database
```bash
php artisan migrate
php artisan db:seed
```

### 7. Khởi Động Development Server
```bash
php artisan serve
```

Ứng dụng sẽ có sẵn tại `http://localhost:8000`

## 🤖 Thiết Lập AI

### Tích Hợp Google Gemini AI

1. **Lấy API Key**:
   - Truy cập [Google AI Studio](https://makersuite.google.com/app/apikey)
   - Tạo API key mới
   - Thêm vào file `.env` của bạn với tên `GEMINI_API_KEY`

2. **Kiểm Tra Kết Nối AI**:
   - Truy cập `/dashboard/ai/test-connection` sau khi đăng nhập
   - Hoặc sử dụng API endpoint: `GET /gemini-ai/test-connection`

3. **Tính Năng AI Có Sẵn**:
   - **Dự Báo Nhu Cầu**: `/dashboard/ai/forecasting`
   - **Thông Tin Lãng Phí**: `/dashboard/ai/waste-insights`
   - **Tối Ưu Hóa Thực Đơn**: `/dashboard/ai/menu-optimization`
   - **Phân Tích Toàn Diện**: `/gemini-ai/comprehensive-analysis`

## 📧 Thiết Lập N8N Workflow

Hệ thống bao gồm workflow báo cáo hàng ngày tự động sử dụng N8N:

1. **Import Workflow**: Sử dụng file `n8n-report-to-restaurant-workflow.json` được cung cấp
2. **Cấu Hình Kết Nối Database**: Cập nhật thông tin đăng nhập MySQL trong workflow
3. **Cấu Hình Email**: Cập nhật cài đặt SMTP cho thông báo email
4. **Lịch Trình**: Workflow chạy hàng ngày lúc 5 PM để gửi báo cáo lãng phí

## 🌐 Hỗ Trợ Đa Ngôn Ngữ

Hệ thống hỗ trợ nhiều ngôn ngữ:
- **Tiếng Nhật** (ja) - Mặc định
- **Tiếng Việt** (vi)

### Chuyển Đổi Ngôn Ngữ
Người dùng có thể chuyển đổi ngôn ngữ bằng cách sử dụng bộ chuyển đổi ngôn ngữ tại `/language/{locale}`

## 📊 Cấu Trúc Database

### Bảng Chính
- `users` - Xác thực người dùng và hồ sơ
- `restaurants` - Thông tin và cài đặt nhà hàng
- `menus` - Quản lý thực đơn và phân loại
- `food_items` - Danh mục món ăn với dự đoán AI
- `orders` - Quản lý và theo dõi đơn hàng
- `order_items` - Các mục đơn hàng riêng lẻ
- `waste_records` - Theo dõi lãng phí với thông tin AI

### Tính Năng Chính
- **Dự Đoán Lãng Phí AI**: Tính toán tự động phần trăm lãng phí
- **Hỗ Trợ Đa Ngôn Ngữ**: Lưu trữ nội dung theo khu vực
- **Audit Trails**: Theo dõi toàn diện các thay đổi
- **Soft Deletes**: Bảo tồn và khôi phục dữ liệu

## 🎯 Hướng Dẫn Sử Dụng

### Bắt Đầu
1. **Đăng Ký/Đăng Nhập**: Tạo tài khoản hoặc đăng nhập vào hệ thống
2. **Tạo Nhà Hàng**: Thiết lập hồ sơ nhà hàng với thông tin cơ bản
3. **Thêm Thực Đơn**: Tạo thực đơn và phân loại món ăn
4. **Thêm Món Ăn**: Thêm món ăn với thông tin chi tiết bao gồm giá cả và thời gian chế biến
5. **Theo Dõi Đơn Hàng**: Ghi lại đơn hàng hàng ngày và thông tin khách hàng
6. **Ghi Nhận Lãng Phí**: Đăng nhập lãng phí thực phẩm với lý do và chi phí
7. **Xem Phân Tích**: Truy cập thông tin và khuyến nghị được hỗ trợ bởi AI

### Sử Dụng Tính Năng AI
- **Bảng Điều Khiển**: Truy cập thông tin AI từ bảng điều khiển chính
- **Món Ăn**: Nhận khuyến nghị AI riêng cho từng món ăn
- **Phân Tích Lãng Phí**: Xem phân tích mô hình lãng phí được hỗ trợ bởi AI
- **Tối Ưu Hóa Thực Đơn**: Nhận gợi ý cải thiện thực đơn

### API Endpoints
Hệ thống cung cấp RESTful API endpoints cho:
- Quản lý nhà hàng
- Thao tác thực đơn và món ăn
- Theo dõi đơn hàng
- Quản lý hồ sơ lãng phí
- Phân tích và thông tin AI

## 🧪 Kiểm Thử

Chạy bộ test:
```bash
php artisan test
```

Chạy kiểm tra code style:
```bash
./vendor/bin/pint
```

## 📈 Hiệu Suất

- **Tối Ưu Database**: Truy vấn hiệu quả với indexing phù hợp
- **Caching**: Laravel caching để cải thiện hiệu suất
- **AI Response Caching**: Cache phản hồi AI để giảm API calls
- **Tối Ưu Hình Ảnh**: Xử lý hình ảnh tối ưu cho món ăn

## 🔒 Tính Năng Bảo Mật

- **Xác Thực**: Hệ thống xác thực người dùng an toàn
- **Phân Quyền**: Kiểm soát truy cập dựa trên vai trò
- **Xác Thực Dữ Liệu**: Xác thực đầu vào toàn diện
- **Bảo Vệ SQL Injection**: Eloquent ORM với parameter binding
- **Bảo Vệ XSS**: Blade template escaping
- **Bảo Vệ CSRF**: Laravel CSRF tokens

## 🚀 Triển Khai

### Môi Trường Production
1. Thiết lập web server production (Apache/Nginx)
2. Cấu hình SSL certificates
3. Thiết lập database với thông tin đăng nhập phù hợp
4. Cấu hình biến môi trường cho production
5. Chạy database migrations và seeders
6. Thiết lập backup tự động
7. Cấu hình N8N workflow cho production

### Hỗ Trợ Docker
Dự án bao gồm Laravel Sail cho phát triển containerized:
```bash
./vendor/bin/sail up
```

## 📞 Hỗ Trợ

Để được hỗ trợ và câu hỏi:
- Tạo issue trên GitHub
- Kiểm tra tài liệu trong thư mục `docs/`
- Xem lại tài liệu API

## 🤝 Đóng Góp

Chúng tôi hoan nghênh các đóng góp! Vui lòng làm theo các bước sau:

1. Fork repository
2. Tạo feature branch (`git checkout -b feature/amazing-feature`)
3. Commit thay đổi (`git commit -m 'Add some amazing feature'`)
4. Push lên branch (`git push origin feature/amazing-feature`)
5. Mở Pull Request

### Hướng Dẫn Phát Triển
- Tuân theo PSR-12 coding standards
- Viết test cho tính năng mới
- Cập nhật tài liệu khi cần thiết
- Đảm bảo tất cả test đều pass trước khi submit

## 📄 Giấy Phép

Dự án này là phần mềm mã nguồn mở được cấp phép theo [MIT license](https://opensource.org/licenses/MIT).

## 🙏 Lời Cảm Ơn

- Laravel Framework cho nền tảng PHP mạnh mẽ
- Google Gemini AI cho thông tin thông minh
- N8N cho tự động hóa workflow
- Bootstrap cho các component UI phản hồi
- Tất cả contributors và người dùng của dự án này

---

**Được tạo với ❤️ cho quản lý nhà hàng bền vững**
