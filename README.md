# Stories

**Laravel Creative Multilingual Blog**

Stories is a clean and minimal Laravel blog script perfect for writers who need to create a personal blog site with simple creative features and effects to make readers feel the pleasure of reading blog posts and articles.

Packed with a fine assortment of adventure and travel blog templates, tons of exciting pre-designed layouts for all types of blogs.

Stories is a personal blog template mixes between modern, classic, and minimal styles and will help you create a simple and clean blog, if you are a blogger, then it’s a perfect choice.

## Feature

-   Page, blog, menu, contact… modules are provided with the use of components to avoid boilerplate code.
-   RSS feeds for posts
-   Multi language support. Unlimited number of languages.
-   SEO & sitemap support: access sitemap.xml to see more.
-   Powerful media system, also support Amazon S3, DigitalOcean Spaces
-   Google Analytics: display analytics data in admin panel.
-   Translation tool: easy to translate front theme and admin panel to your language.
-   Creative blog theme is ready to use.
-   Powerful Permission System: Manage user, team, role by permissions. Easy to manage user by permissions.
-   Admin template comes with color schemes to match your taste.
-   Fully Responsive: Compatible with all screen resolutions
-   Coding Standard: All code follow coding standards PSR-2 and best practices

## Requirements

-   Apache, nginx, or another compatible web server.
-   PHP >= 8.0 >> Higher
-   MySQL Database server
-   PDO PHP Extension
-   OpenSSL PHP Extension
-   Mbstring PHP Extension
-   Exif PHP Extension
-   Fileinfo Extension
-   XML PHP Extension
-   Ctype PHP Extension
-   JSON PHP Extension
-   Tokenizer PHP Extension
-   Module Re_write server
-   PHP_CURL Module Enable

## How to start

**1. Clone From Github**

```bash
git clone https://github.com/kothing/CMS-stories.git
```

**2. Go to that folder**

```bash
cd CMS-stories
```

**3. Required Configuration**
Permission for directories.

1. storage 777
1. bootstrap/cache 777
1. public 777

**4. Install Composer**

```php
composer install
```

**5. Create env file**

```bash
Create a .env file by cloning .env.example file
```

**6. Create a Database named**

```bash
stories
```

**7. Run Migration & Seed**

```php
php artisan migrate:fresh --seed
```

**10. Run On Local Machine**

```bash
php artisan serve
```

**11. Open Browser**

```bash
http://localhost:8000
```

**12. Go to CMS Portal**
Login Now by giving this data

```php
Username: admin
Password: 123456
```
