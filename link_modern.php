<?php
/**
 * 友情链接 - 现代
 *
 * @package custom
 */
$this->need('components/header.php');

$mlLinks = G::getFriendLinks();
$mlKinds = array('pin' => array(), 'link' => array(), 'todo' => array(), 'lost' => array());
if (is_array($mlLinks)) {
    foreach ($mlLinks as $mlIndex => $mlLink) {
        $mlView = G::friendLinkView($mlLink);
        $mlView['index'] = $mlIndex;
        $mlKinds[$mlView['kind']][] = $mlView;
    }
}
$mlTotal = is_array($mlLinks) ? count($mlLinks) : 0;

// 'all' lets visitors switch freely; flat/grouped/random lock the mode and hide
// the switcher. Anything unset/legacy falls back to 'all'.
$mlDisplay = (string) $this->options->modernLinkDisplay;
if (!in_array($mlDisplay, array('all', 'flat', 'grouped', 'random'), true)) {
    $mlDisplay = 'all';
}
$mlColumns = (int) $this->options->modernLinkColumns;
$mlColumns = ($mlColumns >= 1 && $mlColumns <= 6) ? $mlColumns : 0;
$mlIntro = ((string) $this->options->modernLinkIntro === 'bottom') ? 'bottom' : 'top';
$mlBanner = G::getArticleFieldsBanner($this);
$mlHasBanner = $mlBanner !== 'none' && $mlBanner !== '';
$mlContent = trim(G::analyzeContent($this->content));
$mlHasContent = $mlContent !== '';

$mlEsc = function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8', false);
};

$mlText = array(
    'pinned' => GI18n::t('link.section.pinned'),
    'links' => GI18n::t('link.section.links'),
    'todo' => GI18n::t('link.section.todo'),
    'lost' => GI18n::t('link.section.lost'),
    'uncategorized' => GI18n::t('link.section.uncategorized'),
    'reshuffle' => GI18n::t('link.display.reshuffle'),
    'noCategories' => GI18n::t('link.display.no_categories'),
    'result' => GI18n::t('link.search.result'),
    'noResult' => GI18n::t('link.search.empty'),
    'copy' => GI18n::t('link.copy'),
    'copied' => GI18n::t('link.copied'),
    'copyFailed' => GI18n::t('link.copy_failed'),
);

$mlOrder = 0;
$mlCard = function ($v) use ($mlEsc, &$mlOrder) {
    $attrs = ' class="ml-card is-' . $v['kind'] . '"';
    if ($v['url'] !== '') {
        $attrs .= ' href="' . $mlEsc($v['url']) . '" target="_blank" rel="noopener"';
    }
    if ($v['description'] !== '') {
        $attrs .= ' title="' . $mlEsc($v['description']) . '"';
    }
    $attrs .= ' data-i="' . (int) $v['index'] . '" data-kind="' . $v['kind'] . '"'
        . ' data-group="' . $mlEsc($v['group']) . '" data-label="' . $mlEsc($v['label']) . '"'
        . ' data-prio="' . ($v['priority'] === null ? '' : (int) $v['priority']) . '"'
        . ' style="--ml-hue:' . (int) $v['hue'] . ';--ml-i:' . min($mlOrder++, 24) . '"';

    $img = '';
    if ($v['image'] !== '') {
        // data-g-nolazy keeps G.js from swapping in its loading spinner;
        // the monogram underneath shows until the image is ready.
        $img = '<img src="' . $mlEsc($v['image']) . '" alt="" width="64" height="64" loading="lazy"'
            . ' decoding="async" referrerpolicy="no-referrer" data-g-nolazy'
            . ' onload="this.parentNode.classList.add(\'is-loaded\')" onerror="this.remove()">';
    }

    return '<a' . $attrs . '>'
        . '<span class="ml-avatar" aria-hidden="true"><span class="ml-initial">' . $mlEsc($v['initial']) . '</span>' . $img . '</span>'
        . '<span class="ml-text" data-g-translate>'
        . '<span class="ml-name">' . $mlEsc($v['name']) . '</span>'
        . ($v['description'] !== '' ? '<span class="ml-desc">' . $mlEsc($v['description']) . '</span>' : '')
        . ($v['host'] !== '' ? '<span class="ml-host" translate="no">' . $mlEsc($v['host']) . '</span>' : '')
        . '</span>'
        . '<svg class="ml-go" viewBox="0 0 16 16" aria-hidden="true"><path d="M5.5 4.5h6v6M11.5 4.5l-7 7"/></svg>'
        . '</a>';
};

$mlSection = function ($kind, $title, $items) use ($mlEsc, $mlCard) {
    $html = '<section class="ml-section is-section-' . $kind . '">'
        . '<h2 class="ml-section-head"><span class="ml-section-title">' . $mlEsc($title) . '</span>'
        . '<span class="ml-section-rule" aria-hidden="true"></span>'
        . '<span class="ml-section-count" translate="no">' . count($items) . '</span></h2>'
        . '<div class="ml-grid">';
    foreach ($items as $item) {
        $html .= $mlCard($item);
    }
    return $html . '</div></section>';
};
?>

