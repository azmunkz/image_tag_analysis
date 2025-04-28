# Image Tag Analysis - Installation Guide

This module analyzes uploaded images in Drupal articles using AI (OpenAI Vision model), auto-generates descriptive tags and a vivid scene description.

---

## Requirements

- Drupal 10 or 11
- Composer
- Access to Private GitHub Repository: `https://github.com/azmunkz/image_tag_analysis`
- OpenAI API Key (configured in Drupal Admin)

---

## Installation Steps

### 1. Configure GitHub Authentication (if repository is private)

If this GitHub repository is private, you must set up a GitHub Personal Access Token (PAT) for Composer.

#### Generate GitHub Token:
- Go to GitHub ➔ Settings ➔ Developer Settings ➔ Personal Access Token (Fine-grained)
- Permissions required: `read:packages`, `repo`
- Copy the generated token

#### Configure Composer:

```bash
composer config --global --auth github-oauth.github.com <your_token_here>
