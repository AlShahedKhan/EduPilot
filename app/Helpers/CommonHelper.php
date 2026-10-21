<?php

use App\Models\PaymentMethods;
use App\Models\GlobalSettings;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
//All common helper functions
if (! function_exists('get_user_image')) {
    function get_user_image($file_name_or_user_id = '')
    {
        if (is_numeric($file_name_or_user_id)) {
            $user_id = $file_name_or_user_id;
            $file_name = "";
        } else {
            $user_id = "";
            $file_name = $file_name_or_user_id;
        }

        if ($user_id > 0) {
            $user_id = $file_name_or_user_id;
            $user_information = DB::table('users')->where('id', $user_id)->value('user_information');

            $file_name = json_decode($user_information)->photo;

            if (file_exists(public_path() . '/assets/uploads/user-images/' . $file_name) && is_file(public_path() . '/assets/uploads/user-images/' . $file_name)) {
                return asset('assets/uploads/user-images/' . $file_name);
            } else {
                return asset('assets/uploads/user-images/thumbnail.png');
            }
        } elseif (File::exists('public/assets/uploads/user-images/' . $file_name)) {
            return asset('assets/uploads/user-images/' . $file_name);
        } else {
            return asset('assets/uploads/user-images/thumbnail.png');
        }
    }
}

if (!function_exists('addon_status')) {
    function addon_status($unique_identifier = '')
    {
        $result =  DB::table('addons')->where('unique_identifier', $unique_identifier)->value('status');
        return $result;
    }
}

if (! function_exists('phrase')) {
    function phrase($string = '')
    {
        return $string;
    }
}

if (! function_exists('get_all_language')) {
    function get_all_language()
    {
        if (!Schema::hasTable('language')) {
            return collect();
        }
        return DB::table('language')->select('name')->distinct()->get();
    }
}

if (! function_exists('get_phrase')) {
    function get_phrase($phrase = '')
    {
        if (!Schema::hasTable('language')) {
            return $phrase;
        }

        if (isset(auth()->user()->id)) {
            $active_language = auth()->user()->language ?? get_settings('language');
        } else {
            $active_language = get_settings('language');
        }


        $query = DB::table('language')->where('name', $active_language)->where('phrase', $phrase);
        if ($query->get()->count() == 0) {
            $translated = $phrase;

            $all_language = get_all_language();

            if ($all_language->count() > 0) {
                foreach ($all_language as $language) {

                    if (DB::table('language')->where('name', $language->name)->where('phrase', $phrase)->get()->count() == 0) {
                        DB::table('language')->insert(array('name' => $language->name, 'phrase' => $phrase, 'translated' => $translated));
                    }
                }
            } else {
                DB::table('language')->insert(array('name' => 'english', 'phrase' => $phrase, 'translated' => $translated));
            }
            return $translated;
        }
        return $query->value('translated');
    }
}

if (! function_exists('remove_js')) {
    function remove_js($string = '')
    {
        return preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $string);
    }
}

if (!function_exists('date_formatter')) {
    function date_formatter($strtotime = "", $format = "")
    {
        if ($format == "") {
            return date('d', $strtotime) . ' ' . date('M', $strtotime) . ' ' . date('Y', $strtotime);
        }

        if ($format == 1) {
            return date('D', $strtotime) . ', ' . date('d', $strtotime) . ' ' . date('M', $strtotime) . ' ' . date('Y', $strtotime);
        }

        if ($format == 2) {
            $time_difference = time() - $strtotime;
            if ($time_difference < 1) {
                return 'less than 1 second ago';
            }
            //864000 = 10 days
            if ($time_difference > 864000) {
                return dateFormatter($strtotime, 1);
            }

            $condition = array(
                12 * 30 * 24 * 60 * 60    => 'year',
                30 * 24 * 60 * 60        =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60                 =>  'hour',
                60                      =>  'minute',
                1                       =>  'second'
            );

            foreach ($condition as $secs => $str) {
                $d = $time_difference / $secs;
                if ($d >= 1) {
                    $t = round($d);
                    return $t . ' ' . $str . ($t > 1 ? 's' : '') . ' ago';
                }
            }
        }
    }
}

if (!function_exists('currency')) {
    function currency($price = "")
    {
        $symbol = DB::table('global_settings')->where('key', 'system_currency')->value('value');
        if (!empty($price)) {
            return $price . ' ' . $symbol;
        } else {
            return $symbol;
        }
    }
}

