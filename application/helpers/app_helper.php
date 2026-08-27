<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * EduCore Application Helper
 *
 * Centralised utility functions used across controllers, models, and views.
 * Autoloaded via config/autoload.php.
 */

// ---------------------------------------------------------------------------
// User / Identity helpers
// ---------------------------------------------------------------------------

/**
 * Generate up to 2 initials from a full name.
 * e.g. "Ananthu Kumar" → "AK",  "Admin" → "AD"
 */
if ( ! function_exists('educore_initials'))
{
    function educore_initials($name)
    {
        $name = trim((string)$name);
        if ($name === '') return 'U';

        $parts    = preg_split('/\s+/', $name);
        $initials = '';
        foreach ($parts as $p) {
            if (!empty($p)) $initials .= strtoupper($p[0]);
        }
        $initials = substr($initials, 0, 2);
        return $initials ?: 'U';
    }
}

// ---------------------------------------------------------------------------
// Formatting helpers
// ---------------------------------------------------------------------------

/**
 * Format a number as Indian currency.
 * e.g. 841200 → "₹ 8,41,200"
 */
if ( ! function_exists('educore_currency'))
{
    function educore_currency($amount, $symbol = '₹', $decimals = 2)
    {
        $amount = (float)$amount;
        // Indian numbering system formatting
        $formatted = number_format(abs($amount), $decimals);
        // Convert to Indian style (lakhs/crores)
        $parts  = explode('.', $formatted);
        $int    = $parts[0];
        $dec    = isset($parts[1]) ? '.' . $parts[1] : '';
        $last3  = strlen($int) > 3 ? substr($int, -3) : $int;
        $rest   = strlen($int) > 3 ? substr($int, 0, strlen($int) - 3) : '';
        if ($rest !== '') {
            $rest = preg_replace('/(\d)(?=(\d{2})+(?!\d))/', '$1,', $rest);
            $int  = $rest . ',' . $last3;
        }
        $sign = $amount < 0 ? '-' : '';
        return $sign . $symbol . ' ' . $int . $dec;
    }
}

/**
 * Return a human-readable "time ago" string.
 * e.g. "2 hours ago", "3 days ago"
 */
if ( ! function_exists('educore_timeago'))
{
    function educore_timeago($datetime)
    {
        if (empty($datetime)) return '—';
        $now  = time();
        $then = is_numeric($datetime) ? (int)$datetime : strtotime($datetime);
        if ($then === FALSE) return '—';
        $diff = max(0, $now - $then);

        if ($diff < 60)       return 'just now';
        if ($diff < 3600)     return floor($diff / 60) . ' min ago';
        if ($diff < 86400)    return floor($diff / 3600) . ' hr ago';
        if ($diff < 604800)   return floor($diff / 86400) . 'd ago';
        if ($diff < 2592000)  return floor($diff / 604800) . ' weeks ago';
        if ($diff < 31536000) return floor($diff / 2592000) . ' months ago';
        return floor($diff / 31536000) . ' yrs ago';
    }
}

/**
 * Combine first and last name into a consistently formatted full name.
 */
if ( ! function_exists('educore_student_name'))
{
    function educore_student_name($first_name, $last_name = '')
    {
        return trim(trim((string)$first_name) . ' ' . trim((string)$last_name));
    }
}

/**
 * Format a date for display (accepts Y-m-d or any strtotime-compatible string).
 * e.g. "2025-08-15" → "15 Aug 2025"
 */
if ( ! function_exists('educore_date'))
{
    function educore_date($date, $format = 'd M Y')
    {
        if (empty($date) || $date === '0000-00-00') return '—';
        $ts = is_numeric($date) ? (int)$date : strtotime($date);
        return $ts ? date($format, $ts) : '—';
    }
}

// ---------------------------------------------------------------------------
// Status / badge helpers (return safe HTML strings)
// ---------------------------------------------------------------------------

/**
 * Return a Tailwind-styled badge span for common status values.
 * Safe to echo directly in views (values are html_escape'd).
 */
if ( ! function_exists('educore_status_badge'))
{
    function educore_status_badge($status)
    {
        $map = [
            'Active'    => 'bg-secondary-container text-on-secondary-container',
            'Inactive'  => 'bg-surface-container-high text-on-surface-variant',
            'Locked'    => 'bg-error-container text-on-error-container',
            'Suspended' => 'bg-error-container text-on-error-container',
            'Pending'   => 'bg-tertiary-fixed text-on-tertiary-fixed',
            'Approved'  => 'bg-secondary-container text-on-secondary-container',
            'Rejected'  => 'bg-error-container text-on-error-container',
            'Present'   => 'bg-secondary-container text-on-secondary-container',
            'Absent'    => 'bg-error-container text-on-error-container',
            'Late'      => 'bg-tertiary-fixed text-on-tertiary-fixed',
            'Paid'      => 'bg-secondary-container text-on-secondary-container',
            'Unpaid'    => 'bg-error-container text-on-error-container',
            'Partial'   => 'bg-tertiary-fixed text-on-tertiary-fixed',
        ];
        $label = html_escape($status);
        $cls   = isset($map[$status])
            ? $map[$status]
            : 'bg-surface-container-high text-on-surface-variant';
        return '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold ' . $cls . '">' . $label . '</span>';
    }
}

/**
 * Render a yes/no icon badge.
 */
if ( ! function_exists('educore_yn_badge'))
{
    function educore_yn_badge($value)
    {
        if ($value === 'y' || $value === 1 || $value === true) {
            return '<span class="material-symbols-outlined text-[16px] text-on-secondary-container">check_circle</span>';
        }
        return '<span class="material-symbols-outlined text-[16px] text-on-surface-variant">cancel</span>';
    }
}

// ---------------------------------------------------------------------------
// Security / sanitisation helpers
// ---------------------------------------------------------------------------

/**
 * Cast a value to a positive integer. Returns NULL if invalid/zero.
 * Use for URL segment IDs to avoid IDOR with non-numeric values.
 */
if ( ! function_exists('educore_id'))
{
    function educore_id($value)
    {
        $id = (int)$value;
        return $id > 0 ? $id : NULL;
    }
}

/**
 * Safely get an array value with a default fallback.
 */
if ( ! function_exists('educore_array_get'))
{
    function educore_array_get(array $arr, $key, $default = NULL)
    {
        return isset($arr[$key]) ? $arr[$key] : $default;
    }
}
