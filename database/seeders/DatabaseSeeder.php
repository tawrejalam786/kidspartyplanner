<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Category;
use App\Models\CityPaymentSetting;
use App\Models\Coupon;
use App\Models\Gallery;
use App\Models\Package as PartyPackage;
use App\Models\Page;
use App\Models\Review;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Kids Party Admin',
            'email' => 'admin@kidspartyplanner.in',
            'phone' => '9999999999',
            'role' => 'admin',
            'city' => 'Delhi',
            'password' => 'password',
        ]);

        $customer = User::create([
            'name' => 'Demo Parent',
            'email' => 'parent@example.com',
            'phone' => '8888888888',
            'role' => 'customer',
            'city' => 'Noida',
            'address' => 'Sector 62, Noida',
            'password' => 'password',
        ]);

        foreach ([
            ['Delhi', 'delhi', 30, 500, 149, 5, true, 'Delhi bookings are confirmed after the city advance is received.'],
            ['Noida', 'noida', 35, 750, 199, 5, false, 'Noida and Greater Noida travel is included within the listed service area.'],
            ['Gurgaon', 'gurgaon', 40, 1000, 249, 5, false, 'Gurgaon bookings may require venue access and parking confirmation.'],
            ['Ghaziabad', 'ghaziabad', 35, 750, 199, 5, false, 'Ghaziabad bookings are subject to artist availability for the selected date.'],
            ['Faridabad', 'faridabad', 40, 1000, 249, 5, false, 'Faridabad travel and timing are confirmed before payment.'],
        ] as $city) {
            CityPaymentSetting::updateOrCreate(['slug' => $city[1]], [
                'city' => $city[0],
                'advance_percent' => $city[2],
                'minimum_advance' => $city[3],
                'service_fee' => $city[4],
                'tax_percent' => $city[5],
                'is_default' => $city[6],
                'payment_instructions' => $city[7],
            ]);
        }

        $categoryData = [
            ['Entertainment', 'Stage-ready performers and party hosts.', 'fa-wand-magic-sparkles', 'https://images.unsplash.com/photo-1541532713592-79a0317b6b77?auto=format&fit=crop&w=900&q=80'],
            ['Creative Activities', 'Hands-on art, craft, and maker corners.', 'fa-palette', 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&w=900&q=80'],
            ['Beauty & Fun', 'Cute mini makeover and styling activities.', 'fa-face-smile-beam', 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=900&q=80'],
            ['Games & Coordination', 'Coordinators, party games, and group activities.', 'fa-gamepad', 'https://images.unsplash.com/photo-1516627145497-ae6968895b74?auto=format&fit=crop&w=900&q=80'],
            ['DIY Workshops', 'Take-home crafts kids can proudly show off.', 'fa-scissors', 'https://images.unsplash.com/photo-1499892477393-f675706cbe6e?auto=format&fit=crop&w=900&q=80'],
        ];

        $categories = collect($categoryData)->mapWithKeys(function ($category) {
            $model = Category::create([
                'name' => $category[0],
                'slug' => Str::slug($category[0]),
                'description' => $category[1],
                'icon' => $category[2],
                'image' => $category[3],
            ]);

            return [$category[0] => $model];
        });

        $serviceData = [
            ['Magic Show', 2000, 'Entertainment', 'Classic comedy magic, stage tricks, and child-friendly audience participation.'],
            ['Animal Magic', 2000, 'Entertainment', 'A gentle animal-themed magic act with soft props and surprise reveals.'],
            ['Game Coordinator', 2000, 'Games & Coordination', 'A high-energy host who keeps children moving through structured birthday games.'],
            ['Adult Games', 3000, 'Games & Coordination', 'Light-hearted games for parents and adults while the kids enjoy the party.'],
            ['Puppet Show', 2000, 'Entertainment', 'Interactive puppet storytelling with music, jokes, and birthday shout-outs.'],
            ['Tattoo Artist', 1500, 'Beauty & Fun', 'Temporary glitter tattoos with party-safe colors and quick station setup.'],
            ['Face Painting', 2500, 'Beauty & Fun', 'Theme-based face art for superheroes, princesses, animals, and fantasy characters.'],
            ['Gun Shooting', 2500, 'Games & Coordination', 'Supervised carnival-style target shooting booth with soft toy guns.'],
            ['Cap Making', 350, 'DIY Workshops', 'Per-piece cap decoration counter with stickers, colors, and kid-safe craft supplies.'],
            ['Shop Making', 300, 'DIY Workshops', 'Per-piece pretend-shop craft activity where kids decorate mini shop displays.'],
            ['Name Plate', 3000, 'Creative Activities', 'Personalized name plate making station for memorable take-home decor.'],
            ['Stone Making', 3000, 'Creative Activities', 'Decorative stone painting and embellishment corner.'],
            ['Slime Making', 2500, 'Creative Activities', 'Colorful slime workshop with glitter, mix-ins, and guided supervision.'],
            ['Glow Jar', 250, 'DIY Workshops', 'Per-piece glowing jar activity with child-safe craft materials.'],
            ['Candle Making', 250, 'DIY Workshops', 'Per-piece candle activity with colors, aroma options, and guided assembly.'],
            ['Wooden Kitchen', 3000, 'Creative Activities', 'Pretend-play wooden kitchen setup for toddlers and young kids.'],
            ['Key Chain Making', 2500, 'DIY Workshops', 'Personalized key chain crafting with beads, charms, and name tags.'],
            ['Bubble Activity', 2500, 'Entertainment', 'Bubble show and play zone with giant bubbles and photo moments.'],
            ['Nail Art', 2200, 'Beauty & Fun', 'Kids-safe nail paint and sticker art counter.'],
            ['Hair Beading', 2500, 'Beauty & Fun', 'Colorful hair beads and styling station for birthday guests.'],
            ['Mini Beauty Parlour', 5000, 'Beauty & Fun', 'A complete mini makeover booth with hair, nails, and glitter styling.'],
        ];

        $images = [
            'https://images.unsplash.com/photo-1513151233558-d860c5398176?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1464349153735-7db50ed83c84?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1527529482837-4698179dc6ce?auto=format&fit=crop&w=900&q=80',
        ];

        foreach ($serviceData as $index => $service) {
            $model = Service::create([
                'category_id' => $categories[$service[2]]->id,
                'title' => $service[0],
                'slug' => Str::slug($service[0]),
                'short_description' => $service[3],
                'description' => $service[3].' Our team reaches Delhi, Noida, Gurgaon, Ghaziabad, and Faridabad with neat setup, trained artists, and punctual coordination.',
                'price' => $service[1],
                'discount_price' => $index % 4 === 0 ? max(250, $service[1] - 300) : null,
                'duration' => in_array($service[0], ['Cap Making', 'Shop Making', 'Glow Jar', 'Candle Making']) ? 'Per piece counter' : '60 to 90 minutes',
                'location' => 'Delhi NCR, Delhi, Noida, Gurgaon',
                'rating' => 4.6 + (($index % 4) / 10),
                'total_reviews' => 18 + $index,
                'inclusions' => ['Setup and teardown', 'Trained activity expert', 'Basic props and materials', 'Travel within Delhi NCR'],
                'exclusions' => ['Venue decoration', 'Food and cake', 'Parking or society entry charges'],
                'add_ons' => [
                    ['name' => 'Extra 30 minutes', 'price' => 800],
                    ['name' => 'Theme props', 'price' => 1200],
                    ['name' => 'Return gifts coordination', 'price' => 1500],
                ],
                'faq' => [
                    ['question' => 'How early should we book?', 'answer' => 'Three to seven days is ideal, though same-day slots may be available.'],
                    ['question' => 'Do you bring all materials?', 'answer' => 'Yes, standard materials and props are included unless noted otherwise.'],
                    ['question' => 'Can this be done at home?', 'answer' => 'Yes, our activities work well at homes, clubs, banquet halls, and society rooms.'],
                ],
                'featured' => $index < 8,
                'meta_title' => $service[0].' for Kids Birthday Party in Delhi NCR',
                'meta_description' => 'Book '.$service[0].' for kids birthday parties in Delhi, Noida, and Gurgaon with Kids Party Planner.',
            ]);

            $model->images()->createMany([
                ['path' => $images[$index % count($images)], 'alt_text' => $service[0], 'is_primary' => true],
                ['path' => $images[($index + 1) % count($images)], 'alt_text' => $service[0].' party setup'],
            ]);
        }

        $packages = [
            ['Tiny Star Party', 6999, 5999, ['Magic Show', 'Tattoo Artist', 'Game Coordinator'], 'A compact celebration plan for home birthdays and small society rooms.'],
            ['Rainbow Bash', 11999, 9999, ['Puppet Show', 'Face Painting', 'Bubble Activity', 'Game Coordinator'], 'Balanced entertainment, activities, and games for energetic birthday groups.'],
            ['Creative Carnival', 14999, 12999, ['Slime Making', 'Key Chain Making', 'Face Painting', 'Game Coordinator'], 'A craft-heavy party plan with take-home activities and a dedicated coordinator.'],
            ['Premium Playday', 24999, 21999, ['Mini Beauty Parlour', 'Magic Show', 'Bubble Activity', 'Adult Games'], 'A fuller party experience for larger gatherings and premium birthday venues.'],
        ];

        foreach ($packages as $index => $package) {
            PartyPackage::create([
                'title' => $package[0],
                'slug' => Str::slug($package[0]),
                'price' => $package[1],
                'discount_price' => $package[2],
                'services' => $package[3],
                'description' => $package[4],
                'inclusions' => ['Pre-event confirmation call', 'Party host coordination', 'Standard materials', 'Delhi NCR travel'],
                'image' => $images[($index + 2) % count($images)],
                'duration' => '2 to 3 hours',
                'featured' => true,
                'meta_title' => $package[0].' Kids Birthday Party Package',
                'meta_description' => 'Book '.$package[0].' party package in Delhi NCR with Kids Party Planner.',
            ]);
        }

        $galleryItems = [
            ['Magic stage moment', 'Entertainment', $images[0]],
            ['Colorful activity table', 'Creative Activities', $images[1]],
            ['Birthday game circle', 'Games', $images[2]],
            ['Face painting smiles', 'Beauty & Fun', $images[3]],
            ['Premium party corner', 'Packages', $images[4]],
        ];

        foreach ($galleryItems as $index => $item) {
            Gallery::create([
                'title' => $item[0],
                'type' => $item[1],
                'image' => $item[2],
                'sort_order' => $index + 1,
            ]);
        }

        foreach (Service::where('featured', true)->take(6)->get() as $index => $service) {
            Review::create([
                'user_id' => $customer->id,
                'service_id' => $service->id,
                'customer_name' => ['Ritika Sharma', 'Aman Verma', 'Neha Kapoor', 'Sonal Bansal', 'Karan Mehra', 'Priya Sethi'][$index],
                'rating' => 5,
                'comment' => 'The team was punctual, warm with kids, and the activity stayed lively from start to finish.',
                'is_approved' => true,
            ]);
        }

        Coupon::create([
            'code' => 'PARTY10',
            'description' => '10% off launch coupon',
            'type' => 'percent',
            'value' => 10,
            'min_order' => 2000,
            'max_discount' => 1500,
            'expires_at' => now()->addMonths(6),
        ]);

        Coupon::create([
            'code' => 'WELCOME500',
            'description' => 'Flat discount for first booking',
            'type' => 'fixed',
            'value' => 500,
            'min_order' => 3000,
            'expires_at' => now()->addMonths(6),
        ]);

        $pages = [
            'about' => ['About Kids Party Planner', '<p>Kids Party Planner is a Delhi NCR birthday planning studio focused on joyful, practical, and parent-friendly celebrations. We bring entertainers, activity experts, coordinators, and creative counters to homes, societies, clubs, schools, and banquet venues.</p><p>Our team covers Delhi, Noida, Gurgaon, Ghaziabad, and Faridabad with transparent packages and easy booking.</p>'],
            'terms' => ['Terms and Conditions', '<p>Bookings are confirmed after successful advance payment or written confirmation from the Kids Party Planner team. Customers must provide accurate event details, venue permissions, parking information, and access instructions.</p><p>Service timings, inclusions, and add-ons are limited to the details confirmed at booking.</p>'],
            'privacy-policy' => ['Privacy Policy', '<p>We collect customer name, phone, email, event details, and payment references only to process enquiries, bookings, support, and service updates. We do not sell personal data.</p><p>Payment processing is handled through secure third-party payment providers.</p>'],
            'refund-policy' => ['Refund Policy', '<p>Advance payments are refundable only when cancellation is requested at least 72 hours before the event, after deducting payment gateway charges. Date changes depend on artist and coordinator availability.</p><p>No refund applies for same-day cancellations, wrong venue details, or denied venue access.</p>'],
        ];

        foreach ($pages as $slug => [$title, $content]) {
            Page::create([
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'meta_title' => $title.' | Kids Party Planner',
                'meta_description' => Str::limit(strip_tags($content), 150),
            ]);
        }

        Blog::create([
            'title' => 'How to Plan a Kids Birthday Party in Delhi NCR',
            'slug' => 'how-to-plan-kids-birthday-party-delhi-ncr',
            'excerpt' => 'A simple checklist for selecting activities, timing, and booking the right party service.',
            'content' => '<p>Start with the age group, venue size, and event duration. Pick one main entertainment act, one activity counter, and a game coordinator for a balanced celebration.</p>',
            'image' => $images[0],
            'published_at' => now(),
            'meta_title' => 'Kids Birthday Party Planning Guide Delhi NCR',
            'meta_description' => 'Plan kids birthday parties in Delhi, Noida, and Gurgaon with this quick checklist.',
        ]);

        $settings = [
            'site_name' => 'Kids Party Planner',
            'site_phone' => '+91 99999 99999',
            'site_email' => 'hello@kidspartyplanner.test',
            'admin_email' => 'admin@kidspartyplanner.in',
            'whatsapp_number' => '919999999999',
            'service_area' => 'Delhi, Noida, Gurgaon, Ghaziabad, Faridabad',
            'advance_percent' => '30',
            'meta_title' => 'Kids Party Planner - Birthday Party Booking in Delhi NCR',
            'meta_description' => 'Book kids birthday party entertainers, craft activities, game coordinators, and packages in Delhi, Noida, and Gurgaon.',
        ];

        foreach ($settings as $key => $value) {
            Setting::create(['key' => $key, 'value' => $value]);
        }

        $this->call(MarketplaceSeeder::class);
    }
}