if (!function_exists('school_currency')) {
    function school_currency($price = "")
    {
        $symbol = DB::table('schools')->where('id', auth()->user()->school_id)->value('school_currency');
        if (!empty($price)) {
            return $price . ' ' . $symbol;
        } else {
            return $symbol;
        }
    }
}


if (!function_exists('slugify')) {
    function slugify($string)
    {
        $string = preg_replace('~[^\\pL\d]+~u', '-', $string);
        $string = trim($string, '-');
        return strtolower($string);
    }
}

if (!function_exists('get_video_extension')) {
    function get_video_extension($url)
    {
        if (strpos($url, '.mp4') > 0) {
            return 'mp4';
        } elseif (strpos($url, '.webm') > 0) {
            return 'webm';
        } else {
            return 'unknown';
        }
    }
}

if (!function_exists('ellipsis')) {
    function ellipsis($long_string, $max_character = 30)
    {
        $short_string = strlen($long_string) > $max_character ? mb_substr($long_string, 0, $max_character) . "..." : $long_string;
        return $short_string;
    }
}


// Global Settings
if (!function_exists('get_settings')) {
    function get_settings($key = '', $type = '')
    {
        if (!Schema::hasTable('global_settings')) {
            return null;
        }

        $global_settings = DB::table('global_settings')->where('key', $key)->value('value');

        if ($type == 'json') {
            $global_settings = json_decode($global_settings);
        }

        return $global_settings;
    }
}

// School Settings
if (!function_exists('get_school_settings')) {
    function get_school_settings($school_id = '')
    {
        $running_session = DB::table('schools')->where('id', $school_id)->get();

        return $running_session;
    }
}

// Config Settings
if (!function_exists('set_config')) {
    function set_config($key = '', $value = '')
    {
        $config = json_decode(file_get_contents(base_path('config/config.json')), true);

        $config[$key] = $value;

        file_put_contents(base_path('config/config.json'), json_encode($config));
    }
}

// Read a value from config/config.json
if (!function_exists('get_mail_config')) {
    function get_mail_config($key = '', $default = '')
    {
        $path = base_path('config/config.json');
        if (!file_exists($path)) {
            return $default;
        }
        $config = json_decode(file_get_contents($path), true);
        return $config[$key] ?? $default;
    }
}

// Superadmin logo helpers (stored in config.json, shared across all tenants)
if (!function_exists('get_sa_logo')) {
    function get_sa_logo(): string
    {
        return get_mail_config('sa_logo', 'white_logo.png');
    }
}

if (!function_exists('get_sa_favicon')) {
    function get_sa_favicon(): string
    {
        return get_mail_config('sa_favicon', 'favicon.png');
    }
}

// Human readable time
if (!function_exists('time_formatter')) {
    function time_formatter($duration, $format = "")
    {
        if ($duration && $format == "") {
            $duration_array = explode(':', $duration);
            $hour   = $duration_array[0];
            $minute = $duration_array[1];
            $second = $duration_array[2];
            if ($hour > 0) {
                $duration = $hour . ' ' . 'hr' . ' ' . $minute . ' ' . 'min';
            } elseif ($minute > 0) {
                if ($second > 0) {
                    $duration = ($minute + 1) . ' ' . 'min';
                } else {
                    $duration = $minute . ' ' . 'min';
                }
            } elseif ($second > 0) {
                $duration = $second . ' ' . 'sec';
            } else {
                $duration = '00:00';
            }
        } elseif ($seconds && $format == 'seconds_to_format') {
            $hours = floor($seconds / 3600);
            $mins = floor($seconds / 60 % 60);
            $secs = floor($seconds % 60);

            $duration = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
        } elseif ($seconds && $format == 'format_to_seconds') {
        }
        return $duration;
    }
}

// RANDOM NUMBER GENERATOR FOR ELSEWHERE
if (!function_exists('random')) {
    function random($length_of_string, $lowercase = false)
    {
        // String of all alphanumeric character
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        // Shufle the $str_result and returns substring
        // of specified length
        $randVal = substr(str_shuffle($str_result), 0, $length_of_string);
        if ($lowercase) {
            $randVal = strtolower($randVal);
        }
        return $randVal;
    }
}

// RANDOM NUMBER GENERATOR FOR STUDENT CODE
if (! function_exists('student_code')) {
    function student_code($length_of_string = 8)
    {
        // String of all numeric character
        $str_result = '0123456789';
        // Shufle the $str_result and returns substring of specified length
        $unique_id = substr(str_shuffle($str_result), 0, $length_of_string);
        $splited_unique_id = str_split($unique_id, 4);
        $running_year = date('Y');
        $student_code = $running_year . '-' . $splited_unique_id[0] . '-' . $splited_unique_id[1];
        return $student_code;
    }
}

