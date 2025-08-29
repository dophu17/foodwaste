# 🚀 Gemini AI Integration Setup Guide

## Overview
Hệ thống đã được tích hợp với Google Gemini AI để cung cấp các tính năng thông minh:
- **Dự báo nhu cầu** - Dự đoán số lượng khách hàng và món ăn
- **Phân tích lãng phí** - Đưa ra khuyến nghị giảm thiểu lãng phí thực phẩm
- **Tối ưu hóa thực đơn** - Khuyến nghị cải thiện thực đơn và giá cả
- **Insights AI** - Phân tích toàn diện dữ liệu nhà hàng

## 🔧 Setup Requirements

### 1. Environment Variables
Thêm vào file `.env`:
```env
GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_MODEL=gemini-pro
GEMINI_TEMPERATURE=0.7
GEMINI_MAX_TOKENS=2048
```

### 2. Get Gemini API Key
1. Truy cập [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Tạo API key mới
3. Copy và paste vào `GEMINI_API_KEY`

### 3. Install Dependencies
```bash
composer install
php artisan migrate
php artisan db:seed
```

## 🎯 Features Available

### AI Insights Dashboard
- **Route**: `/ai-insights`
- **Controller**: `DashboardController@aiInsights`
- **View**: `resources/views/ai/insights.blade.php`

### AI Analysis Dashboard
- **Route**: `/ai-analysis`
- **Controller**: `AIAnalysisController@index`
- **View**: `resources/views/ai/analysis.blade.php`

### Gemini AI API Endpoints
- **Test Connection**: `GET /gemini-ai/test-connection`
- **Demand Forecast**: `POST /gemini-ai/demand-forecast`
- **Waste Insights**: `POST /gemini-ai/waste-insights`
- **Menu Optimization**: `POST /gemini-ai/menu-optimization`
- **Comprehensive Analysis**: `GET /gemini-ai/comprehensive-analysis`

## 🍽️ Food Items AI Integration

### New Features Added
1. **AI Insights Button** - Nút chính để mở modal AI insights
2. **Test AI Connection** - Kiểm tra kết nối với Gemini AI
3. **Generate AI Insights** - Tạo insights cho tất cả món ăn
4. **Individual AI Insights** - Insights cho từng món ăn cụ thể
5. **AI Waste Prediction Display** - Hiển thị dự đoán lãng phí AI

### AI Waste Prediction
- Hiển thị tỷ lệ lãng phí dự đoán bởi AI
- Dựa trên dữ liệu lịch sử và patterns
- Cập nhật tự động khi có dữ liệu mới

## 🔍 How It Works

### 1. Data Collection
- Thu thập dữ liệu bán hàng (14-30 ngày gần nhất)
- Thu thập dữ liệu lãng phí thực phẩm
- Thu thập thông tin thực đơn và giá cả

### 2. AI Analysis
- Gửi dữ liệu đến Gemini AI API
- AI phân tích patterns và xu hướng
- Trả về insights và khuyến nghị

### 3. Results Display
- Hiển thị insights trong modal
- Cập nhật dự đoán lãng phí
- Cung cấp khuyến nghị hành động

## 📊 AI Analysis Types

### Demand Forecasting
- **Input**: Dữ liệu bán hàng, thông tin nhà hàng
- **Output**: Dự báo khách hàng, nhu cầu món ăn, khuyến nghị nguyên liệu
- **Use Case**: Lập kế hoạch chuẩn bị, quản lý inventory

### Waste Insights
- **Input**: Dữ liệu lãng phí, bán hàng, thông tin nhà hàng
- **Output**: Phân tích nguyên nhân, khuyến nghị giảm lãng phí
- **Use Case**: Tối ưu hóa quy trình, tiết kiệm chi phí

### Menu Optimization
- **Input**: Dữ liệu thực đơn, bán hàng, lãng phí
- **Output**: Khuyến nghị thay đổi thực đơn, tối ưu giá cả
- **Use Case**: Cải thiện lợi nhuận, tăng hiệu suất

## 🚀 Usage Examples

### 1. Generate AI Insights for All Food Items
```javascript
// Click "Generate AI Insights" button
// Or call function directly
generateAIInsights();
```

### 2. Get AI Insights for Specific Food Item
```javascript
// Click "AI Insights" in dropdown menu
// Or call function directly
getAIInsights(foodItemId);
```

### 3. Test AI Connection
```javascript
// Click "Test AI Connection" button
// Or call function directly
checkAIConnection();
```

## 🔧 Customization

### Modify AI Prompts
Edit `app/Services/GeminiAIService.php`:
```php
protected function buildForecastPrompt($restaurantData, $salesData, $forecastPeriod)
{
    // Customize your AI prompt here
    return "Your custom prompt...";
}
```

### Add New AI Features
1. Add new method to `GeminiAIService`
2. Add route in `routes/web.php`
3. Add controller method in `GeminiAIController`
4. Update frontend JavaScript

### Modify AI Response Parsing
Edit parsing methods in `GeminiAIService`:
```php
protected function parseAIResponse($aiResponse)
{
    // Customize response parsing logic
}
```

## 🐛 Troubleshooting

### Common Issues

#### 1. API Key Error
```
Error: Invalid API key
```
**Solution**: Kiểm tra `GEMINI_API_KEY` trong `.env`

#### 2. Connection Timeout
```
Error: Connection timeout
```
**Solution**: Kiểm tra internet connection và API endpoint

#### 3. No Data Available
```
Message: Không có đủ dữ liệu để phân tích
```
**Solution**: Tạo thêm orders, waste records để có dữ liệu

#### 4. AI Response Parse Error
```
Error: Không thể phân tích phản hồi AI
```
**Solution**: Kiểm tra format response từ Gemini AI

### Debug Mode
Enable debug logging in `config/logging.php`:
```php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single'],
        'ignore_exceptions' => false,
    ],
    'single' => [
        'driver' => 'single',
        'path' => storage_path('logs/laravel.log'),
        'level' => 'debug',
    ],
],
```

## 📈 Performance Optimization

### Caching
- AI responses được cache trong 1 giờ
- Cache key: `gemini_{md5_hash_of_prompt}`
- Có thể điều chỉnh TTL trong `GeminiAIService`

### Rate Limiting
- Implement rate limiting cho API calls
- Sử dụng Laravel's built-in rate limiting

### Batch Processing
- Xử lý nhiều food items cùng lúc
- Sử dụng queues cho heavy AI operations

## 🔒 Security Considerations

### API Key Protection
- Không commit API key vào git
- Sử dụng environment variables
- Rotate API keys regularly

### Input Validation
- Validate tất cả input trước khi gửi đến AI
- Sanitize data để tránh prompt injection

### Output Sanitization
- Sanitize AI responses trước khi hiển thị
- Escape HTML và JavaScript

## 📚 Additional Resources

### Documentation
- [Google Gemini API Docs](https://ai.google.dev/docs)
- [Laravel Documentation](https://laravel.com/docs)
- [Bootstrap Documentation](https://getbootstrap.com/docs)

### Support
- Check Laravel logs: `storage/logs/laravel.log`
- Monitor Gemini API usage
- Review error messages in browser console

## 🎉 What's Next?

### Planned Features
- [ ] Real-time AI monitoring dashboard
- [ ] Automated waste prediction updates
- [ ] Integration with weather APIs
- [ ] Multi-language AI support
- [ ] Advanced analytics and reporting

### Contributing
1. Fork the repository
2. Create feature branch
3. Implement changes
4. Submit pull request

---

**Happy AI-powered restaurant management! 🍕🤖**
