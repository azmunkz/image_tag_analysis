# Image Tag Analysis Module

**Version:** 2.1.1\
**Status:** Stable\
**Requires:** Drupal 10.x/11.x, OpenAI API Key, Internet access (for live tagging via OpenAI)

---

This Drupal module uses AI to analyze uploaded images in content nodes (like Product or Article) and auto-tag them based on the visual content. It also provides slider-based matched product suggestions with fallback product support and AJAX-based re-analyze capability.

---

## 📌 Overview

The **Image Tag Analysis** module provides automatic image-based tagging for content nodes in Drupal, powered by **OpenAI Assistant API**.

It can:
- Analyze uploaded images on nodes (e.g. `product_catalog`, `article`)
- Generate descriptive product tags using AI
- Automatically match tags to existing taxonomy terms (or create them optionally)
- Display product suggestions based on tag relevance or fallback products
- Supports both local and S3-hosted image sources
- Offers fallback handling and tag filtering options

---

## 🚀 New in v2.1.1

- Migrated to OpenAI **Assistants API**
- Handles **local + S3 image sources**
- **Configurable tag limit**
- Filters out irrelevant tags (e.g., containing "Sponsor")
- Supports re-analysis via form button or AJAX
- Automaticallyt renders fallback products if no matched tags found
- Logs tag processing and assistant calls
- Admin UI (WIP): tag limit, assistant ID, prompt refinement

---

## ⚙️ Installation Guide

### 1. Required Modules
Make sure the following modules are enabled before installation:

- `key`
- `taxonomy`
- `node`
- `block`
- `s3fs`
- `cdn`

These dependencies are auto-validated when enabling the module.

### 2. Content Type: **Product Catalog**

The module will automatically create this content type. Ensure these field configurations:

| Field                        | Field Name               | Type               | Configurations                                     |
|-----------------------------|--------------------------|--------------------|----------------------------------------------------|
| Product Image               | `field_product_image`    | Image              | Required                                           |
| Product Tags                | `field_product_tags`     | Term reference     | Unlimited. Vocabulary: `Product Tags`             |
| Product Category            | `field_product_category` | Term reference     | Unlimited. Vocabulary: `Product Category`         |
| Product Price               | `field_product_price`    | Decimal            | Optional                                           |
| External Link               | `field_product_external_link` | Link         | Optional                                           |
| Store Name                  | `field_product_store_name` | Text (plain)     | Optional                                           |
| AI Description              | `field_img_tag_analysis_desc` | Long text     | Optional                                           |

### 3. Content Type: **Article**

| Field                        | Field Name               | Type               | Configurations                                     |
|-----------------------------|--------------------------|--------------------|----------------------------------------------------|
| Image                       | `field_image`            | Image              | Required                                           |
| Product Tags (Matched)      | `field_image_product_tags` | Term reference  | Unlimited. Vocabulary: `Product Tags`             |

### 3. Configuration

You must configure:
- `openai_key`: via Drupal Key module
- `assistant_id`: hardcoded in `OpenAiAssistantService.php` (or optionally via admin config)
- Image field machine name: defaults to `field_product_image`
---

## 🧠 Suggested Prompt

### Product Image Analysis
```
You are an expert product image analyst.

Your task:
- Analyze the uploaded image.
- Describe in detail all visible elements such as: product name, brand, category, sponsor logos, sleeve badges, and product-specific visual patterns.
- Focus especially on shirt logos, emblems, and sponsor texts. These are important for tagging.

Strict Rules:
- DO NOT include vague or generic tags like "apparel", "clothing", "Unknown", "Not sure" unless there's truly no better match.
- Ensure each "Product Name" is unique.
- Do NOT repeat items even if wording differs.
- Prioritize specific brand/product over general category.

Return your result in clean raw JSON, no markdown, no comments. Format strictly as:

{
  "description": "Full detailed description of the image...",
  "items": [
    {
      "Product Name": "JDT 2024 Home Shirt",
      "Brand": "Nike",
      "Category": "Football Jersey",
      "Features": "Red and blue vertical stripes, club crest on chest, sponsor logos",
      "Potential Value": "Club official merchandise"
    },
    {
      "Product Name": "Daikin Sleeve Sponsor",
      "Brand": "Daikin",
      "Category": "Sponsorship Logo",
      "Features": "White Daikin logo on left sleeve",
      "Potential Value": "Official sponsor logo"
    }
  ]
}
```