<style>
    /* ===== Modern Link page (scoped). Dark tokens live in modern-dark.css. ===== */
    #link-modern {
        --ml-accent: var(--theme-color, #3399ff);
        --ml-ink: #1b1e24;
        --ml-ink-2: #535a65;
        --ml-ink-3: #8a909b;
        --ml-card: rgba(255, 255, 255, 0.8);
        --ml-card-hover: #fff;
        --ml-line: rgba(17, 24, 39, 0.08);
        --ml-line-2: rgba(17, 24, 39, 0.15);
        --ml-field: rgba(17, 24, 39, 0.045);
        --ml-thumb: #fff;
        --ml-shadow: 0 1px 2px rgba(17, 24, 39, 0.04);
        --ml-shadow-hover: 0 1px 2px rgba(17, 24, 39, 0.04), 0 16px 32px -16px rgba(17, 24, 39, 0.3);
        --ml-avatar-bg: #fff;
        --ml-mono-s: 58%;
        --ml-mono-l: 93%;
        --ml-mono-ink: 34%;
        --ml-todo: #b7791f;
        --ml-lost: #c0505e;
        --ml-radius: 16px;
        --ml-font: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "PingFang TC", "Hiragino Sans", "Microsoft JhengHei", "Noto Sans CJK TC", sans-serif;
        --ml-serif: "Noto Serif TC", "Noto Serif CJK TC", "Source Han Serif TC", "Source Han Serif", "Songti TC", "STSong", "Yu Mincho", "Hiragino Mincho ProN", "PMingLiU", serif;
        --ml-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace;
        --ml-ease: cubic-bezier(0.2, 0.7, 0.2, 1);
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    /* The wrapper carries .PAP only for G.js page transitions; it draws nothing.
       The fade-in runs on each panel: an ancestor with opacity < 1 would switch
       off the panels' backdrop blur until the animation ends. */
    #link-modern.PAP {
        background: none;
        border: 0;
        border-radius: 0;
        box-shadow: none;
        overflow: visible;
        -webkit-backdrop-filter: none;
        backdrop-filter: none;
        animation: none;
    }

    #link-modern .ml-panel {
        position: relative;
        border-radius: var(--theme-radius, 30px);
        background: var(--glass-bg, rgba(255, 255, 255, 0.65));
        -webkit-backdrop-filter: var(--glass-blur, blur(12px)) saturate(180%);
        backdrop-filter: var(--glass-blur, blur(12px)) saturate(180%);
        border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.3));
        box-shadow: var(--glass-shadow, 0 8px 32px rgba(31, 38, 135, 0.15));
        animation: opacity-in var(--theme-animation-in-duration, 1s) ease;
    }

    /* ----- Head ----- */
    #link-modern .ml-head {
        position: relative;
        padding: 2.4rem 2rem 1.6rem;
    }

    #link-modern .has-banner .ml-head {
        display: flex;
        align-items: flex-end;
        min-height: 13.5rem;
        padding: 0;
        overflow: hidden;
        border-radius: var(--theme-radius, 30px) var(--theme-radius, 30px) 0 0;
        background: #262b36 linear-gradient(135deg, rgba(255, 255, 255, 0.08), rgba(0, 0, 0, 0.2));
    }

    #link-modern .has-banner:not(.has-content) .ml-head {
        border-radius: var(--theme-radius, 30px);
    }

    #link-modern .ml-banner {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0;
        transform: scale(1.03);
        transition: opacity 0.9s ease, transform 1.6s var(--ml-ease);
    }

    #link-modern .ml-banner.is-loaded {
        opacity: 1;
        transform: none;
    }

    #link-modern .has-banner .ml-head::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(12, 14, 20, 0.66) 0%, rgba(12, 14, 20, 0.2) 48%, rgba(12, 14, 20, 0) 78%);
        pointer-events: none;
    }

    #link-modern .ml-head-text {
        position: relative;
        z-index: 1;
        min-width: 0;
    }

    #link-modern .has-banner .ml-head-text {
        padding: 1.6rem 2rem;
    }

    #link-modern .ml-eyebrow {
        margin: 0 0 0.55rem;
        font: 600 0.68rem/1.2 var(--ml-font);
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--ml-ink-3);
    }

    #link-modern .ml-title {
        margin: 0;
        font-family: var(--ml-serif);
        font-size: 2.15rem;
        font-weight: 600;
        line-height: 1.2;
        letter-spacing: 0.06em;
        color: var(--ml-ink);
    }

    #link-modern .has-banner .ml-eyebrow {
        color: rgba(255, 255, 255, 0.78);
    }

    #link-modern .has-banner .ml-title {
        color: #fff;
        text-shadow: 0 1px 14px rgba(0, 0, 0, 0.28);
    }

    /* Content-language selector from translate.js lands here. */
    #link-modern .ml-head-bar {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 2;
    }

    #link-modern .ml-head-bar:empty {
        display: none;
    }

    #link-modern .ml-head-bar .content-lang-selector {
        margin: 0;
        padding: 0.3rem 0.35rem 0.3rem 0.7rem;
        gap: 0.35rem;
        border-radius: 999px;
        font-size: 0.75rem;
    }

    #link-modern .ml-head-bar .content-lang-label {
        font-size: 0.8rem;
    }

    #link-modern .ml-head-bar .content-lang-select {
        border-radius: 999px;
        font-size: 0.72rem;
    }

    /* ----- Page body (rendered with the theme's article typography) ----- */
    #link-modern .ml-content {
        margin-bottom: 0;
        padding: 0.2rem 0.5rem 0.4rem;
    }

    #link-modern .ml-notes .ml-content {
        padding-top: 0.6rem;
    }

    #link-modern .ml-content pre {
        position: relative;
        white-space: pre-wrap;
        word-break: break-word;
        border-radius: 12px;
    }

    #link-modern .ml-content pre code {
        white-space: inherit;
        word-break: inherit;
    }

    #link-modern .ml-copy {
        position: absolute;
        top: 0.55rem;
        right: 0.55rem;
        z-index: 1;
        padding: 0.38rem 0.65rem;
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
        color: rgba(255, 255, 255, 0.84);
        font: 600 0.66rem/1 var(--ml-font);
        letter-spacing: 0.04em;
        cursor: pointer;
        opacity: 0;
        transition: opacity 0.2s ease, background-color 0.2s ease;
    }

    #link-modern .ml-content pre:hover .ml-copy,
    #link-modern .ml-copy:focus-visible,
    #link-modern .ml-copy.is-done {
        opacity: 1;
    }

    #link-modern .ml-copy:hover {
        background: rgba(255, 255, 255, 0.16);
    }

    #link-modern .ml-content .collapse-box {
        border-color: var(--ml-line-2);
        border-radius: 14px;
    }

    #link-modern .ml-content .collapse-title {
        background: var(--ml-field);
    }

    /* ----- Board ----- */
    #link-modern .ml-board {
        padding: 1.3rem 1.5rem 1.7rem;
        font-family: var(--ml-font);
    }

    #link-modern .ml-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.7rem;
        margin: 0 0 1.4rem;
    }

    #link-modern .ml-toolbar[hidden],
    #link-modern .ml-seg[hidden],
    #link-modern .ml-section[hidden],
    #link-modern .ml-card[hidden],
    #link-modern .ml-empty[hidden] {
        display: none;
    }

    #link-modern .ml-search {
        position: relative;
        display: flex;
        align-items: center;
        flex: 1 1 14rem;
        max-width: 20rem;
        margin: 0;
    }

    #link-modern .ml-search > svg {
        position: absolute;
        left: 0.8rem;
        width: 0.85rem;
        height: 0.85rem;
        color: var(--ml-ink-3);
        pointer-events: none;
    }

    #link-modern .ml-search input {
        width: 100%;
        height: 2.15rem;
        margin: 0;
        padding: 0 2.3rem 0 2.1rem;
        border: 1px solid var(--ml-line);
        border-radius: 999px;
        background: var(--ml-field);
        color: var(--ml-ink);
        font: 400 0.78rem/1 var(--ml-font);
        outline: none;
        box-shadow: none;
        -webkit-appearance: none;
        appearance: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    #link-modern .ml-search input::placeholder {
        color: var(--ml-ink-3);
        opacity: 1;
    }

    #link-modern .ml-search input::-webkit-search-cancel-button {
        -webkit-appearance: none;
    }

    #link-modern .ml-search input:focus {
        background: var(--ml-card-hover);
        border-color: var(--ml-line-2);
        border-color: color-mix(in srgb, var(--ml-accent) 55%, transparent);
        box-shadow: 0 0 0 3px rgba(51, 153, 255, 0.16);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--ml-accent) 18%, transparent);
    }

    #link-modern .ml-kbd {
        position: absolute;
        right: 0.65rem;
        padding: 0.16rem 0.38rem;
        border: 1px solid var(--ml-line-2);
        border-radius: 5px;
        color: var(--ml-ink-3);
        font: 500 0.62rem/1 var(--ml-mono);
        pointer-events: none;
        transition: opacity 0.2s ease;
    }

    #link-modern .ml-search:focus-within .ml-kbd,
    #link-modern .ml-search.has-value .ml-kbd {
        opacity: 0;
    }

    #link-modern .ml-clear {
        position: absolute;
        right: 0.35rem;
        display: grid;
        place-items: center;
        width: 1.45rem;
        height: 1.45rem;
        padding: 0;
        border: 0;
        border-radius: 50%;
        background: transparent;
        color: var(--ml-ink-3);
        cursor: pointer;
    }

    #link-modern .ml-clear:hover {
        background: var(--ml-field);
        color: var(--ml-ink);
    }

    #link-modern .ml-clear svg {
        width: 0.7rem;
        height: 0.7rem;
    }

    #link-modern .ml-search:not(.has-value) .ml-clear {
        display: none;
    }

    #link-modern .ml-result {
        color: var(--ml-ink-3);
        font: 500 0.72rem/1 var(--ml-font);
        font-variant-numeric: tabular-nums;
    }

    #link-modern .ml-seg {
        position: relative;
        display: inline-grid;
        grid-auto-flow: column;
        grid-auto-columns: 1fr;
        margin-left: auto;
        padding: 3px;
        border: 1px solid var(--ml-line);
        border-radius: 999px;
        background: var(--ml-field);
    }

    #link-modern .ml-seg-thumb {
        position: absolute;
        top: 3px;
        bottom: 3px;
        left: 3px;
        width: calc((100% - 6px) / 3);
        border-radius: 999px;
        background: var(--ml-thumb);
        box-shadow: 0 1px 2px rgba(17, 24, 39, 0.1), 0 0 0 1px var(--ml-line);
        transform: translateX(calc(var(--ml-seg-i, 0) * 100%));
        transition: transform 0.45s var(--ml-ease);
    }

    #link-modern .ml-seg button {
        position: relative;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.32rem;
        min-width: 4.3rem;
        height: 1.9rem;
        margin: 0;
        padding: 0 0.85rem;
        border: 0;
        border-radius: 999px;
        background: transparent;
        color: var(--ml-ink-2);
        font: 500 0.74rem/1 var(--ml-font);
        white-space: nowrap;
        cursor: pointer;
        -webkit-tap-highlight-color: transparent;
        transition: color 0.2s ease;
    }

    #link-modern .ml-seg button:hover,
    #link-modern .ml-seg button[aria-pressed="true"] {
        color: var(--ml-ink);
    }

    #link-modern .ml-seg button:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    #link-modern .ml-seg svg {
        width: 0.78rem;
        height: 0.78rem;
        transition: transform 0.45s var(--ml-ease);
    }

    #link-modern .ml-seg button.is-spun svg {
        transform: rotate(180deg);
    }

    #link-modern button:focus-visible,
    #link-modern .ml-search input:focus-visible {
        outline: 2px solid var(--ml-accent);
        outline-offset: 2px;
    }

    /* ----- Sections ----- */
    #link-modern .ml-section + .ml-section {
        margin-top: 1.9rem;
    }

    #link-modern .ml-section-head {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0 0 0.9rem;
        padding: 0;
        color: var(--ml-ink-3);
        font: 600 0.68rem/1.2 var(--ml-font);
        letter-spacing: 0.16em;
        text-transform: uppercase;
    }

    #link-modern .ml-section-rule {
        flex: 1 1 auto;
        height: 1px;
        background: var(--ml-line);
    }

    #link-modern .ml-section-count {
        letter-spacing: 0.04em;
        font-variant-numeric: tabular-nums;
    }

    #link-modern .is-section-pin .ml-section-head {
        color: var(--ml-accent);
    }

    #link-modern .is-section-todo .ml-section-head {
        color: var(--ml-todo);
    }

    #link-modern .is-section-lost .ml-section-head {
        color: var(--ml-lost);
    }

    #link-modern .ml-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 15.5rem), 1fr));
        gap: 0.75rem;
    }

    #link-modern .ml-board[data-columns] .ml-grid {
        grid-template-columns: repeat(var(--ml-cols), minmax(0, 1fr));
    }

    /* ----- Card ----- */
    #link-modern .ml-card {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        min-width: 0;
        padding: 0.85rem 0.95rem;
        border: 1px solid var(--ml-line);
        border-radius: var(--ml-radius);
        background: var(--ml-card);
        box-shadow: var(--ml-shadow);
        color: var(--ml-ink);
        text-decoration: none;
        transition: transform 0.35s var(--ml-ease), box-shadow 0.35s var(--ml-ease), border-color 0.25s ease, background-color 0.25s ease;
        animation: ml-rise 0.6s var(--ml-ease) backwards;
        animation-delay: calc(var(--ml-i, 0) * 22ms);
    }

    #link-modern.ml-settled .ml-card {
        animation: none;
    }

    #link-modern a.ml-card:hover {
        transform: translateY(-2px);
        border-color: var(--ml-line-2);
        border-color: color-mix(in srgb, var(--ml-accent) 40%, var(--ml-line));
        background: var(--ml-card-hover);
        box-shadow: var(--ml-shadow-hover);
    }

    #link-modern a.ml-card:active {
        transform: translateY(0);
        transition-duration: 0.12s;
    }

    #link-modern a.ml-card:focus-visible {
        outline: 2px solid var(--ml-accent);
        outline-offset: 2px;
    }

    #link-modern .ml-card.is-pin {
        background: color-mix(in srgb, var(--ml-accent) 7%, var(--ml-card));
        border-color: color-mix(in srgb, var(--ml-accent) 20%, var(--ml-line));
    }

    #link-modern .ml-avatar {
        position: relative;
        display: grid;
        flex: none;
        place-items: center;
        width: 2.55rem;
        height: 2.55rem;
        overflow: hidden;
        border-radius: 50%;
        background: hsl(var(--ml-hue, 210), var(--ml-mono-s), var(--ml-mono-l));
        transition: transform 0.35s var(--ml-ease), filter 0.3s ease, opacity 0.3s ease;
    }

    #link-modern .ml-avatar::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        box-shadow: inset 0 0 0 1px var(--ml-line);
        pointer-events: none;
    }

    #link-modern .ml-initial {
        color: hsl(var(--ml-hue, 210), 42%, var(--ml-mono-ink));
        font: 600 1rem/1 var(--ml-font);
        transition: opacity 0.3s ease;
    }

    #link-modern .ml-avatar img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0;
        transition: opacity 0.45s ease;
    }

    #link-modern .ml-avatar.is-loaded {
        background: var(--ml-avatar-bg);
    }

    #link-modern .ml-avatar.is-loaded img {
        opacity: 1;
    }

    #link-modern .ml-avatar.is-loaded .ml-initial {
        opacity: 0;
    }

    #link-modern a.ml-card:hover .ml-avatar {
        transform: scale(1.05);
    }

    #link-modern .ml-text {
        display: flex;
        flex: 1 1 auto;
        flex-direction: column;
        gap: 0.16rem;
        min-width: 0;
    }

    #link-modern .ml-name {
        overflow: hidden;
        padding-right: 1rem;
        font: 600 0.88rem/1.35 var(--ml-font);
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    #link-modern .ml-desc {
        display: -webkit-box;
        overflow: hidden;
        color: var(--ml-ink-2);
        font: 400 0.74rem/1.5 var(--ml-font);
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }

    #link-modern .ml-host {
        overflow: hidden;
        margin-top: 0.08rem;
        color: var(--ml-ink-3);
        font: 400 0.63rem/1.4 var(--ml-mono);
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    #link-modern .ml-go {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        width: 0.78rem;
        height: 0.78rem;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.6;
        stroke-linecap: round;
        stroke-linejoin: round;
        color: var(--ml-ink-3);
        opacity: 0;
        transform: translate(-3px, 3px);
        transition: opacity 0.25s ease, transform 0.35s var(--ml-ease), color 0.25s ease;
    }

    #link-modern a.ml-card:hover .ml-go,
    #link-modern a.ml-card:focus-visible .ml-go {
        opacity: 1;
        transform: none;
        color: var(--ml-accent);
    }

    #link-modern .ml-card.is-todo .ml-avatar {
        filter: saturate(0.55);
    }

    #link-modern .ml-card.is-lost .ml-avatar,
    #link-modern .ml-card.is-lost .ml-text {
        filter: grayscale(1);
        opacity: 0.6;
    }

    #link-modern .ml-card.is-lost:hover .ml-avatar,
    #link-modern .ml-card.is-lost:hover .ml-text,
    #link-modern .ml-card.is-todo:hover .ml-avatar {
        filter: none;
        opacity: 1;
    }

    /* ----- Empty states ----- */
    #link-modern .ml-empty,
    #link-modern .ml-notice {
        margin: 0;
        padding: 2.6rem 1rem;
        color: var(--ml-ink-2);
        font: 400 0.82rem/1.7 var(--ml-font);
        text-align: center;
    }

    #link-modern .ml-empty p {
        margin: 0 0 0.9rem;
    }

    #link-modern .ml-empty button {
        padding: 0.45rem 1rem;
        border: 1px solid var(--ml-line-2);
        border-radius: 999px;
        background: var(--ml-card);
        color: var(--ml-ink);
        font: 500 0.74rem/1 var(--ml-font);
        cursor: pointer;
    }

    #link-modern .ml-empty button:hover {
        background: var(--ml-card-hover);
    }

    @keyframes ml-rise {
        from {
            opacity: 0;
            transform: translateY(8px);
        }
    }

    /* ----- Small screens ----- */
    @media (max-width: 600px) {
        #link-modern {
            gap: 1.1rem;
        }

        #link-modern .ml-head {
            padding: 1.9rem 1.3rem 1.3rem;
        }

        #link-modern .has-banner .ml-head {
            min-height: 11rem;
            padding: 0;
        }

        #link-modern .has-banner .ml-head-text {
            padding: 1.2rem 1.3rem;
        }

        #link-modern .ml-head-bar {
            top: 0.75rem;
            right: 0.75rem;
        }

        #link-modern .ml-title {
            font-size: 1.75rem;
        }

        #link-modern .ml-content {
            padding: 0 0 1.2rem;
        }

        #link-modern .ml-board {
            padding: 1rem 1rem 1.3rem;
        }

        #link-modern .ml-search {
            flex-basis: 100%;
            max-width: none;
        }

        #link-modern .ml-seg {
            width: 100%;
            margin-left: 0;
        }

        #link-modern .ml-board[data-columns] .ml-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (hover: none) {
        #link-modern .ml-kbd {
            display: none;
        }

        #link-modern .ml-copy {
            opacity: 1;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        #link-modern .ml-panel,
        #link-modern .ml-card,
        #link-modern .ml-avatar,
        #link-modern .ml-go,
        #link-modern .ml-banner,
        #link-modern .ml-seg-thumb,
        #link-modern .ml-seg svg {
            animation: none;
            transition: none;
        }

        #link-modern a.ml-card:hover,
        #link-modern a.ml-card:hover .ml-avatar {
            transform: none;
        }
    }
