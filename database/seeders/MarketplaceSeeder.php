<?php

namespace Database\Seeders;

use App\Models\Addon;
use App\Models\Area;
use App\Models\Banner;
use App\Models\Category;
use App\Models\City;
use App\Models\CityPaymentSetting;
use App\Models\Faq;
use App\Models\Package as PartyPackage;
use App\Models\Service;
use App\Models\ServiceCityPrice;
use App\Models\Setting;
use App\Models\Subcategory;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        $images = [
            'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?auto=format&fit=crop&w=1100&q=84',
            'https://images.unsplash.com/photo-1513151233558-d860c5398176?auto=format&fit=crop&w=1100&q=84',
            'https://images.unsplash.com/photo-1464349153735-7db50ed83c84?auto=format&fit=crop&w=1100&q=84',
            'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=1100&q=84',
            'https://images.unsplash.com/photo-1527529482837-4698179dc6ce?auto=format&fit=crop&w=1100&q=84',
        ];

        $cities = collect([
            ['Delhi', 'Delhi', true, true],
            ['Noida', 'Uttar Pradesh', true, true],
            ['Gurgaon', 'Haryana', true, true],
            ['Mumbai', 'Maharashtra', false, false],
            ['Pune', 'Maharashtra', false, false],
            ['Jaipur', 'Rajasthan', false, false],
        ])->mapWithKeys(function ($data, $index) use ($images) {
            $city = City::updateOrCreate(['slug' => Str::slug($data[0])], [
                'name' => $data[0],
                'state' => $data[1],
                'image' => $images[$index % count($images)],
                'is_current' => $data[2],
                'is_active' => $data[3],
                'sort_order' => $index + 1,
                'meta_title' => 'Kids Party Services in '.$data[0],
                'meta_description' => 'Book kids activities, decorations and party packages in '.$data[0].'.',
            ]);

            return [$data[0] => $city];
        });

        foreach ([
            'Delhi' => [['Dwarka', '110075'], ['Rohini', '110085'], ['South Delhi', '110017'], ['East Delhi', '110092'], ['Central Delhi', '110008']],
            'Noida' => [['Sector 18', '201301'], ['Sector 62', '201309'], ['Greater Noida', '201310'], ['Noida Extension', '201318']],
            'Gurgaon' => [['DLF Phase 1', '122002'], ['Sohna Road', '122018'], ['Golf Course Road', '122011'], ['Sector 56', '122011']],
        ] as $cityName => $areas) {
            foreach ($areas as [$name, $pincode]) {
                Area::updateOrCreate(['city_id' => $cities[$cityName]->id, 'slug' => Str::slug($name)], [
                    'name' => $name,
                    'pincode' => $pincode,
                    'travel_fee' => 0,
                    'is_active' => true,
                ]);
            }
        }

        foreach (['Ghaziabad', 'Faridabad'] as $inactiveCity) {
            CityPaymentSetting::where('slug', Str::slug($inactiveCity))->update(['is_active' => false]);
        }

        $categoryData = [
            ['Kids Activities & Games', 'Interactive entertainers, games and creative counters.', 'fa-gamepad'],
            ['Birthday Decoration', 'Balloon, stage, wall and theme birthday setups.', 'fa-cake-candles'],
            ['Anniversary Decoration', 'Romantic home and venue decoration experiences.', 'fa-heart'],
            ['New Born Baby Decoration', 'Welcome baby, hospital room and naming ceremony decor.', 'fa-baby'],
            ['Mascot & Entertainment', 'Mascots, jugglers and live entertainment.', 'fa-masks-theater'],
            ['Decoration Packages', 'Ready decoration combinations for easy booking.', 'fa-box-open'],
            ['Custom Packages', 'Flexible plans built around your venue and celebration.', 'fa-wand-magic-sparkles'],
        ];

        $categories = collect($categoryData)->mapWithKeys(function ($data, $index) use ($images) {
            $category = Category::updateOrCreate(['slug' => Str::slug($data[0])], [
                'name' => $data[0],
                'description' => $data[1],
                'icon' => $data[2],
                'image' => $images[$index % count($images)],
                'is_active' => true,
                'sort_order' => $index + 1,
                'meta_title' => $data[0].' in Delhi NCR',
                'meta_description' => $data[1],
                'meta_keywords' => strtolower($data[0]).', kids party planner, delhi ncr',
            ]);

            return [$data[0] => $category];
        });

        $subcategoryData = [
            'Kids Activities & Games' => ['Live Shows', 'Party Games', 'Art & Craft', 'Activity Counters'],
            'Birthday Decoration' => ['Simple Decoration', 'Ring Decoration', 'Wall Decoration', 'Stage Decoration', 'Birthday Themes'],
            'Anniversary Decoration' => ['Room Decoration', 'Romantic Setups'],
            'New Born Baby Decoration' => ['Welcome Baby', 'Hospital Room', 'Naming Ceremony'],
            'Mascot & Entertainment' => ['Mascots', 'Live Entertainment'],
            'Decoration Packages' => ['Birthday Packages', 'Celebration Combos'],
            'Custom Packages' => ['Build Your Party'],
        ];

        $subcategories = collect();
        foreach ($subcategoryData as $categoryName => $names) {
            foreach ($names as $index => $name) {
                $subcategory = Subcategory::updateOrCreate(['slug' => Str::slug($categoryName.'-'.$name)], [
                    'category_id' => $categories[$categoryName]->id,
                    'name' => $name,
                    'description' => $name.' services curated for celebrations across Delhi NCR.',
                    'image' => $images[$index % count($images)],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                    'meta_title' => $name.' Services in Delhi NCR',
                    'meta_description' => 'Browse and book '.$name.' with Kids Party Planner.',
                ]);
                $subcategories->put($categoryName.'|'.$name, $subcategory);
            }
        }

        $serviceData = [
            ['Magic Show', 2000, 'Kids Activities & Games', 'Live Shows', 'A lively comedy magic show with audience participation.'],
            ['Puppet Show', 2000, 'Kids Activities & Games', 'Live Shows', 'Interactive puppet stories, music and birthday moments.'],
            ['Tattoo Artist', 1500, 'Kids Activities & Games', 'Activity Counters', 'Party-safe temporary glitter tattoo counter.'],
            ['Bubble Activity', 2500, 'Kids Activities & Games', 'Live Shows', 'Giant bubbles, bubble games and photo moments.'],
            ['Game Coordinator', 2000, 'Kids Activities & Games', 'Party Games', 'Structured age-friendly games with an energetic host.'],
            ['Mascot', 3500, 'Mascot & Entertainment', 'Mascots', 'A favourite character mascot for entry, dance and photos.'],
            ['Face Painting', 2500, 'Kids Activities & Games', 'Activity Counters', 'Theme-based child-safe face art for party guests.'],
            ['Nail Art', 2200, 'Kids Activities & Games', 'Activity Counters', 'Kids nail paint, stickers and creative styling.'],
            ['Slime Making', 2500, 'Kids Activities & Games', 'Art & Craft', 'Guided colorful slime workshop with materials included.'],
            ['Art & Craft', 2800, 'Kids Activities & Games', 'Art & Craft', 'A supervised make-and-take creative activity counter.'],
            ['Caricature', 4500, 'Mascot & Entertainment', 'Live Entertainment', 'Quick live caricatures as memorable return keepsakes.'],
            ['Juggler', 3500, 'Mascot & Entertainment', 'Live Entertainment', 'Family-friendly juggling performance and interaction.'],
            ['Balloon Decoration', 2499, 'Birthday Decoration', 'Simple Decoration', 'A colorful balloon setup for homes and small venues.'],
            ['Simple Balloon Decoration', 2999, 'Birthday Decoration', 'Simple Decoration', 'Neat wall or corner decoration with balloons and foil.'],
            ['Ring Decoration', 5999, 'Birthday Decoration', 'Ring Decoration', 'Premium ring backdrop with balloon styling and name neon.'],
            ['Wall Decoration', 3999, 'Birthday Decoration', 'Wall Decoration', 'Balanced balloon wall decor for cake cutting and photographs.'],
            ['Stage Decoration', 8999, 'Birthday Decoration', 'Stage Decoration', 'Larger stage backdrop, balloons and celebration signage.'],
            ['Jungle Safari Theme', 10999, 'Birthday Decoration', 'Birthday Themes', 'Jungle props, animal cutouts and safari balloon styling.'],
            ['Mickey Mouse Theme', 9999, 'Birthday Decoration', 'Birthday Themes', 'Classic red, black and yellow Mickey-inspired party decor.'],
            ['Cocomelon Theme', 10999, 'Birthday Decoration', 'Birthday Themes', 'Bright Cocomelon-inspired backdrop and themed props.'],
            ['Baby Shark Theme', 9999, 'Birthday Decoration', 'Birthday Themes', 'Underwater color palette, sharks and ocean props.'],
            ['Spiderman Theme', 10999, 'Birthday Decoration', 'Birthday Themes', 'Red-blue superhero backdrop, webs and city props.'],
            ['Princess Theme', 11999, 'Birthday Decoration', 'Birthday Themes', 'Elegant princess setup with castle and pastel balloons.'],
            ['Unicorn Theme', 10999, 'Birthday Decoration', 'Birthday Themes', 'Pastel rainbow balloons and magical unicorn styling.'],
            ['Boss Baby Theme', 10999, 'Birthday Decoration', 'Birthday Themes', 'Black, blue and gold Boss Baby-inspired theme.'],
            ['Custom Theme Decoration', 14999, 'Custom Packages', 'Build Your Party', 'A custom visual concept tailored to the child and venue.'],
            ['Anniversary Decoration', 3999, 'Anniversary Decoration', 'Room Decoration', 'Romantic balloons, photos and warm light decor.'],
            ['Welcome Baby Boy Decoration', 6999, 'New Born Baby Decoration', 'Welcome Baby', 'Blue and neutral welcome baby decor for home arrival.'],
            ['Welcome Baby Girl Decoration', 6999, 'New Born Baby Decoration', 'Welcome Baby', 'Pink and pastel welcome baby decor for home arrival.'],
            ['Hospital Room Decoration', 4999, 'New Born Baby Decoration', 'Hospital Room', 'Compact hospital room decoration with quick setup.'],
            ['Home Welcome Decoration', 7999, 'New Born Baby Decoration', 'Welcome Baby', 'Entrance, living room and welcome signage decoration.'],
            ['Naming Ceremony Decoration', 11999, 'New Born Baby Decoration', 'Naming Ceremony', 'Traditional-modern backdrop for a naming celebration.'],
        ];

        $services = collect();
        foreach ($serviceData as $index => [$title, $price, $categoryName, $subcategoryName, $description]) {
            $service = Service::updateOrCreate(['slug' => Str::slug($title)], [
                'category_id' => $categories[$categoryName]->id,
                'subcategory_id' => $subcategories[$categoryName.'|'.$subcategoryName]->id,
                'title' => $title,
                'short_description' => $description,
                'description' => $description.' Delivered by a verified team with setup coordination across Delhi, Noida and Gurgaon.',
                'price' => $price,
                'discount_price' => $index % 4 === 0 ? round($price * .9) : null,
                'duration' => str_contains($title, 'Decoration') || str_contains($title, 'Theme') ? '2 to 3 hours setup' : '60 to 90 minutes',
                'age_group' => '3 to 14 years',
                'kids_capacity' => 25,
                'location' => 'Delhi, Noida, Gurgaon',
                'rating' => 4.7 + (($index % 3) / 10),
                'total_reviews' => 18 + $index,
                'inclusions' => ['Professional setup and teardown', 'Standard props and materials', 'Coordinator confirmation call', 'Travel within selected service area'],
                'exclusions' => ['Venue charges', 'Parking or entry charges', 'Cake and food'],
                'requirements' => ['Clear setup area', 'Power point near the activity', 'Venue access before event time'],
                'cancellation_policy' => 'Free date change up to 72 hours before the event, subject to availability.',
                'terms' => 'Final colors and props may vary slightly based on availability while preserving the selected theme.',
                'advance_percent' => 30,
                'featured' => $index < 12,
                'trending' => in_array($title, ['Magic Show', 'Ring Decoration', 'Jungle Safari Theme', 'Welcome Baby Girl Decoration']),
                'sort_order' => $index + 1,
                'is_active' => true,
                'meta_title' => $title.' in Delhi, Noida & Gurgaon',
                'meta_description' => 'Book '.$title.' online with Kids Party Planner. View price, inclusions, add-ons and available dates.',
                'meta_keywords' => strtolower($title).', kids party service, delhi, noida, gurgaon',
            ]);

            $service->images()->firstOrCreate(['is_primary' => true], ['path' => $images[$index % count($images)], 'alt_text' => $title]);
            $services->put($title, $service);

            foreach (['Delhi' => 0, 'Noida' => 100, 'Gurgaon' => 250] as $cityName => $difference) {
                ServiceCityPrice::updateOrCreate(['service_id' => $service->id, 'city_id' => $cities[$cityName]->id], [
                    'price' => $price + $difference,
                    'sale_price' => $index % 4 === 0 ? round(($price + $difference) * .9) : null,
                    'advance_percent' => $cityName === 'Gurgaon' ? 35 : 30,
                    'travel_fee' => 0,
                    'is_available' => true,
                ]);
            }
        }

        $requestedCategoryIds = $categories->pluck('id');
        Service::whereNotIn('id', $services->pluck('id'))
            ->whereNotIn('category_id', $requestedCategoryIds)
            ->update([
                'category_id' => $categories['Kids Activities & Games']->id,
                'subcategory_id' => $subcategories['Kids Activities & Games|Activity Counters']->id,
            ]);
        Category::whereNotIn('id', $requestedCategoryIds)->update(['is_active' => false, 'sort_order' => 999]);

        $addons = collect([
            ['Extra Balloons', 799, $images[0]], ['Name Foil Balloon', 499, $images[1]], ['Cake Table Decoration', 1499, $images[4]], ['Welcome Board', 1299, $images[2]],
            ['Mascot', 3500, $images[3]], ['Tattoo Artist', 1500, $images[4]], ['Face Painting', 2500, $images[3]], ['Popcorn Counter', 4500, $images[1]],
            ['Cotton Candy', 4500, $images[2]], ['Extra Game Coordinator', 2000, $images[0]], ['Extra Decoration Time', 1200, $images[4]],
        ])->mapWithKeys(function ($data) {
            $addon = Addon::updateOrCreate(['slug' => Str::slug($data[0])], ['name' => $data[0], 'description' => $data[0].' can be added to eligible services.', 'price' => $data[1], 'image' => $data[2], 'is_active' => true]);
            return [$data[0] => $addon];
        });

        foreach ($services as $service) {
            $eligible = $service->category->name === 'Birthday Decoration'
                ? $addons->only(['Extra Balloons', 'Name Foil Balloon', 'Cake Table Decoration', 'Welcome Board', 'Extra Decoration Time'])
                : $addons->only(['Mascot', 'Tattoo Artist', 'Face Painting', 'Popcorn Counter', 'Cotton Candy', 'Extra Game Coordinator']);
            $service->addons()->syncWithoutDetaching($eligible->pluck('id')->all());
        }

        foreach ([
            ['Tiny Star Party', 6999, 5999, ['Magic Show', 'Tattoo Artist', 'Game Coordinator']],
            ['Rainbow Bash', 11999, 9999, ['Puppet Show', 'Face Painting', 'Bubble Activity', 'Game Coordinator']],
            ['Creative Carnival', 14999, 12999, ['Slime Making', 'Art & Craft', 'Game Coordinator']],
            ['Premium Birthday Package', 24999, 21999, ['Ring Decoration', 'Magic Show', 'Face Painting', 'Game Coordinator']],
        ] as $index => [$title, $price, $discount, $included]) {
            $package = PartyPackage::updateOrCreate(['slug' => Str::slug($title)], [
                'title' => $title,
                'description' => 'A coordinated combination of popular services for an easier party plan.',
                'price' => $price,
                'discount_price' => $discount,
                'services' => $included,
                'inclusions' => ['Pre-event planning call', 'Selected services', 'Standard materials', 'On-ground coordination'],
                'image' => $images[($index + 2) % count($images)],
                'duration' => '2 to 3 hours',
                'featured' => true,
                'trending' => $index > 1,
                'is_active' => true,
                'terms' => 'Package services are delivered at one venue on the same date.',
                'meta_title' => $title.' in Delhi NCR',
                'meta_description' => 'Book '.$title.' online with Kids Party Planner.',
            ]);
            $package->includedServices()->sync(collect($included)->mapWithKeys(fn ($name) => [$services[$name]->id => ['quantity' => 1]])->all());
            $package->cities()->syncWithoutDetaching($cities->only(['Delhi', 'Noida', 'Gurgaon'])->pluck('id')->all());
        }

        foreach ([
            ['Turn moments into memories', 'Kids activities, themes and complete party plans across Delhi NCR.', $images[0], 'Explore services', '/services'],
            ['Birthday decor, booked in minutes', 'Choose your city, date, theme and payment preference online.', $images[4], 'View decorations', '/categories/birthday-decoration'],
        ] as $index => [$title, $subtitle, $image, $button, $url]) {
            Banner::updateOrCreate(['title' => $title], ['subtitle' => $subtitle, 'image' => $image, 'button_text' => $button, 'button_url' => $url, 'placement' => 'home', 'is_active' => true, 'sort_order' => $index + 1]);
        }

        foreach ([
            ['Which cities do you currently serve?', 'We currently accept bookings in Delhi, Noida and Gurgaon. Mumbai, Pune and Jaipur are planned next.'],
            ['How much advance is required?', 'Most bookings require 30% advance. The exact city and service amount appears before checkout.'],
            ['Can I customize a decoration theme?', 'Yes. Select Custom Theme Decoration or send your references through WhatsApp.'],
            ['Can I reschedule my booking?', 'Date changes requested at least 72 hours before the event are subject to team availability.'],
            ['Are materials included?', 'Standard materials and props listed under inclusions are covered in the displayed price.'],
            ['How do I track my booking?', 'Use the booking number and registered mobile number on the Track Booking page.'],
        ] as $index => [$question, $answer]) {
            Faq::updateOrCreate(['question' => $question, 'group' => 'general'], ['answer' => $answer, 'is_active' => true, 'sort_order' => $index + 1]);
        }

        User::where('email', 'admin@kidspartyplanner.test')->update(['email' => 'admin@kidspartyplanner.in']);
        User::updateOrCreate(['email' => 'admin@kidspartyplanner.in'], [
            'name' => 'Kids Party Planner Admin', 'phone' => '9910434330', 'role' => 'admin', 'city' => 'Delhi', 'address' => 'TC-37, Pandav Nagar, Shadipur, New Delhi - 110008', 'password' => 'password',
        ]);

        $vendorUser = User::updateOrCreate(['email' => 'vendor@example.com'], [
            'name' => 'Demo Vendor',
            'phone' => '7777777777',
            'role' => 'vendor',
            'city' => 'Delhi',
            'address' => 'Dwarka, New Delhi',
            'password' => 'password',
        ]);

        $vendor = Vendor::updateOrCreate(['user_id' => $vendorUser->id], [
            'city_id' => $cities['Delhi']->id,
            'business_name' => 'Delhi Party Vendor Team',
            'slug' => 'delhi-party-vendor-team',
            'contact_person' => 'Demo Vendor',
            'phone' => '7777777777',
            'email' => 'vendor@example.com',
            'city' => 'Delhi',
            'state' => 'Delhi',
            'address' => 'Dwarka, New Delhi',
            'coverage_areas' => ['Dwarka', 'Rohini', 'South Delhi'],
            'bank_details' => ['account_name' => 'Delhi Party Vendor Team', 'account_number' => '0000000000', 'ifsc' => 'TEST0000001'],
            'commission_percent' => 20,
            'status' => 'Approved',
            'approved_at' => now(),
        ]);

        $vendor->services()->syncWithoutDetaching($services->only(['Magic Show', 'Game Coordinator', 'Face Painting', 'Balloon Decoration'])->pluck('id')->all());

        foreach ([
            'site_name' => 'Kids Party Planner',
            'site_phone' => '+91 9910434330',
            'site_email' => 'sales@kidspartyplanner.in',
            'admin_email' => 'sales@kidspartyplanner.in',
            'whatsapp_number' => '919910434330',
            'instagram_url' => 'https://www.instagram.com/kidspartyplanner1/',
            'site_address' => 'TC-37, Pandav Nagar, Shadipur, New Delhi - 110008',
            'service_area' => 'Delhi, Noida, Gurgaon',
            'advance_percent' => '30',
            'meta_title' => 'Kids Party Planner - Activities & Decoration Booking in Delhi NCR',
            'meta_description' => 'Book kids activities, birthday decorations, welcome baby decor and party packages in Delhi, Noida and Gurgaon.',
        ] as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value, 'type' => 'text']);
        }
    }
}
