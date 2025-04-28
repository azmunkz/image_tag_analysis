# Image Tag Analysis

This Drupal custom module uses AI (OpenAI Vision) to analyze uploaded images and auto-tag articles.

## Features
- Analyze images during article creation
- Create terms based on Product Name, Brand, and Category
- Configurable AI prompts via admin UI

## Example Prompt
```
Analyze the provided image and list clothing, logos, and accessories worn by the subject. Return JSON format:

[
  {
    "Product Name": "",
    "Brand": "",
    "Category": "",
    "Features": "",
    "Potential Value": ""
  }
]
Only return raw JSON.
```
