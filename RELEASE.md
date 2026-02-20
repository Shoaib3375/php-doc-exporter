# Release Instructions

## The Problem
The error shows you're installing v1.0.0 from Packagist, which has the old `composer.json` with `illuminate/support` as a required dependency.

## Solution: Release New Version

### Step 1: Commit Changes
```bash
git add .
git commit -m "Add Blade template support and make illuminate/support optional"
```

### Step 2: Tag New Version
```bash
git tag v1.1.0
git push origin main
git push origin v1.1.0
```

### Step 3: Wait for Packagist
Packagist will auto-update (if webhook is configured) or manually update at:
https://packagist.org/packages/shoaib3375/php-doc-exporter

### Step 4: Install New Version
```bash
composer require shoaib3375/php-doc-exporter:^1.1
```

---

## Alternative: Test Locally

### Option 1: Install from Local Path
In your test project's `composer.json`:
```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../php-doc-exporter"
        }
    ],
    "require": {
        "shoaib3375/php-doc-exporter": "*"
    }
}
```

Then run:
```bash
composer update shoaib3375/php-doc-exporter
```

### Option 2: Install from GitHub
In your test project's `composer.json`:
```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/shoaib3375/php-doc-exporter"
        }
    ],
    "require": {
        "shoaib3375/php-doc-exporter": "dev-main"
    }
}
```

---

## Quick Fix: Update Packagist Now

1. Go to: https://packagist.org/packages/shoaib3375/php-doc-exporter
2. Click "Update" button
3. Wait 1-2 minutes
4. Try installing again:
   ```bash
   composer require shoaib3375/php-doc-exporter
   ```