// TEACHER PERMISSION. PROVIDE MODULE NAME AND TEACHERS ID
if (! function_exists('null_checker')) {
    function null_checker($value = "")
    {
        if (trim($value, "") == "") {
            return '(' . get_phrase('Not found') . ')';
        } else {
            return $value;
        }
    }
}

// GET GRADE
if (! function_exists('get_grade')) {
    function get_grade($acquired_number = "", $type = "")
    {
        if (empty($acquired_number)) {
            return "N/A";
        } else {
            $acquired_grade = DB::table('grades')->where('school_id', auth()->user()->school_id)->distinct()->get();
            if ($acquired_grade->count() > 0) {
                $founder = false;
                foreach ($acquired_grade as $grade) {
                    if ($acquired_number >= $grade->mark_from && $acquired_number <= $grade->mark_upto) {
                        $founder = true;
                        if (!empty($type)) {
                            return $grade[$type];
                        } else {
                            return $grade->name . '(' . $grade->grade_point . ')';
                        }
                    }
                }
                if (!$founder) {
                    return "N/A";
                }
            } else {
                return "N/A";
            }
        }
    }
}

if (!function_exists('get_active_currency')) {
    function get_active_currency()
    {
        $global_system_currency = GlobalSettings::where('key', 'system_currency')->get()->toArray();
        $global_system_currency = $global_system_currency[0]['value'];
        return $global_system_currency;
    }
}

if (!function_exists('relogin_user')) {
    function relogin_user($user_id = '')
    {
        $user = User::find($user_id);
        Auth::login($user);
    }
}



if (!function_exists('get_payment_keys')) {
    function get_payment_keys($payment_method = '', $returnItem = '')
    {
        $return_value;
        $global_system_currency = GlobalSettings::where('key', 'system_currency')->get()->toArray();
        $global_system_currency = $global_system_currency[0]['value'];

        if ($payment_method == "stripe") {
            $stripe = PaymentMethods::where('name', 'stripe')->first()->toArray();
            $stripe_keys = json_decode($stripe['payment_keys']);
            if ($stripe['mode'] == "test") {
                $return_value['test_key'] = $stripe_keys->test_key;
                $return_value['test_secret_key'] = $stripe_keys->test_secret_key;
                $return_value['currency'] = $global_system_currency;
                return    $return_value[$returnItem];
            } elseif ($stripe['mode'] == "live") {
                $return_value['public_live_key'] = $stripe_keys->public_live_key;
                $return_value['secret_live_key'] = $stripe_keys->secret_live_key;
                $return_value['currency'] = $global_system_currency;
                return    $return_value[$returnItem];
            }
        }


        if ($payment_method == "paytm") {
            $paytm = PaymentMethods::where('name', 'paytm')->first()->toArray();
            $paytm_keys = json_decode($paytm['payment_keys']);

            if ($paytm['mode'] == "test") {
                $return_value['environment'] = $paytm_keys->environment;
                $return_value['merchant_id'] = $paytm_keys->test_merchant_id;
                $return_value['merchant_key'] = $paytm_keys->test_merchant_key;
                $return_value['merchant_website'] = $paytm_keys->merchant_website;
                $return_value['channel'] = $paytm_keys->channel;
                $return_value['industry_type'] = $paytm_keys->industry_type;
                $return_value['currency'] = $global_system_currency;
                return    $return_value[$returnItem];
            } elseif ($paytm['mode'] == "live") {
                $return_value['environment'] = $paytm_keys->environment;
                $return_value['merchant_id'] = $paytm_keys->live_merchant_id;
                $return_value['merchant_key'] = $paytm_keys->live_merchant_key;
                $return_value['merchant_website'] = $paytm_keys->merchant_website;
                $return_value['channel'] = $paytm_keys->channel;
                $return_value['industry_type'] = $paytm_keys->industry_type;
                $return_value['currency'] = $global_system_currency;
                return    $return_value[$returnItem];
            }
        }
    }
}

if (!function_exists('user_name')) {
    function user_name($id = '')
    {
        $result =  DB::table('users')->where('id', $id)->value('name');
        return $result;
    }
}


