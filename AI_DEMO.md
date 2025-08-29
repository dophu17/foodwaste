# 🤖 AI Features Demo Guide

## 🎯 Demo Overview
Hướng dẫn test các tính năng AI mới được tích hợp vào hệ thống quản lý nhà hàng.

## 🚀 Quick Start

### 1. Setup Environment
```bash
# Copy .env.example to .env
cp .env.example .env

# Add Gemini AI API key
echo "GEMINI_API_KEY=your_api_key_here" >> .env

# Install dependencies
composer install

# Setup database
php artisan migrate
php artisan db:seed

# Start server
php artisan serve
```

### 2. Login & Navigate
1. Truy cập: `http://localhost:8000`
2. Đăng ký/đăng nhập tài khoản
3. Tạo thông tin nhà hàng
4. Tạo menu và food items
5. Tạo một số orders và waste records

## 🍽️ Food Items AI Demo

### Test AI Connection
1. Vào trang Food Items: `/food-item`
2. Click nút **"Test AI Connection"**
3. **Expected Result**: Alert hiển thị "Kết nối Gemini AI thành công!"

### Generate AI Insights
1. Click nút **"AI Insights"** (màu xanh)
2. Trong modal, click **"Generate Insights"**
3. **Expected Result**: Modal hiển thị insights từ AI

### Individual Food Item AI
1. Trong dropdown menu của food item, click **"AI Insights"**
2. **Expected Result**: Modal hiển thị insights cho món ăn cụ thể

## 📊 AI Analysis Dashboard Demo

### Access AI Analysis
1. Vào: `/ai-analysis`
2. **Expected Result**: Dashboard hiển thị phân tích AI

### Test API Endpoints
```bash
# Test connection
curl -X GET "http://localhost:8000/gemini-ai/test-connection" \
  -H "Accept: application/json"

# Get comprehensive analysis
curl -X GET "http://localhost:8000/gemini-ai/comprehensive-analysis" \
  -H "Accept: application/json"

# Generate demand forecast
curl -X POST "http://localhost:8000/gemini-ai/demand-forecast" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"period": 7}'
```

## 🔍 Expected AI Responses

### Demand Forecast Response
```json
{
  "success": true,
  "message": "Dự báo nhu cầu đã được tạo thành công",
  "data": {
    "analysis": "Phân tích xu hướng bán hàng",
    "customer_forecast": ["số khách mỗi ngày"],
    "food_forecast": ["dự báo từng món"],
    "ingredient_recommendations": ["khuyến nghị nguyên liệu"],
    "confidence_level": "Độ tin cậy",
    "key_factors": ["yếu tố ảnh hưởng"]
  }
}
```

### Waste Insights Response
```json
{
  "success": true,
  "message": "Phân tích lãng phí đã được tạo thành công",
  "data": {
    "waste_analysis": "Phân tích lãng phí",
    "recommendations": ["khuyến nghị giảm lãng phí"],
    "process_optimization": ["tối ưu quy trình"],
    "future_waste_prediction": "dự báo lãng phí",
    "cost_savings": "tiết kiệm chi phí"
  }
}
```

### Menu Optimization Response
```json
{
  "success": true,
  "message": "Tối ưu hóa thực đơn đã được tạo thành công",
  "data": {
    "menu_performance": "Phân tích hiệu suất thực đơn",
    "menu_changes": ["khuyến nghị thay đổi"],
    "pricing_optimization": ["tối ưu giá cả"],
    "waste_reduction": ["giảm lãng phí"],
    "profit_improvement": "cải thiện lợi nhuận"
  }
}
```

## 🧪 Test Scenarios

### Scenario 1: No Data Available
1. Xóa tất cả orders và waste records
2. Click "Generate AI Insights"
3. **Expected Result**: Message "Không có đủ dữ liệu để phân tích"

### Scenario 2: API Key Invalid
1. Set `GEMINI_API_KEY=invalid_key` trong `.env`
2. Click "Test AI Connection"
3. **Expected Result**: Error message về API key

### Scenario 3: Network Error
1. Disconnect internet
2. Click "Generate AI Insights"
3. **Expected Result**: Connection error message

### Scenario 4: Successful AI Analysis
1. Có đủ dữ liệu (orders, waste records)
2. Valid API key
3. Stable internet connection
4. **Expected Result**: AI insights hiển thị thành công

## 📱 Frontend Features Test

### Modal Functionality
- [ ] AI Insights Modal mở/đóng đúng
- [ ] Individual Food Item Modal hoạt động
- [ ] Loading states hiển thị đúng
- [ ] Error handling hoạt động

### Button States
- [ ] Test AI Connection button
- [ ] Generate AI Insights button
- [ ] Individual AI Insights button
- [ ] AI Insights modal button

### Responsive Design
- [ ] Test trên mobile devices
- [ ] Test trên tablet
- [ ] Test trên desktop
- [ ] Modal responsive trên mọi kích thước

## 🔧 Debug & Troubleshooting

### Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

### Check Browser Console
1. Open Developer Tools (F12)
2. Check Console tab
3. Look for JavaScript errors
4. Check Network tab for API calls

### Common Issues & Solutions

#### Issue: "AI insights not loading"
**Solution**: 
1. Check browser console for errors
2. Verify API key in `.env`
3. Check Laravel logs
4. Verify routes are accessible

#### Issue: "Modal not opening"
**Solution**:
1. Check Bootstrap CSS/JS loaded
2. Verify modal HTML structure
3. Check JavaScript errors
4. Verify event handlers attached

#### Issue: "API calls failing"
**Solution**:
1. Check authentication middleware
2. Verify CSRF tokens
3. Check API endpoint URLs
4. Verify request headers

## 📈 Performance Testing

### Load Testing
```bash
# Test multiple concurrent AI requests
for i in {1..10}; do
  curl -X GET "http://localhost:8000/gemini-ai/test-connection" &
done
wait
```

### Cache Testing
1. Make first AI request (should call API)
2. Make identical request immediately (should use cache)
3. Wait 1 hour, make request again (should call API)

### Memory Usage
```bash
# Monitor memory usage during AI operations
php artisan tinker
# Run AI operations
# Check memory usage
```

## 🎯 Success Criteria

### Functional Requirements
- [ ] AI connection test works
- [ ] AI insights generation works
- [ ] Individual food item AI works
- [ ] Modals open/close correctly
- [ ] Error handling works
- [ ] Loading states work

### Performance Requirements
- [ ] AI responses within 5 seconds
- [ ] Cache working correctly
- [ ] No memory leaks
- [ ] Responsive UI

### User Experience
- [ ] Intuitive button placement
- [ ] Clear feedback messages
- [ ] Smooth animations
- [ ] Mobile-friendly design

## 🚀 Next Steps After Demo

### 1. Production Deployment
- Set production API key
- Configure production database
- Set up monitoring
- Configure logging

### 2. User Training
- Create user manual
- Conduct training sessions
- Gather feedback
- Iterate improvements

### 3. Feature Enhancement
- Add more AI analysis types
- Implement real-time updates
- Add export functionality
- Integrate with other systems

---

**Happy AI Testing! 🤖✨**
