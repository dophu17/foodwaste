<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format currency based on current locale
     *
     * @param float $amount
     * @param string|null $locale
     * @return string
     */
    public static function format($amount, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        switch ($locale) {
            case 'ja':
                return self::formatJapaneseYen($amount);
            case 'vi':
                return self::formatVietnameseDong($amount);
            default:
                return self::formatDefault($amount);
        }
    }
    
    /**
     * Format Japanese Yen (¥)
     *
     * @param float $amount
     * @return string
     */
    private static function formatJapaneseYen($amount)
    {
        // Japanese Yen doesn't use decimal places for whole numbers
        if ($amount == floor($amount)) {
            return '¥' . number_format($amount, 0);
        }
        
        return '¥' . number_format($amount, 0);
    }
    
    /**
     * Format Vietnamese Dong (₫)
     *
     * @param float $amount
     * @return string
     */
    private static function formatVietnameseDong($amount)
    {
        // Vietnamese Dong typically doesn't use decimal places
        return number_format($amount, 0, ',', '.') . ' ₫';
    }
    
    /**
     * Format default currency (USD)
     *
     * @param float $amount
     * @return string
     */
    private static function formatDefault($amount)
    {
        return '$' . number_format($amount, 2);
    }
    
    /**
     * Format currency with custom symbol
     *
     * @param float $amount
     * @param string $symbol
     * @param string|null $locale
     * @return string
     */
    public static function formatWithSymbol($amount, $symbol, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        switch ($locale) {
            case 'ja':
                return $symbol . number_format($amount, 0);
            case 'vi':
                return number_format($amount, 0, ',', '.') . ' ' . $symbol;
            default:
                return $symbol . number_format($amount, 2);
        }
    }
    
    /**
     * Format large numbers with K, M, B suffixes
     *
     * @param float $amount
     * @param string|null $locale
     * @return string
     */
    public static function formatCompact($amount, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        if ($amount >= 1000000000) {
            $formatted = number_format($amount / 1000000000, 1);
            $suffix = $locale === 'vi' ? ' tỷ' : 'B';
        } elseif ($amount >= 1000000) {
            $formatted = number_format($amount / 1000000, 1);
            $suffix = $locale === 'vi' ? ' triệu' : 'M';
        } elseif ($amount >= 1000) {
            $formatted = number_format($amount / 1000, 1);
            $suffix = $locale === 'vi' ? 'K' : 'K';
        } else {
            return self::format($amount, $locale);
        }
        
        // Remove .0 from whole numbers
        $formatted = rtrim($formatted, '.0');
        
        switch ($locale) {
            case 'ja':
                return '¥' . $formatted . $suffix;
            case 'vi':
                return $formatted . $suffix . ' ₫';
            default:
                return '$' . $formatted . $suffix;
        }
    }
}
