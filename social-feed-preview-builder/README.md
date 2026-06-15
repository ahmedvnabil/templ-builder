# Social Feed Preview Builder

**Social Feed Preview Builder** is a lightweight, secure, and RTL-optimized WordPress plugin designed to create mock social media feeds and post previews (inspired by Facebook, Instagram, X/Twitter, LinkedIn, TikTok, and YouTube) completely from the WordPress admin panel. 

The plugin **does not make any real network requests, does not use external API keys, does not require React, and does not require build tools.** It is built purely using WordPress standards with PHP, HTML5, CSS, and Vanilla JavaScript.

---

## 🚀 Key Features

* **Visual Mockup Engine**: Inspired templates for Facebook, Instagram, X, LinkedIn, TikTok, YouTube, and a Generic custom card.
* **No Real Platform Dependencies**: Lightweight layouts that avoid slow external tracking scripts or API integrations.
* **Flexible Shortcode**: Embed card grids using `[social_feed_preview]` anywhere on your site.
* **Centralized Configuration**: All meta fields and plugin options are defined in a central array configuration, making it trivial to extend or modify.
* **RTL & Arabic Optimization**: Built-in styling for Arabic typography and right-to-left layout directions.
* **Secure and Production Ready**: Complete protection with security nonces, sanitization on save, escaping on render, and user capability checks.
* **Accessibility (a11y) Compliant**: Described images, semantic markup, focus rings, high contrast, and keyboard navigation support out-of-the-box.

---

## 📂 Plugin Structure

```text
social-feed-preview-builder/
  ├── social-feed-preview-builder.php   # Main plugin entry file
  ├── README.md                         # Documentation
  ├── assets/
  │   ├── css/
  │   │   ├── admin.css                 # Admin styling (Gutenberg & Tools)
  │   │   └── frontend.css              # Cards layout, responsive grid, focus rings, a11y
  │   └── js/
  │       ├── admin.js                  # Field toggler, copy-to-clipboard, wp.media integration
  │       └── frontend.js               # Optional play/pause and like mockup triggers
  ├── includes/
  │   ├── class-plugin.php              # Plugin bootstrap & assets registry
  │   ├── class-cpt.php                 # Post type and custom fields registration
  │   ├── class-shortcode.php           # Shortcode execution, fallbacks, dynamic CSS variables
  │   ├── class-admin.php               # Tools page, settings options, seeder, import/export
  │   ├── class-renderer.php            # Core HTML renderer and SVG generator
  │   └── helpers.php                   # Central fields configuration, option getters & formatters
  └── templates/
      ├── admin-meta-box.php            # Admin meta fields HTML structure
      └── feed-card.php                 # Card layout template with inline SVG icons & a11y labels
```

---

## 🛠️ Installation Steps

1. **Upload the Plugin**:
   - Compress the `social-feed-preview-builder` folder into a `.zip` archive.
   - Go to your WordPress Dashboard > **Plugins > Add New > Upload Plugin**.
   - Select the `.zip` file and click **Install Now**.
2. **Activate the Plugin**:
   - Once installed, click **Activate Plugin**.
3. **Configure Settings**:
   - Go to **Settings > Social Feed Preview** to adjust card aesthetics (radius, shadow style, custom CSS) and default parameters.
4. **Generate Demo Content (Optional)**:
   - Go to **Tools > Social Feed Preview** in your WordPress sidebar.
   - Click **Generate Demo Feed Items** to load 7 inspired mockup posts.

---

## ⚙️ Settings Options (`Settings > Social Feed Preview`)

Configure global default behaviors and layouts in the Settings panel:
* **Default Cards Limit**: The number of cards to query if the `limit` attribute is omitted in the shortcode (default: `6`).
* **Default Grid Columns**: The columns to output if the `columns` attribute is omitted in the shortcode (default: `3`).
* **Enable Frontend JavaScript**: Toggle whether to load `frontend.js` for clientside mockup interactions (like toggling likes or clicking to play video). Disabling this removes the script payload; cards render and function correctly via native controls.
* **Enable Demo Tools**: Toggle the visibility of the Demo Seeder and Previewer cards on the Tools page (default: `true`).
* **Default Card Border Radius**: Adjust the corner rounding of cards globally (in pixels).
* **Default Box Shadow Style**: Select from `None`, `Soft`, `Medium`, or `Strong` to set the card depth dynamically.
* **Custom CSS Stylesheet Override**: A code editor text area to write custom CSS styles that load inline directly with the shortcode.

---

## 📝 Shortcode Guide & Examples

Use the shortcode `[social_feed_preview]` in Gutenberg Shortcode blocks, widgets, page builders, or theme files.

### Attributes Reference
* `platform`: Filter by platform. Options: `facebook`, `instagram`, `x`, `linkedin`, `tiktok`, `youtube`, `generic`. Supports comma-separated lists (e.g. `platform="instagram,tiktok"`).
* `content_type`: Filter by attachment type. Options: `text`, `image`, `video`, `link`. Supports comma-separated lists (e.g. `content_type="image,video"`).
* `campaign`: Filter by a specific campaign label (e.g. `campaign="summer-promo"`).
* `limit`: Maximum number of cards to display. **Falls back to Settings default limit if omitted.**
* `columns`: Number of columns on desktop. **Falls back to Settings default columns if omitted.**
* `order`: Sort order direction. Options: `DESC`, `ASC` (default: `DESC`).
* `orderby`: Parameter to order cards by (default: `date`). Can also use `rand` or `title`.

