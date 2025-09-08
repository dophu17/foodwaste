# 🤖 Hướng Dẫn Tính Năng AI - Hệ Thống Quản Lý Lãng Phí Thực Phẩm

## 📋 Tổng Quan

Hệ thống tích hợp Google Gemini AI để cung cấp các phân tích thông minh và dự đoán lãng phí thực phẩm. AI được tích hợp sâu vào các trang chính của hệ thống để hỗ trợ quyết định quản lý nhà hàng.

## 🎯 Các Trang Có Tích Hợp AI

### 1. 📊 Dashboard - Phân Tích AI Theo Ngày

**Đường dẫn**: `/dashboard`

#### Tính Năng AI Hiện Có:
- **Phân tích theo ngày**: Chọn ngày cụ thể để xem phân tích AI
- **Dự báo nhu cầu**: Dự đoán số lượng khách hàng và món ăn
- **Thống kê lãng phí**: Phân tích mô hình lãng phí theo ngày
- **Khuyến nghị tối ưu**: Gợi ý cải thiện dựa trên dữ liệu ngày

#### Cách Sử Dụng:
1. Truy cập trang Dashboard
2. Chọn ngày cần phân tích từ date picker
3. Xem các thông tin AI được cập nhật theo ngày đã chọn
4. Nhận khuyến nghị cụ thể cho ngày đó

---

### 2. 🏪 Restaurant - Thông Tin Chi Tiết AI Nhà Hàng

**Đường dẫn**: `/restaurant/{id}`

#### Tính Năng AI Hiện Có:
- **Phân tích hiệu suất nhà hàng**: Đánh giá tổng thể hoạt động
- **Dự báo doanh thu**: Dự đoán doanh thu dựa trên xu hướng
- **Phân tích khách hàng**: Insights về hành vi khách hàng
- **Khuyến nghị cải thiện**: Gợi ý tối ưu hóa hoạt động

#### Cách Sử Dụng:
1. Truy cập trang chi tiết nhà hàng
2. Xem phần "Thông tin AI" trong trang
3. Nhận các phân tích chi tiết về nhà hàng
4. Áp dụng các khuyến nghị được đề xuất

---

### 3. 🍽️ Food Item Detail - Dự Đoán Lãng Phí AI

**Đường dẫn**: `/food-item/{id}`

#### Tính Năng AI Hiện Có:
- **Dự đoán lãng phí**: Phần trăm lãng phí dự kiến cho món ăn
- **Phân tích xu hướng**: Xu hướng bán hàng và lãng phí
- **Khuyến nghị giá**: Gợi ý điều chỉnh giá dựa trên AI
- **Tối ưu hóa món ăn**: Cải thiện công thức và cách chế biến

#### Cách Sử Dụng:
1. Truy cập trang chi tiết món ăn
2. Xem phần "Dự đoán lãng phí AI" 
3. Nhận khuyến nghị cụ thể cho món ăn
4. Cập nhật thông tin dựa trên gợi ý AI

---

### 4. 📋 Waste Records Detail - Phân Tích AI

**Đường dẫn**: `/waste-records/{id}`

#### Tính Năng AI Hiện Có:
- **Phân tích nguyên nhân lãng phí**: AI phân tích lý do lãng phí
- **Dự đoán độ chính xác**: So sánh dự đoán với thực tế
- **Khuyến nghị giảm lãng phí**: Gợi ý cụ thể để giảm lãng phí
- **Phân tích mô hình**: Tìm hiểu patterns lãng phí

#### Cách Sử Dụng:
1. Truy cập trang chi tiết bản ghi lãng phí
2. Xem phần "Phân tích AI" 
3. Nhận insights về nguyên nhân lãng phí
4. Áp dụng các khuyến nghị để cải thiện

---

## 📊 Yếu Tố Cần Thiết Cho Phân Tích AI

### ✅ Các Yếu Tố Đã Có

#### Dữ Liệu Cơ Bản:
- [x] **Thông tin nhà hàng**: Tên, địa chỉ, loại hình ẩm thực
- [x] **Dữ liệu món ăn**: Tên, giá, danh mục, thời gian chế biến
- [x] **Hồ sơ đơn hàng**: Ngày, số lượng, tổng tiền
- [x] **Hồ sơ lãng phí**: Ngày, số lượng, lý do, chi phí
- [x] **Dữ liệu thời tiết**: Điều kiện thời tiết khi bán hàng
- [x] **Sự kiện đặc biệt**: Ghi nhận các sự kiện ảnh hưởng

#### Dữ Liệu Lịch Sử:
- [x] **Dữ liệu bán hàng 14-30 ngày**: Để phân tích xu hướng
- [x] **Lịch sử lãng phí**: Để dự đoán patterns
- [x] **Dữ liệu theo ngày trong tuần**: Phân tích theo thứ
- [x] **Dữ liệu theo mùa**: Xu hướng theo thời gian

