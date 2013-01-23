<?php
// I18N support information here
if (empty($locale))
    $language = 'en_GB.utf8';
if (isset($_GET['locale']) && !empty($_GET['locale']))
    $language = $_GET['locale'];
        
putenv("LANG=$language"); 
setlocale(LC_ALL, $language);

// Set the text domain
bindtextdomain($domain, "locale"); 
textdomain($domain);
bind_textdomain_codeset($domain, 'UTF-8');

// Get locale-specific currency and decimal info:
global $locale_info;
$locale_info = localeconv();

/* Text domain binding should be done in the source like so:
 * 
 * $domain = "login";
 * require_once 'locale.php';
 * 
 */
 
?>