</style>

<div id="link-modern" class="PAP ml" data-display="<?php echo $mlDisplay; ?>"
     data-ml-text="<?php echo htmlspecialchars(json_encode($mlText, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>"
     itemscope itemtype="http://schema.org/BlogPosting">
    <section class="ml-panel ml-hero<?php echo $mlHasBanner ? ' has-banner' : ''; ?><?php echo ($mlIntro === 'top' && $mlHasContent) ? ' has-content' : ''; ?>">
        <div class="ml-head">
            <?php if ($mlHasBanner): ?>
                <img class="ml-banner" src="<?php echo $mlEsc($mlBanner); ?>" alt="" decoding="async" fetchpriority="high"
                     data-g-nolazy onload="this.classList.add('is-loaded')">
            <?php endif; ?>
            <div class="ml-head-bar" data-g-translate-mount></div>
            <div class="ml-head-text">
                <?php if ($mlTotal > 0): ?>
                    <p class="ml-eyebrow"><?php echo $mlEsc(GI18n::t('link.eyebrow', $mlTotal)); ?></p>
                <?php endif; ?>
                <h1 class="ml-title" itemprop="name headline"><?php $this->title(); ?></h1>
            </div>
        </div>
        <?php if ($mlIntro === 'top' && $mlHasContent): ?>
            <div id="page-content" class="PAP-content ml-content" itemprop="articleBody">
                <?php echo $mlContent; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="ml-panel ml-board"<?php if ($mlColumns): ?> data-columns style="--ml-cols: <?php echo $mlColumns; ?>"<?php endif; ?>>
        <?php if ($mlLinks === null): ?>
            <p class="ml-notice"><?php GI18n::e('link.no_plugin'); ?></p>
        <?php elseif ($mlTotal === 0): ?>
            <p class="ml-notice"><?php GI18n::e('link.empty'); ?></p>
        <?php else: ?>
            <div class="ml-toolbar" hidden>
                <label class="ml-search">
                    <svg viewBox="0 0 16 16" aria-hidden="true"><circle cx="7" cy="7" r="4.75" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M10.5 10.5L14 14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                    <input type="search" autocomplete="off" spellcheck="false" enterkeyhint="search"
                           aria-label="<?php echo $mlEsc(GI18n::t('link.search')); ?>"
                           placeholder="<?php echo $mlEsc(GI18n::t('link.search.placeholder')); ?>">
                    <kbd class="ml-kbd" aria-hidden="true">/</kbd>
                    <button type="button" class="ml-clear" aria-label="<?php echo $mlEsc(GI18n::t('link.search.clear')); ?>">
                        <svg viewBox="0 0 16 16" aria-hidden="true"><path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </button>
                </label>
                <span class="ml-result" aria-live="polite" translate="no"></span>
                <div class="ml-seg" role="group" aria-label="<?php echo $mlEsc(GI18n::t('link.display')); ?>"<?php echo $mlDisplay === 'all' ? '' : ' hidden'; ?>>
                    <span class="ml-seg-thumb" aria-hidden="true"></span>
                    <button type="button" data-mode="flat" aria-pressed="true"><?php GI18n::e('link.display.flat'); ?></button>
                    <button type="button" data-mode="grouped" aria-pressed="false"><?php GI18n::e('link.display.grouped'); ?></button>
                    <button type="button" data-mode="random" aria-pressed="false"><svg viewBox="0 0 16 16" aria-hidden="true"><path d="M2 4.5h2.2c1.2 0 2.3.6 3 1.6l1.6 2.3c.7 1 1.8 1.6 3 1.6H14M2 11.5h2.2c1.2 0 2.3-.6 3-1.6M11.8 2.5L14 4.5l-2.2 2M11.8 9.5L14 11.5l-2.2 2M8.8 5.9c.7-.9 1.8-1.4 3-1.4H14" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg><?php GI18n::e('link.display.random'); ?></button>
                </div>
            </div>

            <div class="ml-sections">
                <?php
                if ($mlKinds['pin']) echo $mlSection('pin', $mlText['pinned'], $mlKinds['pin']);
                if ($mlKinds['link']) echo $mlSection('link', $mlText['links'], $mlKinds['link']);
                if ($mlKinds['todo']) echo $mlSection('todo', $mlText['todo'], $mlKinds['todo']);
                if ($mlKinds['lost']) echo $mlSection('lost', $mlText['lost'], $mlKinds['lost']);
                ?>
            </div>

            <div class="ml-empty" hidden>
                <p class="ml-empty-text" translate="no"></p>
                <button type="button" class="ml-empty-clear"><?php GI18n::e('link.search.clear'); ?></button>
            </div>
        <?php endif; ?>
    </section>

    <?php if ($mlIntro === 'bottom' && $mlHasContent): ?>
        <section class="ml-panel ml-notes">
            <div id="page-content" class="PAP-content ml-content" itemprop="articleBody">
                <?php echo $mlContent; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<script>
    (function () {
        'use strict';
        // Runs once per page view: on a full load as an inline script, and
        // after pjax navigation when pjax re-executes this block.
        var root = document.getElementById('link-modern');
        if (!root || root.getAttribute('data-ml-ready')) return;
        root.setAttribute('data-ml-ready', '1');

        var T = {};
        try { T = JSON.parse(root.getAttribute('data-ml-text') || '{}'); } catch (e) {}
        var reduceMotion = !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
        var slice = Array.prototype.slice;

        /* ----- Copy buttons for code blocks in the page body ----- */
        slice.call(root.querySelectorAll('.ml-content pre')).forEach(function (pre) {
            if (pre.querySelector('.ml-copy')) return;
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'ml-copy';
            button.textContent = T.copy || 'Copy';
            pre.appendChild(button);
        });

        function fallbackCopy(text) {
            var area = document.createElement('textarea');
            area.value = text;
            area.setAttribute('readonly', '');
            area.style.cssText = 'position:fixed;top:-1000px;opacity:0';
            document.body.appendChild(area);
            area.select();
            var ok = false;
            try { ok = document.execCommand('copy'); } catch (e) {}
            document.body.removeChild(area);
            return ok;
        }

        function copyText(text) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(text).then(function () { return true; }, function () { return fallbackCopy(text); });
            }
            return Promise.resolve(fallbackCopy(text));
        }

        // Delegated: translate.js may rebuild the page body's markup.
        root.addEventListener('click', function (event) {
            var button = event.target.closest && event.target.closest('.ml-copy');
            if (!button) return;
            var pre = button.closest('pre');
            if (!pre) return;
            var clone = pre.cloneNode(true);
            slice.call(clone.querySelectorAll('.ml-copy, .line-numbers-rows')).forEach(function (n) { n.remove(); });
            copyText(clone.textContent.replace(/\s+$/, '')).then(function (ok) {
                button.textContent = ok ? (T.copied || 'Copied') : (T.copyFailed || 'Copy failed');
                button.classList.add('is-done');
                clearTimeout(button._mlTimer);
                button._mlTimer = setTimeout(function () {
                    button.textContent = T.copy || 'Copy';
                    button.classList.remove('is-done');
                }, 1600);
            });
        });

        /* ----- Link board ----- */
        var board = root.querySelector('.ml-board');
        var list = board && board.querySelector('.ml-sections');
        if (!list) return;
        var cards = slice.call(list.querySelectorAll('.ml-card'));
        if (!cards.length) return;
        cards.sort(function (a, b) { return a.getAttribute('data-i') - b.getAttribute('data-i'); });

        var toolbar = board.querySelector('.ml-toolbar');
        var seg = board.querySelector('.ml-seg');
        var buttons = seg ? slice.call(seg.querySelectorAll('button[data-mode]')) : [];
        var searchBox = board.querySelector('.ml-search');
        var search = searchBox && searchBox.querySelector('input');
        var result = board.querySelector('.ml-result');
        var empty = board.querySelector('.ml-empty');
        var STORAGE_KEY = 'modernLinkDisplay';
        var configured = root.getAttribute('data-display') || 'all';
        var locked = configured === 'flat' || configured === 'grouped' || configured === 'random';

        var byKind = { pin: [], link: [], todo: [], lost: [] };
        var groups = [];
        var groupIndex = {};
        cards.forEach(function (card) {
            var kind = card.getAttribute('data-kind');
            if (!byKind[kind]) kind = 'link';
            byKind[kind].push(card);
            if (kind !== 'link') return;
            var key = card.getAttribute('data-group') || '';
            if (!groupIndex[key]) {
                var prio = card.getAttribute('data-prio');
                groupIndex[key] = {
                    key: key,
                    label: card.getAttribute('data-label') || key,
                    prio: prio === '' || prio === null ? Infinity : +prio,
                    order: groups.length,
                    items: []
                };
                groups.push(groupIndex[key]);
            }
            groupIndex[key].items.push(card);
        });
        groups.sort(function (a, b) { return (a.prio - b.prio) || (a.order - b.order); });
        var hasCategories = groups.length > 1 || (groups.length === 1 && groups[0].key !== '');

        function shuffle(items) {
            var a = items.slice();
            for (var i = a.length - 1; i > 0; i--) {
                var j = Math.floor(Math.random() * (i + 1));
                var t = a[i]; a[i] = a[j]; a[j] = t;
            }
            return a;
        }

        function buildSection(kind, title, items, translatable) {
            var section = document.createElement('section');
            section.className = 'ml-section is-section-' + kind;
            var head = document.createElement('h2');
            head.className = 'ml-section-head';
            var name = document.createElement('span');
            name.className = 'ml-section-title';
            if (translatable) name.setAttribute('data-g-translate', '');
            name.textContent = title;
            var rule = document.createElement('span');
            rule.className = 'ml-section-rule';
            rule.setAttribute('aria-hidden', 'true');
            var count = document.createElement('span');
            count.className = 'ml-section-count';
            count.setAttribute('translate', 'no');
            count.textContent = items.length;
            head.appendChild(name);
            head.appendChild(rule);
            head.appendChild(count);
            var grid = document.createElement('div');
            grid.className = 'ml-grid';
            items.forEach(function (card) { grid.appendChild(card); });
            section.appendChild(head);
            section.appendChild(grid);
            return section;
        }

        function buildLayout(mode) {
            var sections = [];
            if (byKind.pin.length) sections.push(buildSection('pin', T.pinned, byKind.pin));
            if (mode === 'grouped' && hasCategories) {
                groups.forEach(function (g) {
                    sections.push(buildSection('group', g.key === '' ? T.uncategorized : g.label, g.items, g.key !== ''));
                });
            } else if (byKind.link.length) {
                sections.push(buildSection('link', T.links, mode === 'random' ? shuffle(byKind.link) : byKind.link));
            }
            if (byKind.todo.length) sections.push(buildSection('todo', T.todo, byKind.todo));
            if (byKind.lost.length) sections.push(buildSection('lost', T.lost, byKind.lost));
            return sections;
        }

        // FLIP: measure, re-layout, then animate each card from where it was.
        function withFlip(mutate) {
            if (reduceMotion || !Element.prototype.animate) { mutate(); return; }
            var before = cards.map(function (c) { return c.hidden ? null : c.getBoundingClientRect(); });
            mutate();
            var viewport = window.innerHeight || 0;
            cards.forEach(function (card, i) {
                var a = before[i];
                if (!a || card.hidden) return;
                var b = card.getBoundingClientRect();
                // Skip cards that start and end off-screen: nobody sees them move.
                if ((a.bottom < 0 || a.top > viewport) && (b.bottom < 0 || b.top > viewport)) return;
                var dx = a.left - b.left;
                var dy = a.top - b.top;
                if (Math.abs(dx) < 1 && Math.abs(dy) < 1) return;
                card.animate([
                    { transform: 'translate(' + dx + 'px, ' + dy + 'px)' },
                    { transform: 'none' }
                ], { duration: 520, easing: 'cubic-bezier(0.2, 0.7, 0.2, 1)' });
            });
        }

        function render(mode, animate) {
            var apply = function () {
                var fragment = document.createDocumentFragment();
                buildLayout(mode).forEach(function (s) { fragment.appendChild(s); });
                list.textContent = '';
                list.appendChild(fragment);
                applyFilter();
                if (typeof window.refreshContentTranslation === 'function') {
                    window.refreshContentTranslation(list);
                }
            };
            if (animate) {
                root.classList.add('ml-settled');
                withFlip(apply);
            } else {
                apply();
            }
        }

        /* ----- Search ----- */
        var tsMap = null;
        function toSimplified(text) {
            if (!tsMap) {
                var data = window.ZHCONV_DATA;
                if (!data || !data.ts || typeof Map === 'undefined') return text;
                tsMap = new Map();
                var chars = Array.from(data.ts);
                for (var i = 0; i + 1 < chars.length; i += 2) tsMap.set(chars[i], chars[i + 1]);
            }
            var out = '';
            for (var k = 0; k < text.length; k++) {
                var ch = text.charAt(k);
                var mapped = tsMap.get(ch);
                out += mapped !== undefined ? mapped : ch;
            }
            return out;
        }

        function normalize(text) {
            text = String(text || '');
            if (text.normalize) text = text.normalize('NFKC');
            return toSimplified(text.toLowerCase());
        }

        function textOf(card, selector) {
            var el = card.querySelector(selector);
            return el ? el.textContent : '';
        }

        var haystack = new WeakMap();
        function haystackOf(card) {
            var value = haystack.get(card);
            if (value === undefined) {
                value = normalize([
                    textOf(card, '.ml-name'),
                    textOf(card, '.ml-desc'),
                    textOf(card, '.ml-host'),
                    card.getAttribute('data-label') || ''
                ].join('\n'));
                haystack.set(card, value);
            }
            return value;
        }

        var query = '';
        function applyFilter() {
            var raw = query.trim();
            var terms = raw ? normalize(raw).split(/\s+/) : [];
            var shown = 0;
            cards.forEach(function (card) {
                var text = terms.length ? haystackOf(card) : '';
                var hit = terms.every(function (t) { return text.indexOf(t) !== -1; });
                card.hidden = !hit;
                if (hit) shown++;
            });
            slice.call(list.querySelectorAll('.ml-section')).forEach(function (section) {
                var n = section.querySelectorAll('.ml-card:not([hidden])').length;
                section.hidden = n === 0;
                var count = section.querySelector('.ml-section-count');
                if (count) count.textContent = n;
            });
            if (empty) {
                empty.hidden = shown !== 0;
                var message = empty.querySelector('.ml-empty-text');
                if (!shown && message) message.textContent = (T.noResult || '%s').replace('%s', raw);
            }
            if (result) {
                result.textContent = raw ? (T.result || '%1$d / %2$d').replace('%1$d', shown).replace('%2$d', cards.length) : '';
            }
            if (searchBox) searchBox.classList.toggle('has-value', query !== '');
        }

        function setQuery(value) {
            query = value;
            if (search && search.value !== value) search.value = value;
            applyFilter();
        }

        if (search) {
            search.addEventListener('input', function () { setQuery(search.value); });
            search.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && search.value) {
                    event.preventDefault();
                    setQuery('');
                }
            });
        }
        slice.call(board.querySelectorAll('.ml-clear, .ml-empty-clear')).forEach(function (button) {
            button.addEventListener('click', function () {
                setQuery('');
                if (search) search.focus();
            });
        });

        // "/" focuses the search box, unless the visitor is typing elsewhere.
        if (!window.__mlSlashBound) {
            window.__mlSlashBound = true;
            document.addEventListener('keydown', function (event) {
                if (event.key !== '/' || event.ctrlKey || event.metaKey || event.altKey || event.defaultPrevented) return;
                var active = document.activeElement;
                var tag = active && active.tagName;
                if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || (active && active.isContentEditable)) return;
                var box = document.querySelector('#link-modern .ml-search input');
                if (!box || !box.offsetParent) return;
                event.preventDefault();
                box.focus();
            });
        }

        /* ----- Display modes ----- */
        var mode = 'flat';
        function pressButtons() {
            buttons.forEach(function (button, i) {
                var on = button.getAttribute('data-mode') === mode;
                button.setAttribute('aria-pressed', on ? 'true' : 'false');
                if (button.getAttribute('data-mode') === 'random') {
                    button.title = on ? (T.reshuffle || '') : '';
                }
                if (on) seg.style.setProperty('--ml-seg-i', i);
            });
        }

        function choose(next, animate) {
            if (next !== 'grouped' && next !== 'random') next = 'flat';
            if (next === 'grouped' && !hasCategories) next = 'flat';
            var reshuffle = next === 'random' && mode === 'random';
            if (next === mode && !reshuffle) return;
            mode = next;
            render(mode, animate);
            if (seg) pressButtons();
        }

        buttons.forEach(function (button) {
            var target = button.getAttribute('data-mode');
            if (target === 'grouped' && !hasCategories) {
                button.disabled = true;
                button.title = T.noCategories || '';
            }
            button.addEventListener('click', function () {
                if (target === 'random' && mode === 'random') {
                    button.classList.toggle('is-spun');
                }
                choose(target, true);
                try { localStorage.setItem(STORAGE_KEY, target); } catch (e) {}
            });
        });

        var initial = locked ? configured : null;
        if (!initial) {
            try { initial = localStorage.getItem(STORAGE_KEY); } catch (e) {}
        }
        // The server already rendered the flat layout.
        choose(initial || 'flat', false);
        if (seg) pressButtons();
        if (toolbar) toolbar.hidden = false;
        if (seg && locked) seg.hidden = true;

        // Entrance animations replay whenever a card is re-inserted; stop them
        // once the first one has played so mode switches only use FLIP.
        setTimeout(function () { root.classList.add('ml-settled'); }, 1400);
    })();
</script>

<?php if ($this->fields->enableComment == 1): ?>
    <?php $this->need('components/comments.php'); ?>
<?php endif; ?>
<?php $this->need('components/footer.php'); ?>
