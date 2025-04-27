<?php
// Include guard to prevent multiple declarations
if (!class_exists('CookieManager')) {

class CookieManager {
    /**
     * Set a cookie with the given parameters
     * 
     * @param string $name Cookie name
     * @param string $value Cookie value
     * @param int $days Number of days until the cookie expires
     * @param string $path Cookie path
     * @param bool $secure Cookie secure flag
     * @param bool $httponly Cookie httponly flag
     * @return bool True on success, false on failure
     */
    public static function setCookie($name, $value, $days = 30, $path = "/", $secure = false, $httponly = false) {
        $expires = time() + ($days * 86400); // 86400 = 1 day in seconds
        return setcookie($name, $value, $expires, $path, "", $secure, $httponly);
    }
    
    /**
     * Get a cookie value by name
     * 
     * @param string $name Cookie name
     * @param mixed $default Default value if cookie doesn't exist
     * @return mixed Cookie value or default value
     */
    public static function getCookie($name, $default = null) {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
    }
    
    /**
     * Check if a cookie exists
     * 
     * @param string $name Cookie name
     * @return bool True if cookie exists, false otherwise
     */
    public static function hasCookie($name) {
        return isset($_COOKIE[$name]);
    }
    
    /**
     * Delete a cookie
     * 
     * @param string $name Cookie name
     * @param string $path Cookie path
     * @return bool True on success, false on failure
     */
    public static function deleteCookie($name, $path = "/") {
        if (isset($_COOKIE[$name])) {
            // To delete a cookie, set its expiration time to the past
            return setcookie($name, "", time() - 3600, $path);
        }
        return false;
    }
    
    /**
     * Set theme preference cookie
     * 
     * @param string $theme Theme name (light, dark)
     * @param int $days Number of days until the cookie expires
     * @return bool True on success, false on failure
     */
    public static function setTheme($theme, $days = 180) {
        // Theme preference should persist for a long time (6 months by default)
        return self::setCookie("theme_preference", $theme, $days);
    }
    
    /**
     * Get theme preference from cookie
     * 
     * @param string $default Default theme if no preference is set
     * @return string Theme name
     */
    public static function getTheme($default = "light") {
        return self::getCookie("theme_preference", $default);
    }
    
    /**
     * Set language preference cookie
     * 
     * @param string $lang Language code (en, fr, es, etc.)
     * @param int $days Number of days until the cookie expires
     * @return bool True on success, false on failure
     */
    public static function setLanguage($lang, $days = 180) {
        // Language preference should persist for a long time (6 months by default)
        return self::setCookie("language_preference", $lang, $days);
    }
    
    /**
     * Get language preference from cookie
     * 
     * @param string $default Default language if no preference is set
     * @return string Language code
     */
    public static function getLanguage($default = "en") {
        return self::getCookie("language_preference", $default);
    }
}

// End of include guard
}
?> 