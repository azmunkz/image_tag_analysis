# 📦 Image Tag Analysis (v9.6 Rebuild)

Custom Drupal module to analyze uploaded images using AI and auto-generate smart tags and vivid descriptions.

---

## ✨ Features

- Auto-tag articles based on uploaded image contents
- Auto-generate vivid, detailed descriptions for images
- Manual **Re-analyze** button with Confirm Dialog for editors
- Automatic duplicate tag filtering (AI & Backend)
- Full compatibility with Gin Admin Theme
- Composer installable via Private GitHub Repository

---

## 🛠 Installation

1. Place the module into `modules/custom/`
2. Enable the module via Drupal Admin UI or using Drush:

```
drush en image_tag_analysis -y
```

3. Add the following fields to your **Article** content type:
   - `field_image_tag_analysis` (Taxonomy Term Reference - Vocabulary: `Image Tag Analysis`)
   - `field_image_tag_analysis_desc` (Long Text)

4. Install and configure the **Key** module:
   - Create a new Key named `openai_key`
   - Store your OpenAI API Key securely.

5. Set your custom AI Prompt under:
   - **Admin UI** ➔ Configuration ➔ Content Authoring ➔ **Image Tag Analysis Settings**

---

## 📋 Example AI Prompt (Recommended)

Use the following example prompt for best results:

```
You are an expert image analyst.

First, provide a detailed, vivid description of the image, explaining the full scene, activities, outfits, brands, logos, visible texts.

Second, generate a list of only unique products and items you can identify.

Important strict rules:
- Each "Product Name" MUST be unique.
- If a product appears twice (even under slightly different description), list it only once.
- Do NOT include general or vague terms ("apparel", "footwear") if a more specific product is available.
- Prioritize brand and product over generic categories.
- If uncertain about brand, use "Unknown".

Format strictly as raw JSON:

{
  "description": "Full vivid scene description...",
  "items": [
    {
      "Product Name": "",
      "Brand": "",
      "Category": "",
      "Features": "",
      "Potential Value": ""
    }
  ]
}

Do NOT include any markdown. Only clean valid JSON.
```

---

## 📦 Composer Installation (optional)

If you want to install the module via Composer:

1. Make sure your `composer.json` has the private repository defined:

```
"repositories": [
  {
    "type": "vcs",
    "url": "https://github.com/azmunkz/image_tag_analysis"
  }
]
```

2. Then run:

```
composer require kanda/image_tag_analysis:^1.0
```

---

## 🛡 Best Practices

- Recommended to use **gpt-4o** model for higher accuracy.
- Use high-quality images with visible logos/texts.
- Always clear cache (`drush cr`) after module installation.
- Avoid generic prompts; use vivid and specific language.

---

# 🚀 Enjoy Smart AI-Powered Image Tagging for your Drupal Articles!
