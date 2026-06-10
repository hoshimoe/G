<?php
/**
 * 重定向
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// Sanitize the redirect target and delay before they reach the page.
// redirectURL is a per-post custom field; without validation it would be
// emitted straight into a JS string (breakout / XSS) and could carry a
// javascript:/data: scheme (script execution via location.href).
$redirectURL = isset($this->fields->redirectURL) ? (string) $this->fields->redirectURL : '';
$redirectSecond = isset($this->fields->redirectSecond) ? (int) $this->fields->redirectSecond : 0;
if ($redirectSecond < 0) {
    $redirectSecond = 0;
}

// Allow only http(s) or site-relative URLs; drop anything else (javascript:, data:, …).
$scheme = $redirectURL !== '' ? parse_url($redirectURL, PHP_URL_SCHEME) : null;
if ($redirectURL !== '' && $scheme !== null && !in_array(strtolower($scheme), array('http', 'https'), true)) {
    $redirectURL = '';
}

// JSON-encode for a safe JS string literal; the HEX flags also neutralise
// '</script>', quotes and ampersands so the value cannot break out.
$jsonFlags = JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>

<div style="display: flex;height: 100vh;justify-content: center; align-items: center;">
    <h2 style="opacity: 0.3; font-weight: 200;font-size: 2.5rem">跳转中...</h2>
</div>

<script type="text/javascript">
    var __gRedirectURL = <?php echo json_encode($redirectURL, $jsonFlags); ?>;
    if (__gRedirectURL) {
        setTimeout(function () { window.location.href = __gRedirectURL; }, <?php echo $redirectSecond; ?>);
    }
</script>
