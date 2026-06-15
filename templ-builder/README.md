# Templ Builder

> Reusable structured content templates in WordPress using custom fields, shortcodes, and lightweight visual layouts.

**Templ Builder** is a lightweight, zero-dependency WordPress plugin that lets administrators create structured content items and render them using pre-built visual templates. It acts as an inline, 11ty-style template content manager directly within WordPress.

---

## 🌟 Features

- **CPT Content Store**: Define modular content cards, FAQs, tool profiles, testimonials, and portfolios.
- **Taxonomy Organization**: Group items by Type (`templ_type`) and Collection (`templ_collection`).
- **Unified Schema Config**: Centralized field definitions (Eyebrows, Subtitles, Accent Colors, primary/secondary CTAs).
- **JSON Custom Fields**: Validate, format, and store custom structured JSON payloads.
- **13 Built-in Visual Layouts**: Pre-packaged templates including SaaS cards, portfolios, accordions, testimonials, and features.
- **Live Shortcode Builder**: Configure attributes visually and copy the dynamically generated shortcode.
- **Import/Export JSON**: Easily back up or migrate content collections.
- **Static HTML Snippet Exporter**: Export layout structures directly as plain HTML files.
- **Production-Ready Security**: Strict nonce validations, capability verification, safe escaping on render, and draft-by-default JSON imports.
- **RTL & Accessibility Compliant**: Fully RTL-friendly for Arabic websites and WCAG accessibility-ready.

---

## 📂 Layout Templates List

1. `card`: Simple structured card.
2. `cards-grid`: Clean SaaS product or directory grid.
3. `list`: Bulleted index or compact resources list.
4. `feature-section`: Grid layout for product benefit features.
5. `landing-section`: Left/right content/image split layouts.
6. `testimonial`: Blockquotes, avatars, and star ratings.
7. `faq`: Interactive summary/details accordion.
8. `portfolio-card`: Image-heavy grid layout for projects.
9. `app-card`: App logo, pricing, stack, and CTA cards.
10. `service-card`: Standard offerings with audience specs and price points.
11. `social-proof`: Compact masonry-style quote walls.
12. `minimal`: Plain text compact layout.
13. `single`: Full detail layout for individual item showcases.

---

## 🚀 Shortcode Parameters Table

Use `[templ]` to display items. Override options inline:

| Attribute | Default | Description |
| :--- | :--- | :--- |
| `id` | `""` | Query a specific post ID. |
| `type` | `""` | Filter by type slug(s) (comma-separated). |
| `collection`| `""` | Filter by collection slug(s) (comma-separated). |
| `template` | `""` | Set visual template key (e.g. `cards-grid`). |
| `limit` | (setting)| Max posts to query. |
| `columns` | (setting)| Grid column count (1 to 6). |
| `order` | `DESC` | Query order (`ASC`, `DESC`). |
| `orderby` | `date` | Sort parameters (e.g. `title`, `menu_order`). |
| `featured` | `""` | Filter by featured flag (`1` or `0`). |
| `status` | `active`| Filter by template status meta value. |
| `class` | `""` | Custom wrapper CSS classes. |

---

## 🔧 Installation

1. Download the plugin folder `templ-builder`.
2. Upload the folder to your WordPress plugins directory: `/wp-content/plugins/`.
3. Go to **Plugins** in the WordPress Admin dashboard and activate **Templ Builder**.
4. Access **Tools > Templ Builder** to generate initial demo items.

---

## 🔒 Security & Accessibility Notes

- **Data Safety**: All database insertions undergo deep type-specific sanitizations. Output renderers use strict context-aware escaping (`esc_html`, `esc_url`, `esc_attr`, and `wp_kses_post`).
- **Backup Controls**: JSON imports set status to `draft` by default, requiring administrators to review them before publishing. Immediate publishing is nonce-protected and checks for the `publish_posts` capability.
- **A11y Standards**: Focus visible rings are colored dynamically using accent colors. Image-only link tags enforce screen-reader text visibility helper `.tb-sr-only`.
