# Contributing to Templ Builder

We welcome contributions to the **Templ Builder** plugin! To maintain a lightweight, highly compatible, and secure codebase for the WordPress community, please review our development standards before opening a Pull Request (PR).

---

## 🛠️ Local WordPress Development Setup

1. **Local environment**: Use LocalWP, DevKinsta, Docker, or your preferred Apache/PHP/MySQL stack.
2. **Pathing**: Clone this repository into the `wp-content/plugins/` directory:
   ```bash
   cd /path/to/wordpress/wp-content/plugins/
   git clone https://github.com/your-username/templ-builder.git
   ```
3. **Debug logging**: Turn on debug mode in your local `wp-config.php` to capture warnings:
   ```php
   define( 'WP_DEBUG', true );
   define( 'WP_DEBUG_LOG', true );
   define( 'WP_DEBUG_DISPLAY', true );
   ```

---

## 📐 Coding & Naming Standards

* **WordPress Coding Standards**: Code must align with standard WordPress conventions (tabs for indentation, spacing inside parentheses, snake_case for functions/variables, and UPPERCASE for constants).
* **Strict Naming Prefix**: Everything must be namespaced or prefixed with `TB_` (for PHP classes), `tb_` (for functions/options/hooks), and `tb-` (for CSS classes and asset handles).
* **Security Auditing**:
  * Sanitize *on save* using helper configuration mappers.
  * Escape *on render* using `esc_html`, `esc_attr`, `esc_url`, or `wp_kses_post`.
  * Protect forms and actions using security nonces.
  * Direct file checks (`defined('ABSPATH') || exit;`) must start every PHP script.

---

## 🚫 Core Architecture Rule: No React, No Build Tools

To maintain high compatibility and minimal footprint, this plugin enforces:
1. **Plain Stack**: Only **PHP + HTML5 + CSS3 + Vanilla JavaScript** are permitted.
2. **No Build Steps**: Do not introduce webpack, Babel, gulp, npm compilation steps, or React components.
3. **No External Libraries**: Do not enqueue external icon sets (like FontAwesome) or brand logos. Draw minimal, inspired vector shapes inline using SVG elements instead.

---

## 🔍 Pre-PR Manual QA Checklist

Before submitting your PR, execute this checklist locally to confirm no regressions are introduced:
- [ ] **WordPress Debug Logs**: Run your code changes and inspect `wp-content/debug.log`; verify no PHP warnings, notices, or deprecation warnings are logged.
- [ ] **Asset Footprint Check**: Verify frontend scripts/styles load *only* on pages executing the shortcode.
- [ ] **RTL Validation**: Toggle your site language to Arabic. Verify layouts, text directions, and margins adjust accurately.
- [ ] **Accessibility Tabs**: Navigate using keyboard `Tab` keys on the card previews; check that focus borders adapt and screen reader texts (`.tb-sr-only`) describe all actions.
- [ ] **JavaScript Disabled**: Turn off JavaScript in settings; verify grids render and links/videos work via native HTML5 controls.
- [ ] **Backup Integrity**: Export and import card items; confirm that schema verification functions execute cleanly and duplicates are bypassed.
- [ ] **WP Sanitizer Check**: Save malicious scripts inside fields (e.g. `</style><script>alert(1)</script>`); confirm scripts are stripped out and XSS fails.
