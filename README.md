# Kids Party Planner

Production-oriented Laravel 12 marketplace for booking kids activities, birthday decorations, celebration packages, and add-ons in Delhi, Noida, and Gurgaon.

## Stack

- Laravel 12, Blade, Eloquent, validation, auth middleware, mail, and secure uploads
- MySQL for production with SQLite support for local development and tests
- Bootstrap 5, jQuery, AJAX filters, Swiper.js, Font Awesome, and Google Fonts
- Razorpay order creation and HMAC signature verification
- WhatsApp enquiries, booking tracking, invoices, coupons, reviews, and wishlists

## Marketplace Flow

1. Customer selects a service city.
2. Category and service pages display city-aware pricing and availability.
3. Services, packages, quantities, and eligible add-ons are added to the cart.
4. Checkout captures the event schedule, area, venue, age group, kids count, theme, and address.
5. The server recalculates city pricing, travel fee, coupon, tax, advance, and full-payment totals.
6. Razorpay handles advance or full payment and stores the transaction state.
7. Customers can track the booking, download the invoice, cancel, rebook, and review completed services.

Current booking cities are Delhi, Noida, and Gurgaon. Mumbai, Pune, and Jaipur are included as inactive upcoming cities.

## Main Features

- Home page with managed banners, city selector, category shelves, trending services, packages, reviews, gallery, FAQ, and Instagram CTA
- Category, subcategory, city/category SEO pages, AJAX service filters, sorting, and pagination
- Detailed service gallery, inclusions, exclusions, requirements, policies, add-ons, FAQs, reviews, related services, cart, wishlist, and WhatsApp enquiry
- Persistent guest cart that merges into the customer account after login
- Multi-item checkout with area-aware charges, coupons, advance/full payment, success/failure pages, and email notifications
- Public booking tracker using booking number plus registered mobile
- Customer dashboard for overview, booking details, payments, profile, wishlist, invoice, cancellation, and rebooking
- Dynamic sitemap, robots.txt, page metadata, Open Graph tags, lazy-loaded images, CSRF, role middleware, and validated uploads

## Admin Panel

Login: `http://127.0.0.1:8000/admin/login`

- Email: `admin@kidspartyplanner.in`
- Password: `password`

Managed modules include dashboard analytics, cities, areas, categories, subcategories, services, service images, city prices, add-ons, packages, bookings, payments, customers, admins, city payment rules, reviews, gallery, banners, FAQs, enquiries, coupons, refunds, blogs, CMS pages, and website settings.

## Local Setup

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Configure MySQL in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kids_party_planner
DB_USERNAME=root
DB_PASSWORD=
```

Then initialize and run:

```powershell
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve --host=127.0.0.1 --port=8000
```

Open `http://127.0.0.1:8000`.

Customer demo credentials:

- Email: `parent@example.com`
- Password: `password`

## Razorpay

Add test or live keys to `.env`:

```env
RAZORPAY_KEY=rzp_test_xxxxxxxxxx
RAZORPAY_SECRET=xxxxxxxxxx
```

The backend creates a Razorpay order, persists the gateway order ID, opens Checkout.js, verifies the returned signature with HMAC SHA-256, and only then marks the booking/payment as successful. A real transaction requires valid Razorpay keys.

## Google Login

Customer login and registration support Google OAuth through Laravel Socialite. Create a Google OAuth web application and add this authorized redirect URI:

```text
http://127.0.0.1:8000/auth/google/callback
```

Then configure:

```env
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

Run `php artisan config:clear` after changing credentials. Google login is customer-only; admin accounts continue to use the dedicated admin login.

## WhatsApp And Mail

```env
WHATSAPP_NUMBER=919910434330
MAIL_MAILER=log
```

Business contacts seeded in website settings:

- Phone: `+91 9910434330`
- Email: `sales@kidspartyplanner.in`
- Address: `TC-37, Pandav Nagar, Shadipur, New Delhi - 110008`
- Instagram: `https://www.instagram.com/kidspartyplanner1/`

For real email notifications, configure SMTP and update `admin_email` under Admin > Settings.

## Verification

```powershell
php artisan migrate:fresh --seed
php artisan route:list --except-vendor
php artisan view:cache
php artisan test
```

The feature suite covers public pages, city/category URLs, customer/admin dashboards, city payment calculations, cart checkout, booking item persistence, and public tracking.