---

## 🔑 Best Practices

- Always set the correct AI prompts in module settings page
- Use meaningful product images (clear, visible branding)
- Tag `product_catalog` nodes first to ensure accurate matching in `article` nodes
- Use Re-analyze if tagging result looks incomplete or outdated
- Enable CDN only if public access to image is required
- Ensure fallback products are configured in module settings

---

Ready to tag your content like a pro 🧠🔥


---

## 📦 Installation Steps

1. Navigate to your Drupal project directory.
2. Ensure folder exists: `web/modules/custom`
3. Clone the module into that folder:
   ```bash
   git clone [your-repo-url] web/modules/custom/image_tag_analysis
   ```
4. Enable the module:
   ```bash
   drush en image_tag_analysis -y
   ```

---

## 🧱 Displaying the Product Slider

To display matched or fallback product suggestion on article pages:

1. Go to: `Structure` → `Block Layout`
2. Find the **Content** region (or your preferred region)
3. Click **Place block**
4. Search for: `Matched Products Slider`
5. Place and configure visibility as needed

---

## 🧩 How Fallback Works

If an `article` node has no matching `product_catalog` based on image tags:

- The module automatically displays a fallback slider
- Fallback products are configurable in the admin settings
- Both matched and fallback logic are handled by `MatchedProductsBlock.php`

---

## 📦 Composer Installation (Dependencies)

If you're managing dependencies via Composer, run the following commands:

```bash
composer require 'drupal/key:^1.17'
composer require 'drupal/cdn:^5.0@alpha'
composer require 'drupal/s3fs:^3.7'
```

> Make sure these modules are enabled before installing Image Tag Analysis:
>
> - `key`
> - `cdn`
> - `s3fs`
> - `taxonomy`
> - `node`
> - `block`
---

## 📁 File Structure
```bash
.
├── CHANGELOG.md
├── INSTALL.md
├── README.md
├── TESTING.md
├── composer.json
├── config
│   ├── install
│   │   └── image_tag_analysis.settings.yml
│   └── schema
│       └── image_tag_analysis.schema.yml
├── css
│   └── matched-products-slider.css
├── image_tag_analysis.info.yml
├── image_tag_analysis.install
├── image_tag_analysis.libraries.yml
├── image_tag_analysis.links.menu.yml
├── image_tag_analysis.module
├── image_tag_analysis.routing.yml
├── image_tag_analysis.services.yml
├── js
│   ├── image_tag_trigger.js
│   └── matched-products-slider.js
├── src
│   ├── Controller
│   │   └── ImageTaggingController.php
│   ├── Form
│   │   ├── ImageTagAnalysisSettingsForm.php
│   │   └── SliderSettingsForm.php
│   ├── Plugin
│   │   └── Block
│   │       └── MatchedProductsBlock.php
│   └── Service
│       └── OpenAiAssistantService.php
└── templates
    └── matched-products-slider.html.twig
```

---


## ❗Troubleshooting

- 🔴 *"No assistant found with ID..."*
  → Check `assistant_id` in `OpenAiAssistantService.php`

- 🔴 *"Invalid image URL"*
  → Make sure the image is accessible via public URL (e.g., not localhost)

- 🔴 *"Run ID is null"*
  → Assistant not initialized correctly; check message creation success

---

## 🙋 Support

Need help? Contact module maintainer or open an issue in your internal GitLab/GitHub project.

---

## 📝 License

This module is released under the **GNU General Public License v2.0**.
See `/LICENSE.txt` in your Drupal root.
