<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * School Application Helper
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
if ( ! function_exists('school_initials'))
{
    function school_initials($name)
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
if ( ! function_exists('school_currency'))
{
    function school_currency($amount, $symbol = '₹', $decimals = 2)
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
if ( ! function_exists('school_timeago'))
{
    function school_timeago($datetime)
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
if ( ! function_exists('school_student_name'))
{
    function school_student_name($first_name, $last_name = '')
    {
        return trim(trim((string)$first_name) . ' ' . trim((string)$last_name));
    }
}

/**
 * Format a date for display (accepts Y-m-d or any strtotime-compatible string).
 * e.g. "2025-08-15" → "15 Aug 2025"
 */
if ( ! function_exists('school_date'))
{
    function school_date($date, $format = 'd M Y')
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
if ( ! function_exists('school_status_badge'))
{
    function school_status_badge($status)
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
if ( ! function_exists('school_yn_badge'))
{
    function school_yn_badge($value)
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
if ( ! function_exists('school_id'))
{
    function school_id($value)
    {
        $id = (int)$value;
        return $id > 0 ? $id : NULL;
    }
}

/**
 * Safely get an array value with a default fallback.
 */
if ( ! function_exists('school_array_get'))
{
    function school_array_get(array $arr, $key, $default = NULL)
    {
        return isset($arr[$key]) ? $arr[$key] : $default;
    }
}

// ---------------------------------------------------------------------------
// Global Academic Year Context Helpers
// ---------------------------------------------------------------------------

/**
 * Get the currently active / selected academic year ID.
 * Returns integer ID. Uses per-request caching to eliminate duplicate queries.
 */
if ( ! function_exists('get_current_academic_year_id'))
{
    function get_current_academic_year_id()
    {
        static $cached_year_id = NULL;
        if ($cached_year_id !== NULL) {
            return $cached_year_id;
        }

        $CI =& get_instance();
        if (!isset($CI->session)) {
            $CI->load->library('session');
        }

        $session_year_id = $CI->session->userdata('selected_academic_year_id');
        if (empty($session_year_id)) {
            $session_year_id = $CI->session->userdata('academic_year_id');
        }

        if (!empty($session_year_id)) {
            $year_id = (int)$session_year_id;
            // Verify it still exists and is not deleted
            if (!isset($CI->Academic_year_model)) {
                $CI->load->model('Academic_year_model');
            }
            $year = $CI->Academic_year_model->get_by_id($year_id);
            if ($year) {
                $cached_year_id = $year_id;
                return $cached_year_id;
            }
        }

        // Fallback to active academic year
        if (!isset($CI->Academic_year_model)) {
            $CI->load->model('Academic_year_model');
        }
        $active_year = $CI->Academic_year_model->get_active_year();
        if ($active_year) {
            $year_id = (int)$active_year->academic_year_id;
            $CI->session->set_userdata('selected_academic_year_id', $year_id);
            $CI->session->set_userdata('academic_year_id', $year_id);
            $cached_year_id = $year_id;
            return $cached_year_id;
        }

        return 1; // Default fallback if no years configured
    }
}

/**
 * Get the full record object for the currently active / selected academic year.
 */
if ( ! function_exists('get_current_academic_year'))
{
    function get_current_academic_year()
    {
        static $cached_year = NULL;
        if ($cached_year !== NULL) {
            return $cached_year;
        }

        $CI =& get_instance();
        if (!isset($CI->Academic_year_model)) {
            $CI->load->model('Academic_year_model');
        }

        $year_id = get_current_academic_year_id();
        $year = $CI->Academic_year_model->get_by_id($year_id);
        if (!$year) {
            $year = $CI->Academic_year_model->get_active_year();
        }

        $cached_year = $year;
        return $cached_year;
    }
}

/**
 * Get the full record object for a specific or current academic year.
 *
 * @param  int|null $academic_year_id
 * @return object|null
 */
if ( ! function_exists('get_academic_year_record'))
{
    function get_academic_year_record($academic_year_id = NULL)
    {
        $CI =& get_instance();
        if (!isset($CI->Academic_year_model)) {
            $CI->load->model('Academic_year_model');
        }

        if ($academic_year_id !== NULL && (int)$academic_year_id > 0) {
            return $CI->Academic_year_model->get_by_id((int)$academic_year_id);
        }

        return get_current_academic_year();
    }
}

/**
 * Get the default / synchronized date for an academic year.
 * Rule:
 *   If today's server date falls within [start_date, end_date] of the academic year:
 *       return today's date (Y-m-d)
 *   Else:
 *       return academic_year_start_date (Y-m-d)
 *
 * @param  int|object|null $academic_year
 * @return string (Y-m-d)
 */
if ( ! function_exists('get_academic_year_default_date'))
{
    function get_academic_year_default_date($academic_year = NULL)
    {
        $today = date('Y-m-d');
        $year_obj = is_object($academic_year) ? $academic_year : get_academic_year_record($academic_year);

        if (!$year_obj || empty($year_obj->start_date) || empty($year_obj->end_date)) {
            return $today;
        }

        $start_date = $year_obj->start_date;
        $end_date   = $year_obj->end_date;

        if ($today >= $start_date && $today <= $end_date) {
            return $today;
        }

        return $start_date;
    }
}

/**
 * Check if a given date falls within the start_date and end_date of an academic year.
 *
 * @param  string          $date (Y-m-d or parseable string)
 * @param  int|object|null $academic_year
 * @return bool
 */
if ( ! function_exists('is_date_within_academic_year'))
{
    function is_date_within_academic_year($date, $academic_year = NULL)
    {
        if (empty($date)) {
            return FALSE;
        }

        $formatted_date = date('Y-m-d', strtotime($date));
        if ($formatted_date === '1970-01-01' && $date !== '1970-01-01') {
            return FALSE;
        }

        $year_obj = is_object($academic_year) ? $academic_year : get_academic_year_record($academic_year);
        if (!$year_obj || empty($year_obj->start_date) || empty($year_obj->end_date)) {
            return TRUE;
        }

        return ($formatted_date >= $year_obj->start_date && $formatted_date <= $year_obj->end_date);
    }
}

/**
 * Normalize and validate a date against an academic year.
 * Rule:
 *   If user selected a date and it falls within [start_date, end_date], keep it.
 *   If user selected a date and it falls OUTSIDE [start_date, end_date], or date is empty/invalid,
 *   safely normalize to the academic year's default date (today if in-range, else start_date).
 *
 * @param  string|null     $date
 * @param  int|object|null $academic_year
 * @return string (Y-m-d)
 */
if ( ! function_exists('normalize_date_to_academic_year'))
{
    function normalize_date_to_academic_year($date = NULL, $academic_year = NULL)
    {
        $year_obj = is_object($academic_year) ? $academic_year : get_academic_year_record($academic_year);
        $default_date = get_academic_year_default_date($year_obj);

        if (empty($date)) {
            return $default_date;
        }

        $formatted_date = date('Y-m-d', strtotime($date));
        if ($formatted_date === '1970-01-01' && $date !== '1970-01-01') {
            return $default_date;
        }

        if (!$year_obj || empty($year_obj->start_date) || empty($year_obj->end_date)) {
            return $formatted_date;
        }

        if ($formatted_date >= $year_obj->start_date && $formatted_date <= $year_obj->end_date) {
            return $formatted_date;
        }

        return $default_date;
    }
}

/**
 * Set the currently selected academic year in session context.
 *
 * @param  int $academic_year_id
 * @return bool
 */
if ( ! function_exists('set_current_academic_year'))
{
    function set_current_academic_year($academic_year_id)
    {
        $CI =& get_instance();
        if (!isset($CI->Academic_year_model)) {
            $CI->load->model('Academic_year_model');
        }

        $year = $CI->Academic_year_model->get_by_id((int)$academic_year_id);
        if (!$year || $year->is_deleted === 'y' || (int)$year->status !== 1) {
            return FALSE;
        }

        $CI->session->set_userdata('selected_academic_year_id', (int)$year->academic_year_id);
        $CI->session->set_userdata('academic_year_id', (int)$year->academic_year_id);
        return TRUE;
    }
}

/**
 * Retrieve all available (active & non-deleted) academic years for dropdown selector.
 */
if ( ! function_exists('get_available_academic_years'))
{
    function get_available_academic_years()
    {
        static $cached_years = NULL;
        if ($cached_years !== NULL) {
            return $cached_years;
        }

        $CI =& get_instance();
        if (!isset($CI->Academic_year_model)) {
            $CI->load->model('Academic_year_model');
        }
        $cached_years = $CI->Academic_year_model->get_available_years();
        return $cached_years;
    }
}

/**
 * Check if the user has permission to change the active global academic year.
 *
 * @param  int|null $user_id
 * @return bool
 */
if ( ! function_exists('can_change_academic_year'))
{
    function can_change_academic_year($user_id = NULL)
    {
        static $cached_can_change = [];
        $cache_key = ($user_id === NULL) ? 'current' : (int)$user_id;
        if (isset($cached_can_change[$cache_key])) {
            return $cached_can_change[$cache_key];
        }

        $CI =& get_instance();
        if (!isset($CI->rbac)) {
            $CI->load->library('Rbac');
        }

        if ($CI->rbac->is_super_admin($user_id)) {
            $cached_can_change[$cache_key] = TRUE;
            return TRUE;
        }

        if ($user_id === NULL) {
            $user_data = $CI->session->userdata('user');
            $user_id   = (int)($user_data['user_id'] ?? $CI->session->userdata('user_id') ?? 0);
            $role_name = $user_data['role'] ?? $CI->session->userdata('user_role') ?? '';
            $role_code = $user_data['role_code'] ?? '';
        } else {
            if (!isset($CI->User_model)) {
                $CI->load->model('User_model');
            }
            $user = $CI->User_model->get_by_id($user_id);
            $role_name = $user ? $user->role_name : '';
            $role_code = $user ? $user->role_code : '';
        }

        $role_name_clean = strtolower(trim((string)$role_name));
        $role_code_clean = strtoupper(trim((string)$role_code));

        if (in_array($role_name_clean, ['super admin', 'principal', 'admin']) || in_array($role_code_clean, ['SUPER_ADMIN', 'PRINCIPAL', 'ADMIN'])) {
            $cached_can_change[$cache_key] = TRUE;
            return TRUE;
        }

        $res = ($CI->rbac->has_permission('academics.edit', $user_id)
            || $CI->rbac->has_permission('academics.manage', $user_id)
            || $CI->rbac->has_permission('academic_year.change', $user_id));

        $cached_can_change[$cache_key] = $res;
        return $res;
    }
}

// ---------------------------------------------------------------------------
// Student Attendance & Class Level helpers
// ---------------------------------------------------------------------------

/**
 * Determine whether a class is Higher Secondary (+1 or +2)
 * e.g. "Grade 11", "Grade 12", "+1", "+2", "Class 11", "Class 12", "Plus One", "Plus Two", "XI", "XII"
 * Returns FALSE for LKG, UKG, Class 1 through Class 10.
 */
if ( ! function_exists('is_higher_secondary_class'))
{
    function is_higher_secondary_class($class)
    {
        if (empty($class)) return false;

        static $class_name_cache = array();

        $className = '';
        if (is_object($class)) {
            $className = isset($class->class_name) ? $class->class_name : '';
        } elseif (is_array($class)) {
            $className = isset($class['class_name']) ? $class['class_name'] : '';
        } elseif (is_int($class) || (is_string($class) && ctype_digit(trim($class)))) {
            $cid = (int)$class;
            if (isset($class_name_cache[$cid])) {
                $className = $class_name_cache[$cid];
            } elseif (function_exists('get_instance')) {
                $CI = &get_instance();
                if ($CI && isset($CI->db)) {
                    $row = $CI->db->select('class_name')->where('class_id', $cid)->get('tbl_classes')->row();
                    $className = $row ? $row->class_name : '';
                    $class_name_cache[$cid] = $className;
                }
            }
        } else {
            $className = (string)$class;
        }

        $trimmed = trim($className);
        if ($trimmed === '') return false;

        // Matches +1, +2, Plus One, Plus Two, Grade 11, Grade 12, Class 11, Class 12, Std 11, Std 12, 11th, 12th, XI, XII
        if (preg_match('/(\+1|\+2|plus\s*(?:one|two|1|2)|(?:grade|class|std|standard)?\s*(?:11|12)(?:th)?\b|\b(?:xi|xii)\b)/i', $trimmed)) {
            return true;
        }
        return false;
    }
}

/**
 * Get attendance workflow type for a class: 'daily' (LKG-10) or 'period' (+1/+2)
 */
if ( ! function_exists('get_attendance_workflow_type'))
{
    function get_attendance_workflow_type($class)
    {
        return is_higher_secondary_class($class) ? 'period' : 'daily';
    }
}

/**
 * Get allowed attendance statuses based on class level
 */
if ( ! function_exists('get_allowed_attendance_statuses'))
{
    function get_allowed_attendance_statuses($class = NULL)
    {
        if ($class !== NULL && is_higher_secondary_class($class)) {
            // +1 and +2: Present, Half Day, Absent, Late Coming
            return array('Present', 'Half Day', 'Absent', 'Late Coming');
        }
        // LKG - Class 10: Present, Half Day, Absent
        return array('Present', 'Half Day', 'Absent');
    }
}


