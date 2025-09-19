# Being Blog — WordPress Project

![WordPress](https://img.shields.io/badge/WordPress-6.x-blue)
![PHP](https://img.shields.io/badge/PHP-%3E%3D%208.1-777bb3)
![Nginx](https://img.shields.io/badge/Nginx-Production-green)
![GitHub%20Actions](https://img.shields.io/badge/GitHub_Actions-CI%2FCD-black)
![License](https://img.shields.io/badge/License-MIT-lightgrey)

> Blog/Portfolio đa ngôn ngữ xây dựng trên WordPress. Dự án tập trung vào hiệu năng, SEO, CI/CD với GitHub Actions, và khả năng mở rộng (CPT, ACF, Polylang).

## Mục lục

- [Giới thiệu](#giới-thiệu)
- [Kiến trúc & Công nghệ](#kiến-trúc--công-nghệ)
- [Tính năng nổi bật](#tính-năng-nổi-bật)
- [Cấu trúc thư mục](#cấu-trúc-thư-mục)
- [Yêu cầu hệ thống](#yêu-cầu-hệ-thống)
- [Thiết lập môi trường local](#thiết-lập-môi-trường-local)
- [Cấu hình môi trường production](#cấu-hình-môi-trường-production)
- [Triển khai bằng GitHub Actions](#triển-khai-bằng-github-actions)
- [Quản lý database (local ⇄ server)](#quản-lý-database-local--server)
- [Đồng bộ ACF JSON](#đồng-bộ-acf-json)
- [Đa ngôn ngữ với Polylang](#đa-ngôn-ngữ-với-polylang)
- [Custom Post Type: Projects](#custom-post-type-projects)
- [SEO & Hiệu năng](#seo--hiệu-năng)
- [Bảo mật & Phân quyền](#bảo-mật--phân-quyền)
- [Sao lưu & Khôi phục](#sao-lưu--khôi-phục)
- [Khắc phục sự cố thường gặp](#khắc-phục-sự-cố-thường-gặp)
- [Checklist trước khi go-live](#checklist-trước-khi-go-live)
- [Roadmap](#roadmap)
- [Giấy phép](#giấy-phép)

---

## Giới thiệu

**Being Blog** là một dự án WordPress được tối ưu cho học tập và triển khai thực tế:

- Local dùng XAMPP/MAMP; Production trên Google Cloud (Compute Engine) + Nginx + PHP-FPM + MariaDB.
- Mã nguồn quản lý qua Git, chỉ commit `wp-content` (themes, plugins, mu-plugins, acf-json, code tùy chỉnh).
- CI/CD với GitHub Actions tự động sync code lên server qua SSH/SCP/RSYNC.
- Hỗ trợ đa ngôn ngữ bằng Polylang, custom fields với ACF (kèm ACF JSON để version control).

## Kiến trúc & Công nghệ

- **WordPress 6.x** (classic theme hoặc block theme tùy cấu hình)
- **PHP 8.1+**, **MariaDB/MySQL 10.6+**
- **Nginx** + **PHP-FPM**
- **GitHub Actions** (build + deploy)
- **ACF Pro** (hoặc ACF Free) + **Polylang**
- **wp-cli** (khuyến nghị cài đặt trên server)
- **Let’s Encrypt** cho SSL/TLS (Certbot)

## Tính năng nổi bật

- Đa ngôn ngữ (EN/VN) với Polylang, URL format chuẩn SEO.
- CPT `projects` + taxonomy `project_cat` (có thể mở rộng).
- ACF JSON: các field group được lưu vào repo, tự đồng bộ giữa môi trường.
- Triển khai CI/CD: push lên `main` → auto deploy lên server.
- Tối ưu Nginx rewrite, WebP, caching cơ bản.
- Bảo mật cơ bản: quyền file/folder, tách user deploy, hạn chế quyền ghi.

## Cấu trúc thư mục

> Repository chỉ chứa phần **`wp-content`** và các file cấu hình/automation cần thiết.

```
being-blog/
├─ .github/
│  └─ workflows/
│     └─ deploy.yml           # Workflow GitHub Actions (CI/CD)
├─ .gitignore                 # Bỏ qua core WP, uploads (tùy chọn) ...
├─ README.md
└─ wp-content/
   ├─ themes/
   │  └─ mythemes/            # Theme chính
   ├─ plugins/
   │  ├─ my-custom-plugin/    # Plugin tùy chỉnh (nếu có)
   │  └─ ...
   ├─ mu-plugins/             # Must-use plugins (nếu dùng)
   ├─ languages/              # File dịch (nếu có)
   ├─ acf-json/               # ACF JSON (tự sinh khi bật save point)
   └─ uploads/                # Ảnh/media (thường KHÔNG commit)
```

**.gitignore** gợi ý:

```
/wp-content/uploads/
/wp-content/cache/
/wp-content/upgrade/
/wp-content/wflogs/
/wp-content/debug.log
/.DS_Store
Thumbs.db
```

> Nếu muốn đồng bộ media, cân nhắc **WP-CLI + rsync** theo lịch, hoặc S3/Cloud Storage.

## Yêu cầu hệ thống

- Ubuntu 22.04 LTS (khuyến nghị)
- Nginx, PHP 8.1+/8.2, PHP extensions: `mbstring`, `curl`, `zip`, `xml`, `gd`, `imagick` (tùy plugin)
- MariaDB/MySQL 10.6+
- Quyền SSH tới server với user **deployer** (khuyến nghị tách khỏi `www-data`)
- DNS trỏ domain về IP VM, SSL với Certbot

## Thiết lập môi trường local

1. Cài **XAMPP** (Apache, PHP, MySQL/MariaDB).
2. Tạo database (vd: `being_blog`) và user có đủ quyền.
3. Tải WordPress core vào `htdocs/blog` (hoặc thư mục bạn chọn).
4. Clone repo này vào `wp-content` của site local.
5. Cấu hình `wp-config.php` (DB, salts, keys).
6. Cài plugins/theme còn thiếu từ WP Admin.
7. Import database mẫu (nếu có): `phpMyAdmin` hoặc `wp-cli db import`.
8. Chạy search/replace URL nếu đổi domain local.

## Cấu hình môi trường production

- **Đường dẫn webroot**: ví dụ `/var/www/wordpress` (core) và repo sync vào `/var/www/wordpress/wp-content`.
- **User/Group**:
  - Owner: `deployer:www-data` cho `wp-content`
  - Quyền đề xuất:
    ```bash
    sudo chown -R deployer:www-data /var/www/wordpress/wp-content
    sudo find /var/www/wordpress/wp-content -type d -exec chmod 2775 {} \;
    sudo find /var/www/wordpress/wp-content -type f -exec chmod 0664 {} \;
    ```
  - Riêng thư mục upload tạm (CF7): đảm bảo web server có thể ghi:
    ```bash
    mkdir -p /var/www/wordpress/wp-content/uploads/wpcf7_uploads
    sudo chown -R www-data:www-data /var/www/wordpress/wp-content/uploads/wpcf7_uploads
    sudo chmod 2775 /var/www/wordpress/wp-content/uploads/wpcf7_uploads
    ```
- **Nginx server block** (rút gọn):

  ```nginx
  server {
    listen 80;
    server_name your-domain.com;
    root /var/www/wordpress;
    index index.php index.html;

    location / {
      try_files $uri $uri/ /index.php?$args;
    }

    location ~ \.php$ {
      include snippets/fastcgi-php.conf;
      fastcgi_pass unix:/run/php/php8.2-fpm.sock;
    }

    location ~* \.(jpg|jpeg|gif|png|webp|svg|css|js)$ {
      access_log off;
      add_header Cache-Control "public, max-age=2592000, immutable";
    }
  }
  ```

- **SSL**: bật bằng Certbot `sudo certbot --nginx`.

## Triển khai bằng GitHub Actions

- Lưu SSH private key trong **Repository Secrets** (ví dụ: `DEPLOY_KEY`), và biến môi trường:
  - `DEPLOY_HOST`, `DEPLOY_PORT`, `DEPLOY_USER`, `REMOTE_PATH` (vd: `/var/www/wordpress/wp-content`).
- **deploy.yml** (mẫu rút gọn):

  ```yaml
  name: Deploy wp-content to Server

  on:
    push:
      branches: ["main"]

  jobs:
    deploy:
      runs-on: ubuntu-latest
      steps:
        - uses: actions/checkout@v4

        - name: Setup SSH
          run: |
            mkdir -p ~/.ssh
            echo "${{ secrets.DEPLOY_KEY }}" > ~/.ssh/id_ed25519
            chmod 600 ~/.ssh/id_ed25519
            ssh-keyscan -p ${{ secrets.DEPLOY_PORT }} ${{ secrets.DEPLOY_HOST }} >> ~/.ssh/known_hosts

        - name: Rsync wp-content
          run: |
            rsync -az --delete \
              --exclude 'uploads/' \
              ./wp-content/ ${{ secrets.DEPLOY_USER }}@${{ secrets.DEPLOY_HOST }}:${{ secrets.REMOTE_PATH }}/
  ```

> Nếu cần đồng bộ `uploads/`, bỏ `--exclude 'uploads/'` và đảm bảo quyền ghi đúng.

## Quản lý database (local ⇄ server)

**Export từ local:**

```bash
# Với wp-cli
wp db export /path/to/being_blog_local.sql

# Hoặc phpMyAdmin: Export dạng SQL
```

**Import lên server:**

```bash
# Upload file .sql lên server (scp/rsync)
wp db import /path/to/being_blog_local.sql
```

**Search & Replace URL (bắt buộc sau khi đổi domain):**

```bash
wp search-replace 'http://localhost/blog' 'https://your-domain.com' --skip-columns=guid
```

**Backup trước khi import:**

```bash
wp db export /path/to/backup_before_import.sql
```

## Đồng bộ ACF JSON

- Bật **Local JSON** trong theme/plugin để **SAVE** & **LOAD** ACF JSON:
  ```php
  add_filter('acf/settings/save_json', function() {
    return get_stylesheet_directory() . '/acf-json';
  });
  add_filter('acf/settings/load_json', function($paths) {
    $paths[] = get_stylesheet_directory() . '/acf-json';
    return $paths;
  });
  ```
- Commit toàn bộ file trong `wp-content/acf-json/`.
- Khi đổi môi trường, vào **ACF → Sync** để đồng bộ các field group.

## Đa ngôn ngữ với Polylang

- Cài **Polylang** (hoặc Polylang Pro).
- Thiết lập ngôn ngữ (EN/VN), chọn **URL modifications** phù hợp (vd: ngôn ngữ trong URL).
- Mapping bản dịch cho Pages/Posts/CPT. Với CPT `projects`, bật hỗ trợ dịch:
  ```php
  add_action('init', function(){
    register_post_type('projects', [
      'label' => 'Projects',
      'public' => true,
      'supports' => ['title','editor','thumbnail','excerpt','custom-fields'],
      'has_archive' => true,
      'rewrite' => ['slug' => 'projects'],
      'show_in_rest' => true,
    ]);
    register_taxonomy('project_cat','projects',[
      'label' => 'Project Categories',
      'hierarchical' => true,
      'rewrite' => ['slug' => 'project-category'],
      'show_in_rest' => true,
    ]);
  });
  ```
- Đồng bộ bản dịch media, menu, widgets theo yêu cầu.

## Custom Post Type: Projects

- Slug archive: `/projects/`
- Template gợi ý: `archive-projects.php`, `single-projects.php`
- ACF fields gợi ý: `client_name`, `project_url`, `tech_stack`, `gallery`…
- Khi deploy, đảm bảo **Flush Permalinks** (Settings → Permalinks → Save).

## SEO & Hiệu năng

- Cài **Yoast SEO** hoặc **Rank Math**.
- Bật **WebP/AVIF** (plugin: WebP Converter for Media…) và chắc rewrite hoạt động.
- Tối ưu ảnh bằng **Imagick/GD** (server).
- Cache mức Nginx (static files) + plugin cache (nếu cần).

## Bảo mật & Phân quyền

- Tạo user `deployer` chỉ dùng để deploy code, không cấp sudo không cần thiết.
- Quyền file/folder như phần **Production**.
- Khóa ssh bằng key, tắt password auth (trong `/etc/ssh/sshd_config`).
- Sao lưu DB định kỳ, tải xuống hoặc đẩy lên Cloud Storage.
- Giới hạn plugin, cập nhật core/plugin có kiểm soát.

## Sao lưu & Khôi phục

- **DB**: `wp db export` theo lịch (cron) → lưu vào `/var/backups` hoặc cloud.
- **Uploads**: rsync đến storage phụ.
- **Code**: đã có trên Git (có thể tag phiên bản release).

## Khắc phục sự cố thường gặp

### 1) `rewrites_not_executed` (WebP/Rewrite)

- Kiểm tra Nginx server block có `try_files $uri $uri/ /index.php?$args;`.
- Với plugin WebP, tham khảo rule Nginx do plugin đề xuất; hoặc thêm:
  ```nginx
  location ~* \.(png|jpe?g)$ {
    try_files $uri$webp_suffix $uri =404;
  }
  ```
- Kiểm tra quyền đọc `uploads/` và file `.htaccess` (nếu dùng Apache ở local).

### 2) Lỗi quyền `uploads/` khi deploy

- Một số file do `root` sở hữu → dùng:
  ```bash
  sudo chown -R deployer:www-data /var/www/wordpress/wp-content
  sudo find /var/www/wordpress/wp-content -type d -exec chmod 2775 {} \;
  sudo find /var/www/wordpress/wp-content -type f -exec chmod 0664 {} \;
  ```
- Với thư mục upload tạm của CF7:
  ```bash
  sudo chown -R www-data:www-data /var/www/wordpress/wp-content/uploads/wpcf7_uploads
  sudo chmod 2775 /var/www/wordpress/wp-content/uploads/wpcf7_uploads
  ```

### 3) Sau khi import DB, link vẫn trỏ về localhost

- Chạy `wp search-replace` như phần **Database**.
- Xóa cache plugin/CDN nếu có.

### 4) 404 cho CPT sau khi deploy

- Vào **Settings → Permalinks → Save** để flush.
- Đảm bảo file template `archive-projects.php`, `single-projects.php` tồn tại.

### 5) ACF không hiện fields

- Kiểm tra thư mục `acf-json/` đã sync vào server.
- Vào ACF → **Sync** để đồng bộ.
- Kiểm tra `add_filter('acf/settings/load_json'...)` đã bật.

## Checklist trước khi go-live

- [ ] Domain trỏ đúng IP, SSL hoạt động (HTTP→HTTPS redirect).
- [ ] `wp-config.php` production (DB, salts, `WP_DEBUG` off, `DISALLOW_FILE_EDIT` on).
- [ ] Quyền file/folder chuẩn, user deploy riêng.
- [ ] Permalinks đã flush.
- [ ] ACF JSON đã sync, Polylang mapping đầy đủ.
- [ ] Sitemap/Robots, Search Console, Analytics.
- [ ] Backup DB + Uploads mới nhất.
- [ ] Pages/Posts/CPT đã QA nội dung/ảnh.

## Roadmap

- [ ] Đồng bộ media tự động qua rsync/S3 (tuỳ chọn).
- [ ] Tối ưu CI: chỉ rsync file thay đổi.
- [ ] Thêm test E2E (Playwright) cho các trang chính.
- [ ] Cache nâng cao (FastCGI cache) trên Nginx.
- [ ] CDN cho static assets.

## Giấy phép

Phát hành theo giấy phép **MIT**. Bạn có thể sử dụng cho mục đích học tập và dự án cá nhân/thương mại theo điều kiện của MIT.

---

**Gợi ý**: Đổi toàn bộ biến `your-domain.com`, đường dẫn `/var/www/wordpress`, và thông số SSH theo server của bạn. Nếu cần, tạo file `DEPLOYMENT.md` riêng để ghi chi tiết pipeline và playbook vận hành.
