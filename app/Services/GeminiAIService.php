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
     * Get current system language
     */
    protected function getCurrentLanguage()
    {
        $locale = app()->getLocale();
        switch ($locale) {
            case 'ja':
                return 'japanese';
            case 'vi':
                return 'vietnamese';
            default:
                return 'japanese';
        }
    }

    /**
     * Get language-specific prompts
     */
    protected function getLanguagePrompts()
    {
        $locale = app()->getLocale();
        
        switch ($locale) {
            case 'ja':
                return [
                    'forecast_intro' => 'あなたはレストランの需要予測の専門AIです。以下のデータを分析し、今後{period}日間の予測を提供してください：',
                    'restaurant_info' => '**レストラン情報:**',
                    'sales_data' => '**売上データ（過去14日間）:**',
                    'requirements' => '**要求事項:**',
                    'format_response' => '**回答形式（JSON）:**',
                    'waste_intro' => 'あなたは食品廃棄管理の専門AIです。以下のデータを分析し、推奨事項を提供してください：',
                    'waste_data' => '**廃棄データ:**',
                    'menu_intro' => 'あなたはレストランメニュー最適化の専門AIです。分析し、推奨事項を提供してください：',
                    'menu_data' => '**メニューデータ:**',
                    'test_prompt' => 'こんにちは！日本語で回答できますか？簡潔にお答えください。',
                    'success_message' => 'Gemini AI接続成功！',
                    'error_message' => 'Gemini AIに接続できません',
                    'connection_error' => '接続エラー: ',
                    'analysis_success' => 'AIがデータを正常に分析しました',
                    'view_details' => 'AI応答で詳細を確認',
                    'parse_error' => 'AI応答を解析できません'
                ];
            case 'vi':
                return [
                    'forecast_intro' => 'Bạn là một chuyên gia AI về dự báo nhu cầu nhà hàng. Hãy phân tích dữ liệu sau và đưa ra dự báo cho {period} ngày tới:',
                    'restaurant_info' => '**THÔNG TIN NHÀ HÀNG:**',
                    'sales_data' => '**DỮ LIỆU BÁN HÀNG (14 ngày gần nhất):**',
                    'requirements' => '**YÊU CẦU:**',
                    'format_response' => '**ĐỊNH DẠNG TRẢ LỜI (JSON):**',
                    'waste_intro' => 'Bạn là chuyên gia AI về quản lý lãng phí thực phẩm. Hãy phân tích dữ liệu sau và đưa ra khuyến nghị:',
                    'waste_data' => '**DỮ LIỆU LÃNG PHÍ:**',
                    'menu_intro' => 'Bạn là chuyên gia AI về tối ưu hóa thực đơn nhà hàng. Hãy phân tích và đưa ra khuyến nghị:',
                    'menu_data' => '**DỮ LIỆU THỰC ĐƠN:**',
                    'test_prompt' => 'Xin chào! Bạn có thể trả lời bằng tiếng Việt không? Hãy trả lời ngắn gọn.',
                    'success_message' => 'Kết nối Gemini AI thành công!',
                    'error_message' => 'Không thể kết nối với Gemini AI',
                    'connection_error' => 'Lỗi kết nối: ',
                    'analysis_success' => 'AI đã phân tích dữ liệu thành công',
                    'view_details' => 'Xem chi tiết trong phản hồi AI',
                    'parse_error' => 'Không thể phân tích phản hồi AI'
                ];
            default:
                return [
                    'forecast_intro' => 'You are an AI expert in restaurant demand forecasting. Please analyze the following data and provide forecasts for the next {period} days:',
                    'restaurant_info' => '**RESTAURANT INFORMATION:**',
                    'sales_data' => '**SALES DATA (Last 14 days):**',
                    'requirements' => '**REQUIREMENTS:**',
                    'format_response' => '**RESPONSE FORMAT (JSON):**',
                    'waste_intro' => 'You are an AI expert in food waste management. Please analyze the following data and provide recommendations:',
                    'waste_data' => '**WASTE DATA:**',
                    'menu_intro' => 'You are an AI expert in restaurant menu optimization. Please analyze and provide recommendations:',
                    'menu_data' => '**MENU DATA:**',
                    'test_prompt' => 'Hello! Can you respond in English? Please answer briefly.',
                    'success_message' => 'Gemini AI connection successful!',
                    'error_message' => 'Cannot connect to Gemini AI',
                    'connection_error' => 'Connection error: ',
                    'analysis_success' => 'AI has successfully analyzed the data',
                    'view_details' => 'See details in AI response',
                    'parse_error' => 'Cannot parse AI response'
                ];
        }
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
        $prompts = $this->getLanguagePrompts();
        $language = $this->getCurrentLanguage();
        
        // Add language instruction to the prompt
        $languageInstruction = "Please respond in {$language}. ";
        
        $prompt = $languageInstruction . str_replace('{period}', $forecastPeriod, $prompts['forecast_intro']) . "

{$prompts['restaurant_info']}
- " . ($language === 'japanese' ? '名前' : ($language === 'vietnamese' ? 'Tên' : 'Name')) . ": {$restaurantData['name']}
- " . ($language === 'japanese' ? '料理の種類' : ($language === 'vietnamese' ? 'Loại ẩm thực' : 'Cuisine Type')) . ": {$restaurantData['cuisine_type']}
- " . ($language === 'japanese' ? '収容人数' : ($language === 'vietnamese' ? 'Sức chứa' : 'Capacity')) . ": {$restaurantData['capacity']} " . ($language === 'japanese' ? '席' : ($language === 'vietnamese' ? 'chỗ' : 'seats')) . "

{$prompts['sales_data']}
" . $this->formatSalesData($salesData) . "

{$prompts['requirements']}
1. " . ($language === 'japanese' ? '売上傾向の分析' : ($language === 'vietnamese' ? 'Phân tích xu hướng bán hàng' : 'Analyze sales trends')) . "
2. " . ($language === 'japanese' ? '今後{period}日間の顧客数予測' : ($language === 'vietnamese' ? 'Dự báo số lượng khách hàng cho {period} ngày tới' : 'Forecast customer count for next {period} days')) . "
3. " . ($language === 'japanese' ? '各料理の需要予測' : ($language === 'vietnamese' ? 'Dự báo nhu cầu từng món ăn' : 'Forecast demand for each dish')) . "
4. " . ($language === 'japanese' ? '食材準備の推奨' : ($language === 'vietnamese' ? 'Khuyến nghị chuẩn bị nguyên liệu' : 'Recommend ingredient preparation')) . "
5. " . ($language === 'japanese' ? '予測の信頼性' : ($language === 'vietnamese' ? 'Độ tin cậy của dự báo' : 'Forecast confidence level')) . "

{$prompts['format_response']}
{
    \"analysis\": \"" . ($language === 'japanese' ? '傾向分析' : ($language === 'vietnamese' ? 'Phân tích xu hướng' : 'Trend analysis')) . "\",
    \"customer_forecast\": [\"" . ($language === 'japanese' ? '日別顧客数' : ($language === 'vietnamese' ? 'số khách mỗi ngày' : 'daily customer count')) . "\"],
    \"food_forecast\": [\"" . ($language === 'japanese' ? '各料理の予測' : ($language === 'vietnamese' ? 'dự báo từng món' : 'forecast for each dish')) . "\"],
    \"ingredient_recommendations\": [\"" . ($language === 'japanese' ? '食材推奨' : ($language === 'vietnamese' ? 'khuyến nghị nguyên liệu' : 'ingredient recommendations')) . "\"],
    \"confidence_level\": \"" . ($language === 'japanese' ? '信頼性' : ($language === 'vietnamese' ? 'Độ tin cậy' : 'Confidence level')) . "\",
    \"key_factors\": [\"" . ($language === 'japanese' ? '影響要因' : ($language === 'vietnamese' ? 'yếu tố ảnh hưởng' : 'key factors')) . "\"]
}";

        return str_replace('{period}', $forecastPeriod, $prompt);
    }

    /**
     * Build waste insights prompt
     */
    protected function buildWasteInsightsPrompt($wasteData, $salesData, $restaurantInfo)
    {
        $prompts = $this->getLanguagePrompts();
        $language = $this->getCurrentLanguage();
        
        // Add language instruction to the prompt
        $languageInstruction = "Please respond in {$language}. ";
        
        $prompt = $languageInstruction . $prompts['waste_intro'] . "

{$prompts['restaurant_info']}
- " . ($language === 'japanese' ? '名前' : ($language === 'vietnamese' ? 'Tên' : 'Name')) . ": {$restaurantInfo['name']}
- " . ($language === 'japanese' ? '料理の種類' : ($language === 'vietnamese' ? 'Loại ẩm thực' : 'Cuisine Type')) . ": {$restaurantInfo['cuisine_type']}

{$prompts['waste_data']}
" . $this->formatWasteData($wasteData) . "

{$prompts['sales_data']}
" . $this->formatSalesData($salesData) . "

{$prompts['requirements']}
1. " . ($language === 'japanese' ? '廃棄の原因分析' : ($language === 'vietnamese' ? 'Phân tích nguyên nhân lãng phí' : 'Analyze waste causes')) . "
2. " . ($language === 'japanese' ? '廃棄削減の推奨' : ($language === 'vietnamese' ? 'Khuyến nghị giảm thiểu lãng phí' : 'Recommend waste reduction')) . "
3. " . ($language === 'japanese' ? '準備プロセスの最適化' : ($language === 'vietnamese' ? 'Tối ưu hóa quy trình chuẩn bị' : 'Optimize preparation process')) . "
4. " . ($language === 'japanese' ? '将来の廃棄予測' : ($language === 'vietnamese' ? 'Dự báo lãng phí trong tương lai' : 'Future waste prediction')) . "

{$prompts['format_response']}
{
    \"waste_analysis\": \"" . ($language === 'japanese' ? '廃棄分析' : ($language === 'vietnamese' ? 'Phân tích lãng phí' : 'Waste analysis')) . "\",
    \"recommendations\": [\"" . ($language === 'japanese' ? '廃棄削減推奨' : ($language === 'vietnamese' ? 'khuyến nghị giảm lãng phí' : 'waste reduction recommendations')) . "\"],
    \"process_optimization\": [\"" . ($language === 'japanese' ? 'プロセス最適化' : ($language === 'vietnamese' ? 'tối ưu quy trình' : 'process optimization')) . "\"],
    \"future_waste_prediction\": \"" . ($language === 'japanese' ? '将来の廃棄予測' : ($language === 'vietnamese' ? 'dự báo lãng phí' : 'future waste prediction')) . "\",
    \"cost_savings\": \"" . ($language === 'japanese' ? 'コスト削減' : ($language === 'vietnamese' ? 'tiết kiệm chi phí' : 'cost savings')) . "\"
}";

        return $prompt;
    }

    /**
     * Build menu optimization prompt
     */
    protected function buildMenuOptimizationPrompt($menuData, $salesData, $wasteData)
    {
        $prompts = $this->getLanguagePrompts();
        $language = $this->getCurrentLanguage();
        
        // Add language instruction to the prompt
        $languageInstruction = "Please respond in {$language}. ";
        
        $prompt = $languageInstruction . $prompts['menu_intro'] . "

{$prompts['menu_data']}
" . $this->formatMenuData($menuData) . "

{$prompts['sales_data']}
" . $this->formatSalesData($salesData) . "

{$prompts['waste_data']}
" . $this->formatWasteData($wasteData) . "

{$prompts['requirements']}
1. " . ($language === 'japanese' ? '各料理のパフォーマンス分析' : ($language === 'vietnamese' ? 'Phân tích hiệu suất từng món ăn' : 'Analyze performance of each dish')) . "
2. " . ($language === 'japanese' ? 'メニュー変更の推奨' : ($language === 'vietnamese' ? 'Khuyến nghị thay đổi thực đơn' : 'Recommend menu changes')) . "
3. " . ($language === 'japanese' ? '価格最適化' : ($language === 'vietnamese' ? 'Tối ưu hóa giá cả' : 'Optimize pricing')) . "
4. " . ($language === 'japanese' ? '廃棄率の改善' : ($language === 'vietnamese' ? 'Cải thiện tỷ lệ lãng phí' : 'Improve waste ratio')) . "

{$prompts['format_response']}
{
    \"menu_performance\": \"" . ($language === 'japanese' ? 'メニューパフォーマンス分析' : ($language === 'vietnamese' ? 'Phân tích hiệu suất thực đơn' : 'Menu performance analysis')) . "\",
    \"menu_changes\": [\"" . ($language === 'japanese' ? 'メニュー変更推奨' : ($language === 'vietnamese' ? 'khuyến nghị thay đổi' : 'menu change recommendations')) . "\"],
    \"pricing_optimization\": [\"" . ($language === 'japanese' ? '価格最適化' : ($language === 'vietnamese' ? 'tối ưu giá cả' : 'pricing optimization')) . "\"],
    \"waste_reduction\": [\"" . ($language === 'japanese' ? '廃棄削減' : ($language === 'vietnamese' ? 'giảm lãng phí' : 'waste reduction')) . "\"],
    \"profit_improvement\": \"" . ($language === 'japanese' ? '利益改善' : ($language === 'vietnamese' ? 'cải thiện lợi nhuận' : 'profit improvement')) . "\"
}";

        return $prompt;
    }

    /**
     * Format sales data for prompt
     */
    protected function formatSalesData($salesData)
    {
        $formatted = '';
        $language = $this->getCurrentLanguage();
        
        // Language-specific labels
        $itemsSold = $language === 'japanese' ? '販売された料理' : ($language === 'vietnamese' ? 'món bán được' : 'items sold');
        $orders = $language === 'japanese' ? '注文' : ($language === 'vietnamese' ? 'đơn hàng' : 'orders');
        $customers = $language === 'japanese' ? '顧客' : ($language === 'vietnamese' ? 'khách' : 'customers');
        
        // Handle sales data from WasteRecordController (array of daily sales)
        if (is_array($salesData) && count($salesData) > 0 && isset($salesData[0]['date'])) {
            foreach ($salesData as $data) {
                $formatted .= "- {$data['date']}: {$data['total_sold']} {$itemsSold}\n";
            }
            return $formatted;
        }
        
        // Handle sales data from DashboardController (key-value format)
        foreach ($salesData as $day => $data) {
            if (is_array($data) && isset($data['orders'])) {
                $currency = $language === 'japanese' ? '¥' : ($language === 'vietnamese' ? 'VNĐ' : '$');
                $formatted .= "- {$day}: {$data['orders']} {$orders}, {$data['revenue']} {$currency}, {$data['customers']} {$customers}\n";
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
        $language = $this->getCurrentLanguage();
        
        // Language-specific labels
        $reason = $language === 'japanese' ? '理由' : ($language === 'vietnamese' ? 'Lý do' : 'Reason');
        $date = $language === 'japanese' ? '日付' : ($language === 'vietnamese' ? 'Ngày' : 'Date');
        $price = $language === 'japanese' ? '価格' : ($language === 'vietnamese' ? 'Giá' : 'Price');
        $prepTime = $language === 'japanese' ? '準備時間' : ($language === 'vietnamese' ? 'Thời gian chuẩn bị' : 'Preparation time');
        $minutes = $language === 'japanese' ? '分' : ($language === 'vietnamese' ? 'phút' : 'minutes');
        
        $currency = $language === 'japanese' ? '¥' : ($language === 'vietnamese' ? 'VNĐ' : '$');
        
        // Handle single waste record (from WasteRecordController)
        if (isset($wasteData['food_item'])) {
            $formatted .= "- {$wasteData['food_item']}: {$wasteData['quantity_wasted']} {$wasteData['waste_unit']}, {$wasteData['cost_wasted']} {$currency}\n";
            $formatted .= "  {$reason}: {$wasteData['waste_reason']}\n";
            $formatted .= "  {$date}: {$wasteData['waste_date']}\n";
            $formatted .= "  {$price}: {$wasteData['price']} {$currency}\n";
            if (isset($wasteData['preparation_time'])) {
                $formatted .= "  {$prepTime}: {$wasteData['preparation_time']} {$minutes}\n";
            }
            return $formatted;
        }
        
        // Handle multiple waste records (from DashboardController)
        foreach ($wasteData as $category => $data) {
            if (is_array($data) && isset($data['quantity'])) {
                $formatted .= "- {$category}: {$data['quantity']} {$data['unit']}, {$data['cost']} {$currency}\n";
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
        $language = $this->getCurrentLanguage();
        
        // Language-specific labels
        $stock = $language === 'japanese' ? '在庫' : ($language === 'vietnamese' ? 'Tồn kho' : 'Stock');
        
        $currency = $language === 'japanese' ? '¥' : ($language === 'vietnamese' ? 'VNĐ' : '$');
        
        foreach ($menuData as $item) {
            $formatted .= "- {$item['name']}: {$item['price']}{$currency}, {$item['category']}, {$stock}: {$item['stock']}\n";
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
            $prompts = $this->getLanguagePrompts();
            return [
                'raw_response' => $aiResponse,
                'analysis' => $prompts['analysis_success'],
                'recommendations' => [$prompts['view_details']],
                'confidence_level' => 'Medium'
            ];
        } catch (\Exception $e) {
            Log::error('AI Response Parse Error: ' . $e->getMessage());
            $prompts = $this->getLanguagePrompts();
            return [
                'error' => $prompts['parse_error'],
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
            $prompts = $this->getLanguagePrompts();
            $language = $this->getCurrentLanguage();
            
            // Add language instruction to the prompt
            $languageInstruction = "Please respond in {$language}. ";
            $prompt = $languageInstruction . $prompts['test_prompt'];
            
            $response = $this->callGeminiAPI($prompt);
            
            if ($response && isset($response['candidates'][0]['content']['parts'][0]['text'])) {
                return [
                    'success' => true,
                    'message' => $prompts['success_message'],
                    'response' => $response['candidates'][0]['content']['parts'][0]['text']
                ];
            }
            
            return [
                'success' => false,
                'message' => $prompts['error_message']
            ];
        } catch (\Exception $e) {
            $prompts = $this->getLanguagePrompts();
            return [
                'success' => false,
                'message' => $prompts['connection_error'] . $e->getMessage()
            ];
        }
    }
}

