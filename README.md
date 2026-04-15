# OmniBalance - Holistic Wellness & Habit Tracker
*(Formerly Multi-User SaaS Nutrition & Supplement Tracker)*

A multi-tenant progressive web application (PWA) built with Laravel and Tailwind CSS. This system acts like a native mobile app to help users track daily nutrition, supplements, spiritual habits, mental wellness, and automatically manage inventory stock. It features a complete Super Admin panel for platform management and is specially configured to run seamlessly on shared hosting root directories without relying on a `public` folder.

## ✨ Features

* 🏢 **Multi-User Architecture (SaaS):** Complete user authentication. Every user gets their own private, isolated dashboard, inventory, and analytics.
* 🧘‍♀️ **Quick-Add Habit & Activity System:** Users have a customizable list of default exercises (like Kegels, Walking, Yoga) that they can log with a single click. Default holistic activities are automatically injected upon registration.
* 🕌 **Spiritual & Prayer Tracker:** Built-in daily tracking for the 5 Muslim prayers plus Tahajjud, visually integrated into the dashboard.
* 🏃‍♂️ **Exercise & Activity Logging:** Quickly log daily workouts and active minutes, complete with monthly activity analytics and CRUD management for default routines.
* 🌙 **Daily Wellness Metrics:** Track daily fasting status, hours of sleep, and overall mood to find correlations between habits and well-being.
* 📶 **Advanced Offline Syncing:** Log water, weight, supplements, prayers, and exercises even without an internet connection. The PWA uses IndexedDB and Background Sync to optimistically update the UI and automatically syncs data once the connection is restored.
* ⚠️ **Smart Low-Stock Alerts:** Set custom low-stock thresholds. The system automatically sends a web push notification when you are running out (intelligently limited to one alert per item every 24 hours).
* 💧 **Quick Water Intake Tracker:** Visual 8-glass widget on the dashboard to log daily hydration instantly.
* 🔔 **Custom Reminders & Bedtime Control:** Users can set personalized hydration intervals and toggle whether they want to receive push notifications during sleeping hours (10 PM - 8 AM).
* ⚖️ **Body Weight Tracking:** Log your daily weight and view your progress history directly from the analytics dashboard.
* 🗂️ **Item Categorization:** Group inventory into Supplements, Meals, Hydration, Medication, or General.
* 📄 **Advanced Analytics & Export:** Visual monthly calendar showing perfect vs. partial completion days. Export monthly logs, including exercise and wellness metrics, to CSV or beautifully formatted PDF reports for dietitians.
* 🌍 **Global Timezone Support:** The daily tracker, streaks, scheduling logic, and date-slider automatically adjust to the user's local midnight. Blocks future date logging for accurate tracking.
* 📊 **Dynamic Progress & Macros Visualization:** Real-time animated progress bar and daily calculated summaries for Calories, Protein, Carbs, and Fats.
* 🛡️ **Super Admin Panel:** Dedicated dashboard to view system stats, manage users, configure global settings, and access Cron Job instructions.
* 📱 **Mobile-First PWA:** Feels and acts like a native mobile app. Can be installed to the home screen via the browser.
* 🌓 **Dark & Light Mode:** Built-in theme switcher that remembers user preference.
* 🌐 **Bilingual Support (EN/BN):** Switch between English and Bengali seamlessly. 
* ⚡ **Automated Web Installer (3-Steps):** Zero manual server terminal commands required. Connects to the database and seeds starter items directly from the browser.
* 📁 **Root Directory Ready:** Custom `index.php` and advanced `.htaccess` ensure the app runs securely from the root folder without needing a `public` directory symlink.

---

## 🚀 Step 1: Local Preparation & Build

Before uploading to your hosting, you need to generate the base framework, apply the custom code, and prepare the build on your local machine.

1. **Generate Laravel Skeleton:**
   Open your terminal and run the following command to create a fresh Laravel framework foundation:
   `composer create-project laravel/laravel omnibalance`
   Once it finishes downloading, navigate into the new directory:
   `cd omnibalance`

2. **Replace & Add Custom Files:**
   Copy all the custom files you gathered (Controllers, Models, Blade views, Migrations, Seeders, Service Worker `sw.js`, etc.) and paste them into this newly created folder. Overwrite any existing files when prompted.
   *Important:* Ensure the custom `index.php`, `.htaccess`, `composer.json`, and `.env.example` files are placed directly in the root directory.

3. **Install & Optimize PHP Dependencies:**
   Since you updated the `composer.json` to include PDF generation and Web Push packages, refresh and optimize your dependencies:
   `composer update`
   `composer install --optimize-autoloader --no-dev`