#### Cấu Hình AI:
- [x] **Google Gemini API**: Đã tích hợp và cấu hình
- [x] **Model AI**: gemini-1.5-flash
- [x] **Temperature**: 0.7 (cân bằng sáng tạo và chính xác)
- [x] **Max Tokens**: 2048

### ❌ Các Yếu Tố Chưa Có (Cần Bổ Sung)

#### Dữ Liệu Bổ Sung:
- [ ] **Dữ liệu khách hàng**: Tuổi, giới tính, sở thích
- [ ] **Dữ liệu nguyên liệu**: Chi phí, thời hạn sử dụng
- [ ] **Dữ liệu nhân viên**: Số lượng, kinh nghiệm, ca làm việc
- [ ] **Dữ liệu cạnh tranh**: Giá cả đối thủ, xu hướng thị trường
- [ ] **Dữ liệu mùa vụ**: Lễ hội, sự kiện địa phương
- [ ] **Dữ liệu kinh tế**: Chỉ số kinh tế, lạm phát

#### Dữ Liệu Thời Gian Thực:
- [ ] **API thời tiết**: Dữ liệu thời tiết chính xác
- [ ] **API giao thông**: Tình trạng giao thông ảnh hưởng
- [ ] **API sự kiện**: Sự kiện địa phương tự động
- [ ] **Dữ liệu mạng xã hội**: Sentiment, trends

#### Cải Thiện AI:
- [ ] **Machine Learning Models**: Model riêng cho từng nhà hàng
- [ ] **Real-time Learning**: Học từ dữ liệu mới liên tục
- [ ] **Ensemble Methods**: Kết hợp nhiều model AI
- [ ] **Custom Prompts**: Prompts tùy chỉnh cho từng loại nhà hàng

---

## 🔧 Cấu Hình AI Nâng Cao

### Environment Variables Cần Thiết:
```env
# Google Gemini AI
GEMINI_API_KEY=your_api_key_here
GEMINI_MODEL=gemini-1.5-flash
GEMINI_TEMPERATURE=0.7
GEMINI_MAX_TOKENS=2048

# AI Analysis Settings
AI_ANALYSIS_DAYS=14
AI_PREDICTION_ACCURACY_THRESHOLD=0.8
AI_UPDATE_FREQUENCY=daily
```

### API Endpoints AI:
- `GET /dashboard/ai/test-connection` - Kiểm tra kết nối AI
- `GET /dashboard/ai/forecasting` - Dự báo nhu cầu
- `GET /dashboard/ai/waste-insights` - Phân tích lãng phí
- `GET /dashboard/ai/menu-optimization` - Tối ưu hóa thực đơn
- `POST /food-item/{id}/calculate-ai` - Tính toán AI cho món ăn

---

## 📈 Kế Hoạch Phát Triển AI

### Giai Đoạn 1 (Hiện Tại):
- ✅ Tích hợp Google Gemini AI cơ bản
- ✅ Phân tích theo ngày trên Dashboard
- ✅ Dự đoán lãng phí cho món ăn
- ✅ Phân tích AI cho hồ sơ lãng phí

### Giai Đoạn 2 (Sắp Tới):
- [ ] Tích hợp API thời tiết thời gian thực
- [ ] Machine Learning models tùy chỉnh
- [ ] Phân tích sentiment từ đánh giá khách hàng
- [ ] Dự báo nhu cầu theo giờ trong ngày

### Giai Đoạn 3 (Tương Lai):
- [ ] Computer Vision cho phân tích hình ảnh món ăn
- [ ] IoT sensors cho theo dõi nhiệt độ, độ ẩm
- [ ] Blockchain cho truy xuất nguồn gốc thực phẩm
- [ ] AR/VR cho training nhân viên

---

## 🚀 Hướng Dẫn Sử Dụng AI

### 1. Kiểm Tra Kết Nối AI:
```bash
# Truy cập endpoint kiểm tra
GET /dashboard/ai/test-connection
```

### 2. Chạy Phân Tích AI:
```bash
# Phân tích toàn diện
GET /gemini-ai/comprehensive-analysis

# Dự báo nhu cầu
POST /gemini-ai/demand-forecast
```

### 3. Cập Nhật Dự Đoán AI:
```bash
# Cập nhật dự đoán cho món ăn
POST /food-item/{id}/calculate-ai
```

---

## 📞 Hỗ Trợ AI

Nếu gặp vấn đề với tính năng AI:
1. Kiểm tra kết nối API: `/dashboard/ai/test-connection`
2. Xem logs: `storage/logs/laravel.log`
3. Kiểm tra cấu hình: `.env` file
4. Tạo issue trên GitHub với tag `ai-feature`

---

**🤖 AI được thiết kế để giúp nhà hàng giảm lãng phí và tăng hiệu quả kinh doanh!**
