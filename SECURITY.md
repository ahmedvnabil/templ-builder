# Security Policy

We take the security of **Templ Builder** seriously. If you believe you have discovered a vulnerability, please read this policy to report it responsibly.

---

## Supported Versions

Only the latest release version is actively supported with security hotfixes and patches:

| Version | Supported          |
| ------- | ------------------ |
| 0.1.x   | :white_check_mark: |

---

## 🔒 Security Principles of this Plugin

To prevent attacks (such as SQL Injection, CSRF, and XSS), this plugin adheres to the following principles:

1. **Sanitize on Save**: All custom post type metadata inputs are sanitized before database storage using configurations mapped in `helpers.php` (e.g. `sanitize_text_field`, `esc_url_raw`, `absint`, `sanitize_key`).
2. **Escape on Render**: Data retrieved from the database is escaped inside templates immediately before display using `esc_html`, `esc_attr`, `esc_url`, or `wp_kses_post`.
3. **Nonce-Protected Admin Actions**: Nonces protect all form submissions and state alterations (saving cards, running seeder operations, importing/exporting configurations) to block CSRF exploits.
4. **Draft-by-Default JSON Imports**: Imported posts default to `'draft'` status. Publishing them immediately on import requires an explicit checkbox confirmation and executes a capability check for `publish_posts`. Under no circumstances is the `post_status` inside the JSON file trusted.
5. **No External APIs by Default**: We do not load external third-party API tracking scripts, styles, or tracking cookies. All layouts are simulated locally, making the plugin highly performant and secure.
6. **Custom CSS Setting Safety**: The custom CSS setting uses a strict sanitizer `tb_sanitize_custom_css()` that strips HTML brackets and tags completely (`wp_strip_all_tags`), preventing script breakout from enqueued `<style>` tags.

---

## 💬 How to Report a Vulnerability

Please do not report security vulnerabilities via public GitHub issues. Instead, report them confidentially:

* **Email**: Please email security concerns to `security@example.com` (replace with your security email address).
* **Details**: Include a detailed description of the vulnerability, steps to reproduce, and a proof of concept (PoC) if available.
* **Timeline**: We will acknowledge receipt of your report within 48 hours and work with you to test and deploy a hotfix before public disclosure.