4. **Generate Push Notification VAPID Keys (CRITICAL FOR PUSH):**
   Generate the VAPID keys for Web Push Notifications locally, then copy them into `.env.example` so they ship with the zip.
   * `cp .env.example .env`
   * Publish the migration: `php artisan vendor:publish --provider="NotificationChannels\WebPush\WebPushServiceProvider" --tag="migrations"`
   * Generate the keys: `php artisan webpush:vapid`
   *(This adds `VAPID_PUBLIC_KEY` and `VAPID_PRIVATE_KEY` to your local `.env`.)*
   * **Copy those two key values into `.env.example`** so they are included in the zip.
   * **Do NOT include `.env` in the zip** — `APP_KEY` and database credentials are generated fresh by the Web Installer for each client installation.

5. **Install Node Modules & Build Assets (Optional but Recommended):**
   `npm install`
   `npm run build`

---

## 📦 Step 2: Zipping the Project

You must compress the files into a `.zip` archive to upload to your shared hosting. 

**CRITICAL:** Include **hidden files** (files starting with a dot, especially `.env.example` and `.htaccess`) when zipping. **Do NOT include `.env`** — it contains your personal app key and must never be shipped to clients.

* **Windows:** Select all files/folders inside the project directory, right-click -> Compress to ZIP file. Make sure hidden items are visible. Confirm `.env.example` and `.htaccess` are included, but `.env` is excluded.
* **Mac/Linux:** Open terminal in the project folder and run:
  `zip -r omnibalance.zip . -x "*.git*" -x "node_modules/*" -x ".env"`

> **Security note:** The Web Installer generates a fresh `APP_KEY` automatically for each installation. No sensitive credentials from your development machine are ever shipped to clients.

---

## ☁️ Step 3: Hosting Upload & Extraction

1. Log in to your hosting control panel (cPanel, DirectAdmin, etc.).
2. Go to the **File Manager** and navigate to the exact root directory of your subdomain (e.g., `track.yourdomain.com`).
3. **Upload** the `omnibalance.zip` file.
4. **Extract** the zip file directly into this root directory.
5. **Verify Hidden Files:** Ensure that `.htaccess`, `.env.example`, and `index.php` are sitting directly in the root folder, alongside folders like `app`, `bootstrap`, `vendor`, etc. There is no `.env` file yet — the Web Installer will create it automatically from `.env.example`.

---

## ⚙️ Step 4: Web Installation & Automation

You will not need to touch any code or run any terminal commands on the server. 

1. Create an empty MySQL Database and Database User from your hosting control panel. Note down the Database Name, Username, and Password.
2. Open your browser and visit your subdomain (e.g., `https://track.yourdomain.com`).
3. The app will automatically detect it is not installed and redirect you to the **Web Installer**.
4. **Step 1 (Requirements Check):** Ensure all server requirements (PHP version, PDO, Writable permissions) show as ✔ OK. Click Continue.
5. **Step 2 (Database Setup):** Enter the Database Host (usually `localhost` or `127.0.0.1`), Port, Database Name, Username, and Password. Click "Connect".
6. **Step 3 (Admin Setup):** Enter the Name, Email, Timezone, and Password for the Super Administrator account. Click "Complete Installation".
7. **Step 4 (Setup Cron Job):** To enable automated hydration reminders and **Low-Stock Alerts**, log in as Admin, go to **Global Settings**, and copy the generated **Cron Command**. Paste it into your hosting panel's Cron Job section and set it to run **Every Minute (* * * * *)**.

---

## 🛠 Troubleshooting

* **Push Notifications aren't prompting/working:**
    Web Push Notifications *strictly* require an active SSL certificate. Ensure your site is loaded via `https://` and not `http://`. Also, ensure `VAPID_PUBLIC_KEY` and `VAPID_PRIVATE_KEY` exist in your `.env` file.
* **Automated Reminders or Low-Stock Alerts not sending:**
    Ensure the Cron Job (Step 4 above) is active. The system uses this to check user preferences and inventory thresholds every minute. Also, check if the user has opted out via the "Bedtime Notification" toggle.
* **Offline Syncing isn't working:**
    Background sync requires a modern browser that supports the `ServiceWorkerRegistration.sync` API (like Chrome/Edge for Android). On iOS, offline logs are queued optimistically but may require the app to be reopened while online to fully flush the IndexedDB queue.
* **500 Internal Server Error immediately after extraction:**
    Check your `.htaccess` file. Make sure your server supports `mod_rewrite`. Ensure the PHP version assigned to the subdomain is at least 8.2.
* **Installer cannot write to `.env`:**
    If your server has strict permissions, the script might fail to modify `.env`. In this case, ensure the `.env` file you extracted has `644` or `775` permissions via your File Manager.
* **Database Error 150 (Foreign Key Constraint):**
    Ensure the default Laravel `0001_01_01_000000_create_users_table.php` migration exists in your `database/migrations` folder so it runs before the Items and Logs migrations.