### Examples
* **Standard Grid (using fallback settings)**: `[social_feed_preview]`
* **Instagram Wall (9 cards override)**: `[social_feed_preview platform="instagram" limit="9"]`
* **Custom Campaign (2 columns override)**: `[social_feed_preview campaign="launch" columns="2"]`
* **Video YouTube Card**: `[social_feed_preview content_type="video" platform="youtube"]`

---

## 🛠️ Live Shortcode Builder & Portability (Tools Page)

Navigate to **Tools > Social Feed Preview** to access these features:

### 1. Live Shortcode Builder
Select platforms, content types, limits, and columns inside the dropdowns. The tool automatically generates the corresponding shortcode in real-time. Click **Copy Code** to copy it instantly to your clipboard.

### 2. Export Cards
Click **Download JSON Export** to download all published social feed cards in a single `.json` file. This is highly useful for migrating mock testimonials between staging and production sites.

### 3. Import Cards
Choose a previously exported `.json` file and click **Upload and Import**. 

> [!IMPORTANT]
> **Draft by Default**: To protect production sites, imported cards are created as **Drafts** by default, allowing administrators to review them before publishing. To publish them immediately on import, check the "Publish imported items immediately" box (requires the `publish_posts` capability).

During import, the plugin will:
* Verify the file structure.
* Validate that `platform` and `content_type` match allowed values.
* Sanitize all meta fields.
* Prevent duplication by checking if a post with the same title and platform already exists.

---

## ♿ Accessibility (a11y) Integration

The plugin follows web accessibility best practices:
* **Focus States**: Interactive items (cards, link previews, buttons, video elements) feature clear, high-contrast outline focus rings (`:focus-visible`) tailored to match platform accent colors.
* **Icon-Only Buttons**: Visual action buttons (like Instagram hearts/bubbles) contain hidden accessible labels via screen reader specific text classes (`.sfpb-sr-only`), ensuring screen reader users hear actions clearly.
* **Alternative Text**: Image attachment tags include descriptive alt attributes (e.g., `"Profile avatar of [Author]"` or `"Post image attachment by [Author]"`).
* **Color Contrast**: Card text colors (4.5:1 ratio) and focus ring styles meet WCAG guidelines.

---

## 🔒 Security Design

Security is prioritized at every stage of the lifecycle:
* **Direct File Access Prevention**: Every file begins with `defined( 'ABSPATH' ) || exit;` to prevent execution outside the WordPress application container.
* **Sanitize on Save**: User inputs are sanitized using appropriate core functions based on the field type config in `helpers.php` (e.g., `sanitize_text_field`, `esc_url_raw`, `absint`, `sanitize_key`).
* **Escape on Render**: All database outputs in templates are safely escaped when rendering using `esc_html`, `esc_attr`, `esc_url`, or `wp_kses_post`.
* **Nonces Protection**: Admin actions (saving a post CPT, generating/deleting demo items, exporting, importing) verify nonces (`sfpb_meta_box_nonce`, `sfpb_demo_nonce`, `sfpb_export_nonce`, `sfpb_import_nonce`) to block CSRF exploits.
* **Capability Checks**: Operations are restricted to authorized users. Saving metadata checks `current_user_can('edit_post', $post_id)`. Seeder, Settings saving, and Import/Export verify `current_user_can('manage_options')`.

---

## 🔍 Manual QA Checklist

- [ ] **Settings Save**: Save custom card radius (e.g., `0px` or `25px`) and shadows in *Settings > Social Feed Preview*. Confirm changes reflect on the frontend instantly.
- [ ] **Shortcode Fallbacks**: Set columns to `1` in settings. Insert `[social_feed_preview]` with no attributes; verify it renders in 1 column. Now insert `[social_feed_preview columns="3"]`; verify it overrides to 3.
- [ ] **Shortcode Builder**: Click options in the builder; check that text updates immediately and copy button copies the exact generated shortcode.
- [ ] **Accessibility Tabs**: Use the keyboard `Tab` key on the frontend page. Check that focus outlines wrap links and buttons with distinct, clear focus rings.
- [ ] **No-JS Compatibility**: Turn off "Enable Frontend JavaScript" in settings. Reload page; check that cards still display grid correctly and video plays via native controls without script errors.
- [ ] **JSON Portability**: Export posts to a JSON file. Delete all items. Import the file back; verify cards are restored accurately, sanitized, and duplicate entries are skipped.

---

## ⚠️ Known Limitations

1. **Interactive State Persistency**: Interactions on frontend cards (Likes click, Video Play) are visual mockup behaviors managed clientside. Likes clicked on the frontend do not update the actual database counts.
2. **Video Autoplay Limitations**: Browsers require user interaction before playing audio. Custom video mockup elements remain paused until hovered/clicked or configured to play muted.
3. **No External Live Sync**: The plugin does not sync with live social accounts. To show real posts, copy the text and image URLs from the social network and save them as a card in the plugin.

---

## 📜 Changelog

### v0.2 (Sprint 2)
* **Feature**: Added custom Settings panel under Settings > Social Feed Preview.
* **Feature**: Added interactive Live Shortcode Builder on the Tools page.
* **Feature**: Added JSON Export and JSON Import backup systems on the Tools page.
* **Feature**: Added card styling options (Radius slider, Shadow style intensity, Custom CSS overrides).
* **A11y**: Replaced display-hidden icon buttons with accessible screen reader texts. Added focus indicators for keyboard navigation.
* **Refactoring**: Centralized option configurations in helper configuration files. Added optional frontend script loading settings.

### v1.0.0 (Sprint 1)
* Initial Release. Custom Post Type `social_feed_item`, platform specific layout rendering (Facebook, Instagram, X, LinkedIn, TikTok, YouTube, Generic), seeder, and grid responsive shortcode.
