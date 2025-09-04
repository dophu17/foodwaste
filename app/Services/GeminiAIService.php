<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAIService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model', 'gemini-1.5-flash');
        $this->baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
    }

    /**
     * Generate AI insights for restaurant demand forecasting
     */
    public function generateDemandForecast($restaurantData, $salesData, $forecastPeriod = 7)
    {
        try {
            $prompt = $this->buildForecastPrompt($restaurantData, $salesData, $forecastPeriod);
            
            $response = $this->callGeminiAPI($prompt);
            
            if ($response && isset($response['candidates'][0]['content']['parts'][0]['text'])) {
                $aiResponse = $response['candidates'][0]['content']['parts'][0]['text'];
                return $this->parseAIResponse($aiResponse);
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Gemini AI Forecast Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate AI insights for waste management
     */
    public function generateWasteInsights($wasteData, $salesData, $restaurantInfo)
    {
        try {
            $prompt = $this->buildWasteInsightsPrompt($wasteData, $salesData, $restaurantInfo);
            
            $response = $this->callGeminiAPI($prompt);
            
            if ($response && isset($response['candidates'][0]['content']['parts'][0]['text'])) {
                $aiResponse = $response['candidates'][0]['content']['parts'][0]['text'];
                return $this->parseWasteInsightsResponse($aiResponse);
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Gemini AI Waste Insights Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate menu optimization recommendations
     */
    public function generateMenuOptimization($menuData, $salesData, $wasteData)
    {
        try {
            $prompt = $this->buildMenuOptimizationPrompt($menuData, $salesData, $wasteData);
            
            $response = $this->callGeminiAPI($prompt);
            
            if ($response && isset($response['candidates'][0]['content']['parts'][0]['text'])) {
                $aiResponse = $response['candidates'][0]['content']['parts'][0]['text'];
                return $this->parseMenuOptimizationResponse($aiResponse);
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Gemini AI Menu Optimization Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Call Gemini AI API
     */
    public function callGeminiAPI($prompt)
    {

        $requestData = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ]
            ]
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '?key=' . $this->apiKey, $requestData);

        if ($response->successful()) {
            $result = $response->json();
            return $result;
        }

        Log::error('Gemini API Error: ' . $response->body());
        return null;
    }

    /**
     * Build forecast prompt for demand prediction
     */
    protected function buildForecastPrompt($restaurantData, $salesData, $forecastPeriod)
    {
        return "Bạn là một chuyên gia AI về dự báo nhu cầu nhà hàng. Hãy phân tích dữ liệu sau và đưa ra dự báo cho {$forecastPeriod} ngày tới:

**THÔNG TIN NHÀ HÀNG:**
- Tên: {$restaurantData['name']}
- Loại ẩm thực: {$restaurantData['cuisine_type']}
- Sức chứa: {$restaurantData['capacity']} chỗ

**DỮ LIỆU BÁN HÀNG (14 ngày gần nhất):**
" . $this->formatSalesData($salesData) . "

**YÊU CẦU:**
1. Phân tích xu hướng bán hàng
2. Dự báo số lượng khách hàng cho {$forecastPeriod} ngày tới
3. Dự báo nhu cầu từng món ăn
4. Khuyến nghị chuẩn bị nguyên liệu
5. Độ tin cậy của dự báo

**ĐỊNH DẠNG TRẢ LỜI (JSON):**
{
    \"analysis\": \"Phân tích xu hướng\",
    \"customer_forecast\": [\"số khách mỗi ngày\"],
    \"food_forecast\": [\"dự báo từng món\"],
    \"ingredient_recommendations\": [\"khuyến nghị nguyên liệu\"],
    \"confidence_level\": \"Độ tin cậy\",
    \"key_factors\": [\"yếu tố ảnh hưởng\"]
}";
    }

    /**
     * Build waste insights prompt
     */
    protected function buildWasteInsightsPrompt($wasteData, $salesData, $restaurantInfo)
    {
        return "Bạn là chuyên gia AI về quản lý lãng phí thực phẩm. Hãy phân tích dữ liệu sau và đưa ra khuyến nghị:

**THÔNG TIN NHÀ HÀNG:**
- Tên: {$restaurantInfo['name']}
- Loại ẩm thực: {$restaurantInfo['cuisine_type']}

**DỮ LIỆU LÃNG PHÍ:**
" . $this->formatWasteData($wasteData) . "

**DỮ LIỆU BÁN HÀNG:**
" . $this->formatSalesData($salesData) . "

**YÊU CẦU:**
1. Phân tích nguyên nhân lãng phí
2. Khuyến nghị giảm thiểu lãng phí
3. Tối ưu hóa quy trình chuẩn bị
4. Dự báo lãng phí trong tương lai

**ĐỊNH DẠNG TRẢ LỜI (JSON):**
{
    \"waste_analysis\": \"Phân tích lãng phí\",
    \"recommendations\": [\"khuyến nghị giảm lãng phí\"],
    \"process_optimization\": [\"tối ưu quy trình\"],
    \"future_waste_prediction\": \"dự báo lãng phí\",
    \"cost_savings\": \"tiết kiệm chi phí\"
}";
    }

    /**
     * Build menu optimization prompt
     */
    protected function buildMenuOptimizationPrompt($menuData, $salesData, $wasteData)
    {
        return "Bạn là chuyên gia AI về tối ưu hóa thực đơn nhà hàng. Hãy phân tích và đưa ra khuyến nghị:

**DỮ LIỆU THỰC ĐƠN:**
" . $this->formatMenuData($menuData) . "

**DỮ LIỆU BÁN HÀNG:**
" . $this->formatSalesData($salesData) . "

**DỮ LIỆU LÃNG PHÍ:**
" . $this->formatWasteData($wasteData) . "

**YÊU CẦU:**
1. Phân tích hiệu suất từng món ăn
2. Khuyến nghị thay đổi thực đơn
3. Tối ưu hóa giá cả
4. Cải thiện tỷ lệ lãng phí

**ĐỊNH DẠNG TRẢ LỜI (JSON):**
{
    \"menu_performance\": \"Phân tích hiệu suất thực đơn\",
    \"menu_changes\": [\"khuyến nghị thay đổi\"],
    \"pricing_optimization\": [\"tối ưu giá cả\"],
    \"waste_reduction\": [\"giảm lãng phí\"],
    \"profit_improvement\": \"cải thiện lợi nhuận\"
}";
    }

    /**
     * Format sales data for prompt
     */
    protected function formatSalesData($salesData)
    {
        $formatted = '';
        
        // Handle sales data from WasteRecordController (array of daily sales)
        if (is_array($salesData) && count($salesData) > 0 && isset($salesData[0]['date'])) {
            foreach ($salesData as $data) {
                $formatted .= "- {$data['date']}: {$data['total_sold']} món bán được\n";
            }
            return $formatted;
        }
        
        // Handle sales data from DashboardController (key-value format)
        foreach ($salesData as $day => $data) {
            if (is_array($data) && isset($data['orders'])) {
                $formatted .= "- {$day}: {$data['orders']} đơn hàng, {$data['revenue']} VNĐ, {$data['customers']} khách\n";
            } else {
                // Handle simple format
                $formatted .= "- {$day}: " . json_encode($data) . "\n";
            }
        }
        return $formatted;
    }

    /**
     * Format waste data for prompt
     */
    protected function formatWasteData($wasteData)
    {
        $formatted = '';
        
        // Handle single waste record (from WasteRecordController)
        if (isset($wasteData['food_item'])) {
            $formatted .= "- {$wasteData['food_item']}: {$wasteData['quantity_wasted']} {$wasteData['waste_unit']}, {$wasteData['cost_wasted']} VNĐ\n";
            $formatted .= "  Lý do: {$wasteData['waste_reason']}\n";
            $formatted .= "  Ngày: {$wasteData['waste_date']}\n";
            $formatted .= "  Giá: {$wasteData['price']} VNĐ\n";
            if (isset($wasteData['preparation_time'])) {
                $formatted .= "  Thời gian chuẩn bị: {$wasteData['preparation_time']} phút\n";
            }
            return $formatted;
        }
        
        // Handle multiple waste records (from DashboardController)
        foreach ($wasteData as $category => $data) {
            if (is_array($data) && isset($data['quantity'])) {
                $formatted .= "- {$category}: {$data['quantity']} {$data['unit']}, {$data['cost']} VNĐ\n";
            } else {
                // Handle simple array format
                $formatted .= "- {$category}: " . json_encode($data) . "\n";
            }
        }
        return $formatted;
    }

    /**
     * Format menu data for prompt
     */
    protected function formatMenuData($menuData)
    {
        $formatted = '';
        foreach ($menuData as $item) {
            $formatted .= "- {$item['name']}: {$item['price']}¥, {$item['category']}, Stock: {$item['stock']}\n";
        }
        return $formatted;
    }

    /**
     * Parse AI response for demand forecast
     */
    protected function parseAIResponse($aiResponse)
    {
        try {
            // Try to extract JSON from response
            $jsonStart = strpos($aiResponse, '{');
            $jsonEnd = strrpos($aiResponse, '}');
            
            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonString = substr($aiResponse, $jsonStart, $jsonEnd - $jsonStart + 1);
                $parsed = json_decode($jsonString, true);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $parsed;
                }
            }
            
            // Fallback: return structured text response
            return [
                'raw_response' => $aiResponse,
                'analysis' => 'AI đã phân tích dữ liệu thành công',
                'recommendations' => ['Xem chi tiết trong phản hồi AI'],
                'confidence_level' => 'Medium'
            ];
        } catch (\Exception $e) {
            Log::error('AI Response Parse Error: ' . $e->getMessage());
            return [
                'error' => 'Không thể phân tích phản hồi AI',
                'raw_response' => $aiResponse
            ];
        }
    }

    /**
     * Parse waste insights response
     */
    protected function parseWasteInsightsResponse($aiResponse)
    {
        return $this->parseAIResponse($aiResponse);
    }

    /**
     * Parse menu optimization response
     */
    protected function parseMenuOptimizationResponse($aiResponse)
    {
        return $this->parseAIResponse($aiResponse);
    }

    /**
     * Test Gemini AI connection
     */
    public function testConnection()
    {
        try {
            $prompt = "Xin chào! Bạn có thể trả lời bằng tiếng Việt không? Hãy trả lời ngắn gọn.";
            
            $response = $this->callGeminiAPI($prompt);
            
            if ($response && isset($response['candidates'][0]['content']['parts'][0]['text'])) {
                return [
                    'success' => true,
                    'message' => 'Kết nối Gemini AI thành công!',
                    'response' => $response['candidates'][0]['content']['parts'][0]['text']
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Không thể kết nối với Gemini AI'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi kết nối: ' . $e->getMessage()
            ];
        }
    }
}

