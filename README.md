# Image Tag Analysis Module

**Version:** v1.5.0

This Drupal module uses AI to analyze uploaded images in content nodes (like Product or Article) and auto-tag them based on the visual content. It also provides slider-based matched product suggestions and AJAX-based re-analyze support.

---

## ✅ Features

- Auto-analyze uploaded images using OpenAI
- Extract product tags, brands, categories
- Create or match tags automatically
- Product slider block with configurable Swiper.js settings
- AJAX-based reanalyze button (no page reload)
- Settings forms for prompt, slider behavior, and CDN fallback
- Built-in fallback if CDN or S3 is not configured

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

### Article Image Prompt
```
You are an expert AI product image analyst.

Your task is to analyze the uploaded product image and return detailed metadata.

Steps:
1. Identify what type of product is shown (e.g., sneakers, football jersey, backpack, smartwatch, etc.).
2. Detect brand name, model (if known), product category, and visible design features.
3. Identify any sponsor logos, visible text, badges, patterns, or signature design elements.
4. Ensure your output is relevant, specific, and avoids general or vague terms.

Strict Rules:
- Do NOT use vague terms like "apparel", "clothing", or "unknown".
- Only include products or features that are clearly visible in the image.
- Avoid repetition. Each item must be unique.
- Be concise, but specific.
- Output in clean raw JSON (no markdown, no explanation).

Output format:

{
  "description": "Detailed description of what's seen in the image...",
  "items": [
    {
      "Product Name": "Nike Phantom GX 2 Elite",
      "Brand": "Nike",
      "Category": "Football Boots",
      "Features": "White and blue synthetic upper, asymmetric lacing, mesh tongue, bold Swoosh logo on side",
      "Potential Value": "Top-tier pro football boot"
    },
    {
      "Product Name": "Nike Swoosh Logo",
      "Brand": "Nike",
      "Category": "Logo",
      "Features": "Black Nike Swoosh on lateral side",
      "Potential Value": "Brand identity feature"
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

To place the matched product slider block on article pages:

1. Go to: `Structure` → `Block Layout`
2. Find the **Content** region (or your preferred region)
3. Click **Place block**
4. Search for: `Matched Products Slider`
5. Place and configure visibility as needed



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

