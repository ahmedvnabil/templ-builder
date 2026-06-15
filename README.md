# Templ Builder

[![WordPress Version](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)](https://wordpress.org)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-GPL--2.0--or--later-orange.svg)](LICENSE)

**Templ Builder** is a beautiful, lightweight, and extensible WordPress plugin that lets administrators create structured content items and render them using reusable visual templates via a shortcode.

The plugin feels like a mini **11ty-style template/content system** inside WordPress, but operates with **zero external dependencies, zero build tools, no React, and no npm packages**. It is designed to work on any standard WordPress installation out-of-the-box and is fully RTL-ready and optimized for Arabic language sites.

---

## 🚀 Key Features

* **13 Visual Layout Templates**: Pre-configured templates (such as grids, pricing cards, split hero sections, FAQ accordions, and social walls).
* **Flexible Custom JSON Fields**: Support for structured variables enqueued in a simple, syntax-checked JSON editor in the metabox.
* **No Real Platform Dependencies**: Lightweight local styles that adapt to the active theme natively without remote requests.
* **Interactive Live Shortcode Builder**: Build customized shortcode strings dynamically inside the WordPress dashboard and copy them instantly.
* **JSON Portability Backup Systems**: Export and import Templ Items in a single JSON file. Bypasses duplicate imports.
* **Static HTML Snippet Exporter**: Download responsive, escaped, raw HTML block snippets to copy-paste into static pages.
* **RTL & Arabic Optimization**: Native support for Arabic typography, metadata adjustments, and right-to-left layouts.
* **Accessibility (a11y) Integrated**: Fully keyboard navigable (`:focus-visible`), aria-labeled, screen-reader friendly (`.tb-sr-only`), and compliant color contrasts.
* **Security-Reviewed**: Strict nonces, capability validation, draft-by-default imports, and Custom CSS HTML-tag stripping sanitizers.

---

## 📂 Repository File Tree
```text
templ-builder/
├── templ-builder.php                   # Main entry point (constants, loader)
├── README.md                           # Documentation, use cases, shortcodes table
├── CHANGELOG.md                        # Version history (v0.1.0 initial release)
├── LICENSE                             # GPL-2.0-or-later License
├── assets/
│   ├── css/
│   │   ├── admin.css                   # Admin tab switching & validation badges
│   │   └── frontend.css                # Premium responsive grid layouts (1-6 cols)
│   └── js/
│       ├── admin.js                    # Live builder, JSON checker, Media uploader
│       └── frontend.js                 # Progressive FAQ accordions
├── includes/
│   ├── helpers.php                     # Meta fields schemas, sanitizers, rating stars
│   ├── class-plugin.php                # Singleton bootstrapper & cache-busting assets
│   ├── class-cpt.php                   # CPT (templ_item), taxonomies, custom admin columns
│   ├── class-admin.php                 # Metabox rendering, tools post action routes
│   ├── class-settings.php              # Options API integrations (Settings > Templ Builder)
│   ├── class-shortcode.php             # Main shortcode [templ] handler & inline styles
│   ├── class-renderer.php              # Normalizer, KSES escaping, custom JSON formatter
│   ├── class-template-manager.php     # 13 templates metadata configs & validators
│   └── class-exporter.php              # JSON backup export and draft-by-default import
└── templates/
    ├── admin-meta-box.php              # Form inputs arranged under 6 tab sections
    ├── admin-tools-page.php            # Tools board with generator, seeder, import/export
    ├── admin-settings-page.php         # Options dashboard card form
    ├── frontend-card.php               # Cards wrapper, minimal, app, service styles
    ├── frontend-grid.php               # Grid container loop
    ├── frontend-list.php               # Link listing/resource logs
    ├── frontend-section.php            # Alternating landing sections
    ├── frontend-feature.php            # Grid of benefit items
    ├── frontend-testimonial.php        # Testimonials with rating stars
    ├── frontend-faq.php                # Progressive collapse FAQ accordion
    ├── frontend-portfolio.php          # Image-heavy project card
    └── frontend-single.php             # Single item detailed showcase
```

---

## 🛠️ Installation

1. **Download the ZIP**: Download the latest zip package `templ-builder-v0.1.0.zip` from the [GitHub Releases](https://github.com/ahmedvnabil/templ-builder/releases) section.
2. **Upload to WordPress**:
   - Go to your WordPress Dashboard > **Plugins > Add New > Upload Plugin**.
   - Select the downloaded `.zip` file and click **Install Now**.
3. **Activate**: Click **Activate Plugin**.
4. **Seed Demo (Optional)**:
   - Go to **Tools > Templ Builder** and click **Generate Demo Items** to populate mock items instantly.

---

## 📝 Shortcode Guide & Examples

Use the main `[templ]` shortcode to render items in your posts, pages, widgets, or visual builders.

### Attributes
| Attribute | Default | Description |
| :--- | :--- | :--- |
| `id` | `""` | Query a specific single Templ Item by ID. |
| `type` | `""` | Filter items by one or more `templ_type` term slugs (comma-separated). |
| `collection` | `""` | Filter items by one or more `templ_collection` term slugs (comma-separated). |
| `template` | `card` | The template style key to render (e.g. `cards-grid`, `testimonial`, `faq`). |
| `limit` | `6` | Number of items to query (capped by Settings Max Limit). |
| `columns` | `3` | Number of grid columns on desktop (1 to 6). |
| `orderby` | `date` | Query order parameter (`date`, `title`, `menu_order`, `rand`). |
| `order` | `DESC` | Sorting direction (`DESC` or `ASC`). |
| `status` | `active` | Filter items by layout status (`active`, `featured`, `coming-soon`, `draft`). |
| `featured` | `""` | Set to `1` or `true` to display only featured items. |
| `class` | `""` | Append a custom CSS class to the grid wrapper. |
| `show_empty` | `false` | If true, renders a fallback empty state message if no items match. |
| `full_width` | `no` | If yes, stretches the layout to the viewport width (breaks container width). |

### Examples
- **App Directory Grid**: `[templ type="app" template="cards-grid" columns="3" limit="6"]`
- **FAQ Accordions**: `[templ type="faq" template="faq"]`
- **SaaS Testimonials**: `[templ collection="landing-page" template="testimonial" limit="3" columns="3"]`
- **Full Split Section Showcase**: `[templ id="123" template="single"]`

---

## ⚙️ Settings Guide (`Settings > Templ Builder`)

Configure global defaults dynamically:
* **Default Accent Color**: Accent theme color enqueued inline (defaults to `#2563eb`).
* **Default Corner Border Radius**: Card corner rounding value in pixels (defaults to `16`).
* **Default Box Shadow Style**: Choose between `None`, `Soft shadow`, `Medium shadow`, or `Strong shadow` to customize depth.
* **Custom CSS Stylesheet Override**: Add custom styling declarations safely enqueued inline.
* **Enable Frontend JavaScript**: Choose whether to load `frontend.js` for progressive enhancements.

---

## 🛠️ Portability Guide (Import / Export)

### Exporting Cards
Click **Download Backup JSON** on the Tools page to download all published feed cards in a single JSON file.

### Importing Cards
1. Choose a previously exported JSON file.
2. Check or uncheck **Publish imported items immediately**.
3. Click **Upload and Restore Backup**.

> [!WARNING]
> **Draft by Default**: Imported cards default to `Draft` status for administrator inspection. Immediate publishing requires verifying the `publish_posts` capability. Under no circumstances is the `post_status` inside the JSON file trusted.

---

## ♿ Accessibility (a11y) Notes

The plugin implements:
* **Outlines**: Platforms feature high-visibility `:focus-visible` outline focus rings for tabbed keyboard access.
* **Icon Buttons**: Icon-only action items use standard screen-reader offset classes (`.tb-sr-only`) to ensure screen readers read button actions.
* **Alternative Text**: Dynamic avatar and attachment tags include descriptive alt tags.

---

## 🔒 Security Summary

* **Sanitize on Save**: Standard filters sanitize inputs on database inserts.
* **Escape on Render**: Template variables use safe rendering filters (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`).
* **Nonces Protection**: Nonces protect all form actions and endpoints.
* **CSS Tag Stripping**: Custom CSS settings strip HTML tags completely to prevent escaping inline `<style>` tags.

---

## 📜 License

This project is licensed under the GPL-2.0-or-later license. See the [LICENSE](LICENSE) file for details.