if (!function_exists('class_name')) {
    function class_name($id = '', $school_id = "")
    {
        $result =  DB::table('classes')->where('id', $id)->where('school_id', $school_id)->value('name');
        return $result;
    }
}

//school status check
if (!function_exists('school_status_check')) {
    function school_status_check($email = '')
    {
        // 1. If inside tenant context, prioritize tenant and tenant school status
        if (function_exists('tenancy') && tenancy()->initialized) {
            $tenantStatus = tenancy()->tenant->status ?? null;
            if ($tenantStatus !== null) {
                return (int) $tenantStatus;
            }
            try {
                $schoolStatus = DB::table('schools')->value('status');
                if ($schoolStatus !== null) {
                    return (int) $schoolStatus;
                }
            } catch (\Throwable $e) {
            }
        }

        // 2. Look up by user's email if provided
        if (!empty($email)) {
            try {
                $school_id = DB::table('users')->where('email', $email)->value('school_id');
                if ($school_id) {
                    $status = DB::table('schools')->where('id', $school_id)->value('status');
                    if ($status !== null) {
                        return (int) $status;
                    }
                }
            } catch (\Throwable $e) {
            }
        }

        // 3. Fallback: check first school record
        try {
            $status = DB::table('schools')->value('status');
            if ($status !== null) {
                return (int) $status;
            }
        } catch (\Throwable $e) {
        }

        return 1;
    }
}

//user role check
if (!function_exists('user_role_check')) {
    function user_role_check($email = '')
    {
        $role_id =  DB::table('users')->where('email', $email)->value('role_id');

        return $role_id;
    }
}

// Safe user info access
if (!function_exists('user_info')) {
    function user_info($user = null, $key = '')
    {
        if (!$user) return '';
        $info = json_decode($user->user_information ?? '{}');
        return $info->{$key} ?? '';
    }
}

// Root domain under which each school tenant subdomain is created.
if (!function_exists('get_school_domain_base')) {
    function get_school_domain_base()
    {
        return config('tenancy.school_domain_base', env('TENANT_DOMAIN_BASE', 'localhost'));
    }
}

// Generate the tenant id (used as DB name: prefix + id). Accepts a bare
// subdomain ("school1") or a full URL ("http://school1.localhost:8000/") and
// extracts just the subdomain label, e.g. "school1".
if (!function_exists('make_tenant_id')) {
    function make_tenant_id($school_url = '')
    {
        $url = strtolower(trim((string) $school_url));

        // Remove scheme.
        $url = preg_replace('#^https?://#i', '', $url);
        // Remove path, query and fragment (delimiter ~ so # inside class is safe).
        $url = preg_replace('~[/?#].*$~', '', $url);
        // Remove port, e.g. "school1.localhost:8000" -> "school1.localhost".
        $url = preg_replace('#:\d+$#', '', $url);
        // Hostname like "school1.localhost" -> take the first label.
        $url = explode('.', $url)[0];

        $base = preg_replace('/[^a-z0-9\-]/', '', $url);

        if (empty($base)) {
            $base = 'school';
        }

        return $base;
    }
}

// Build the school's public URL, e.g. https://{slug}.mydomain.com
if (!function_exists('school_domain_url')) {
    function school_domain_url($slug = '')
    {
        return 'https://' . $slug . '.' . get_school_domain_base();
    }
}

// Build a simple view-friendly object from a tenant's metadata
if (!function_exists('tenantSchoolMap')) {
    function tenantSchoolMap($tenant)
    {
        return (object) [
            'id'              => $tenant->id,
            'tenant_id'       => $tenant->id,
            'title'           => $tenant->title ?? '',
            'email'           => $tenant->email ?? '',
            'phone'           => $tenant->phone ?? '',
            'address'         => $tenant->address ?? '',
            'school_info'     => $tenant->school_info ?? '',
            'admin_name'      => $tenant->admin_name ?? '',
            'admin_email'     => $tenant->admin_email ?? '',
            'admin_address'   => $tenant->admin_address ?? '',
            'admin_phone'     => $tenant->admin_phone ?? '',
            'status'          => $tenant->status ?? '1',
            'running_session' => $tenant->running_session ?? '',
            'domain'          => optional($tenant->domains->first())->domain ?? ($tenant->id . '.' . get_school_domain_base()),
            'btcl_email'      => optional($tenant->domains->first())->btcl_email ?? '',
            'btcl_phone'      => optional($tenant->domains->first())->btcl_phone ?? '',
            'btcl_password'   => optional($tenant->domains->first())->btcl_password ?? '',
        ];
    }
}
