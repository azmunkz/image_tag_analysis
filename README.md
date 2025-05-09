# Image Tag Analysis Module

**Latest Version**: v1.4.0

## 🧩 Features

- Automatically tag **product images** using OpenAI's GPT-4o
- Analyze **articles** to match existing product tags (no new tag creation)
- Supports both **manual re-analyze button** and **automatic AJAX tagging**
- Product slider block to show matched products inside article view
- Fully supports S3 CDN-hosted images and local file system
- Admin-configurable prompt and CDN domain settings

---

## 🛠 Installation Guide

### Required Content Types:
1. **product_catalog**
  - `field_product_image` (Image, required)
  - `field_product_tags` (Term Reference → `product_tags` vocabulary)
  - `field_product_store_name` (Text)
  - `field_product_external_link` (Link)

2. **article**
  - `field_image` (Image, required)
  - `field_image_product_tags` (Term Reference → `product_tags` vocabulary)

### Required Taxonomy:
- `product_tags` (Vocabulary)

### Required Drupal Modules:
- `key`
- `taxonomy`
- `s3fs` (if using S3)
- `image_tag_analysis` (this module)

---

## 🧠 Suggested Prompts

### 🎯 AI Prompt for Product Image Analysis

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

### 📰 AI Prompt for Article Image Tagging

```
You are an expert AI product image analyst.

Your task is to analyze the uploaded product image and return detailed metadata.

Steps:
1. First, identify what type of product is shown (e.g., sneakers, football jersey, backpack, smartwatch, etc.).
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

## ✅ Best Practices

- Ensure all product images are clear, close-up, and free from excessive noise.
- Avoid uploading watermarked or blurred images for better AI results.
- Maintain a curated list of taxonomy terms under `product_tags` to ensure consistent tagging.
- Use **Re-analyze** only when needed to reduce OpenAI token usage.
- Always validate AI output and edit manually if needed.
