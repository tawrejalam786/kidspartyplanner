<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Addon;
use App\Models\Area;
use App\Models\Banner;
use App\Models\Booking;
use App\Models\BookingAssignment;
use App\Models\Category;
use App\Models\City;
use App\Models\CityPaymentSetting;
use App\Models\Coupon;
use App\Models\Enquiry;
use App\Models\Gallery;
use App\Models\Faq;
use App\Models\Package as PartyPackage;
use App\Models\Page;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceCityPrice;
use App\Models\ServiceImage;
use App\Models\Setting;
use App\Models\Subcategory;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorEarning;
use App\Models\VendorWithdrawal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ResourceController extends Controller
{
    public function index(Request $request, string $resource)
    {
        $config = $this->config($resource);
        $query = $this->query($config);
        $search = trim((string) $request->query('search'));

        if ($search !== '') {
            $this->applySearch($query, $config, $search);
        }

        return view('admin.resources.index', [
            'resource' => $resource,
            'config' => $config,
            'items' => $query->latest()->paginate(12)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function create(string $resource)
    {
        return view('admin.resources.form_v2', [
            'resource' => $resource,
            'config' => $this->config($resource),
            'item' => null,
        ]);
    }

    public function store(Request $request, string $resource)
    {
        $config = $this->config($resource);
        $data = $this->data($request, $config);

        if (in_array($resource, ['customers', 'admins'], true)) {
            $data['role'] = $resource === 'admins' ? 'admin' : 'customer';
            $data['password'] = $data['password'] ?? Str::random(14);
        }

        $item = $config['model']::create($data);
        $this->afterSave($request, $resource, $item);

        return redirect()->route('admin.resources.index', $resource)->with('success', $config['singular'].' created.');
    }

    public function edit(string $resource, int $id)
    {
        $config = $this->config($resource);

        return view('admin.resources.form_v2', [
            'resource' => $resource,
            'config' => $config,
            'item' => $this->query($config)->findOrFail($id),
        ]);
    }

    public function update(Request $request, string $resource, int $id)
    {
        $config = $this->config($resource);
        $item = $this->query($config)->findOrFail($id);
        $data = $this->data($request, $config, $item);
        $replacedFiles = collect($config['fields'])
            ->filter(fn ($field) => $field['type'] === 'file' && ! ($field['virtual'] ?? false) && array_key_exists($field['name'], $data))
            ->mapWithKeys(fn ($field) => [$field['name'] => data_get($item, $field['name'])])
            ->all();

        if (in_array($resource, ['customers', 'admins'], true)) {
            $data['role'] = $resource === 'admins' ? 'admin' : 'customer';
            if (empty($data['password'])) {
                unset($data['password']);
            }
        }

        $item->update($data);
        $this->deleteReplacedFiles($replacedFiles, $data);
        $this->afterSave($request, $resource, $item);

        return redirect()->route('admin.resources.index', $resource)->with('success', $config['singular'].' updated.');
    }

    public function destroy(string $resource, int $id)
    {
        $config = $this->config($resource);
        $this->query($config)->findOrFail($id)->delete();

        return back()->with('success', $config['singular'].' deleted.');
    }

    private function config(string $resource): array
    {
        $resources = [
            'cities' => [
                'model' => City::class, 'title' => 'City Management', 'singular' => 'City',
                'columns' => ['name', 'state', 'is_current', 'is_active', 'sort_order'],
                'fields' => [
                    ['name' => 'name', 'label' => 'City Name', 'type' => 'text', 'required' => true], ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'],
                    ['name' => 'state', 'label' => 'State', 'type' => 'text'], ['name' => 'image', 'label' => 'City Image', 'type' => 'file', 'folder' => 'cities'],
                    ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number'], ['name' => 'is_current', 'label' => 'Current Service City', 'type' => 'checkbox', 'default' => false],
                    ['name' => 'is_active', 'label' => 'Accept Bookings', 'type' => 'checkbox'], ['name' => 'meta_title', 'label' => 'Meta Title', 'type' => 'text'], ['name' => 'meta_description', 'label' => 'Meta Description', 'type' => 'textarea'],
                ],
            ],
            'areas' => [
                'model' => Area::class, 'title' => 'Area & Location Management', 'singular' => 'Area',
                'columns' => ['name', 'city.name', 'pincode', 'travel_fee', 'is_active'],
                'fields' => [
                    ['name' => 'city_id', 'label' => 'City', 'type' => 'select', 'required' => true, 'options' => City::orderBy('name')->pluck('name', 'id')->all()], ['name' => 'name', 'label' => 'Area Name', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'], ['name' => 'pincode', 'label' => 'Pincode', 'type' => 'text'], ['name' => 'travel_fee', 'label' => 'Travel Fee', 'type' => 'number'], ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox'],
                ],
            ],
            'subcategories' => [
                'model' => Subcategory::class, 'title' => 'Subcategory Management', 'singular' => 'Subcategory',
                'columns' => ['name', 'category.name', 'sort_order', 'is_active'],
                'fields' => [
                    ['name' => 'category_id', 'label' => 'Category', 'type' => 'select', 'required' => true, 'options' => Category::orderBy('name')->pluck('name', 'id')->all()], ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'], ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'], ['name' => 'image', 'label' => 'Image', 'type' => 'file', 'folder' => 'subcategories'],
                    ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number'], ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox'], ['name' => 'meta_title', 'label' => 'Meta Title', 'type' => 'text'], ['name' => 'meta_description', 'label' => 'Meta Description', 'type' => 'textarea'], ['name' => 'meta_keywords', 'label' => 'Meta Keywords', 'type' => 'text'], ['name' => 'og_image', 'label' => 'OG Image URL', 'type' => 'text'],
                ],
            ],
            'service-prices' => [
                'model' => ServiceCityPrice::class, 'title' => 'Service City Pricing', 'singular' => 'City Price',
                'columns' => ['service.title', 'city.name', 'price', 'sale_price', 'advance_percent', 'is_available'],
                'fields' => [
                    ['name' => 'service_id', 'label' => 'Service', 'type' => 'select', 'required' => true, 'options' => Service::orderBy('title')->pluck('title', 'id')->all()], ['name' => 'city_id', 'label' => 'City', 'type' => 'select', 'required' => true, 'options' => City::orderBy('name')->pluck('name', 'id')->all()],
                    ['name' => 'price', 'label' => 'Base Price', 'type' => 'number', 'required' => true], ['name' => 'sale_price', 'label' => 'Sale Price', 'type' => 'number'], ['name' => 'advance_percent', 'label' => 'Advance Percent', 'type' => 'number'], ['name' => 'travel_fee', 'label' => 'Travel Fee', 'type' => 'number'], ['name' => 'is_available', 'label' => 'Available', 'type' => 'checkbox'],
                ],
            ],
            'addons' => [
                'model' => Addon::class, 'title' => 'Add-on Management', 'singular' => 'Add-on',
                'columns' => ['image', 'name', 'price', 'is_active'],
                'fields' => [['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true], ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'], ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'], ['name' => 'price', 'label' => 'Price', 'type' => 'number', 'required' => true], ['name' => 'image', 'label' => 'Image', 'type' => 'file', 'folder' => 'addons'], ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox']],
            ],
            'service-images' => [
                'model' => ServiceImage::class, 'title' => 'Service Image Management', 'singular' => 'Service Image',
                'columns' => ['service.title', 'alt_text', 'is_primary'],
                'fields' => [['name' => 'service_id', 'label' => 'Service', 'type' => 'select', 'required' => true, 'options' => Service::orderBy('title')->pluck('title', 'id')->all()], ['name' => 'path', 'label' => 'Image', 'type' => 'file', 'folder' => 'services', 'required' => true], ['name' => 'alt_text', 'label' => 'Alt Text', 'type' => 'text'], ['name' => 'is_primary', 'label' => 'Primary Image', 'type' => 'checkbox', 'default' => false]],
            ],
            'payments' => [
                'model' => Payment::class, 'title' => 'Payment Management', 'singular' => 'Payment',
                'columns' => ['booking.booking_no', 'payment_id', 'amount', 'method', 'payment_status', 'created_at'],
                'fields' => [['name' => 'booking_id', 'label' => 'Booking', 'type' => 'select', 'required' => true, 'options' => Booking::latest()->pluck('booking_no', 'id')->all()], ['name' => 'amount', 'label' => 'Amount', 'type' => 'number', 'required' => true], ['name' => 'method', 'label' => 'Method', 'type' => 'text'], ['name' => 'payment_status', 'label' => 'Payment Status', 'type' => 'select', 'options' => ['Pending' => 'Pending', 'Paid' => 'Paid', 'Partially Paid' => 'Partially Paid', 'Failed' => 'Failed', 'Refunded' => 'Refunded']], ['name' => 'payment_id', 'label' => 'Gateway Payment ID', 'type' => 'text'], ['name' => 'razorpay_order_id', 'label' => 'Razorpay Order ID', 'type' => 'text']],
            ],
            'banners' => [
                'model' => Banner::class, 'title' => 'Banner Management', 'singular' => 'Banner',
                'columns' => ['image', 'title', 'placement', 'sort_order', 'is_active'],
                'fields' => [['name' => 'title', 'label' => 'Title', 'type' => 'text', 'help' => 'Leave title, subtitle and button text blank for image-only banner.'], ['name' => 'subtitle', 'label' => 'Subtitle', 'type' => 'textarea'], ['name' => 'image', 'label' => 'Banner Image', 'type' => 'file', 'folder' => 'banners', 'required' => true], ['name' => 'button_text', 'label' => 'Button Text', 'type' => 'text'], ['name' => 'button_url', 'label' => 'Button URL', 'type' => 'text'], ['name' => 'placement', 'label' => 'Placement', 'type' => 'text'], ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number'], ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox']],
            ],
            'faqs' => [
                'model' => Faq::class, 'title' => 'FAQ Management', 'singular' => 'FAQ',
                'columns' => ['question', 'group', 'service.title', 'sort_order', 'is_active'],
                'fields' => [['name' => 'service_id', 'label' => 'Service (optional)', 'type' => 'select', 'options' => ['' => 'General FAQ'] + Service::orderBy('title')->pluck('title', 'id')->all()], ['name' => 'question', 'label' => 'Question', 'type' => 'text', 'required' => true], ['name' => 'answer', 'label' => 'Answer', 'type' => 'textarea', 'required' => true], ['name' => 'group', 'label' => 'Group', 'type' => 'text'], ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number'], ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox']],
            ],
            'refunds' => [
                'model' => Refund::class, 'title' => 'Refund Management', 'singular' => 'Refund',
                'columns' => ['booking.booking_no', 'amount', 'status', 'gateway_refund_id', 'created_at'],
                'fields' => [['name' => 'booking_id', 'label' => 'Booking', 'type' => 'select', 'required' => true, 'options' => Booking::latest()->pluck('booking_no', 'id')->all()], ['name' => 'payment_id', 'label' => 'Payment', 'type' => 'select', 'options' => ['' => 'None'] + Payment::latest()->pluck('payment_id', 'id')->all()], ['name' => 'amount', 'label' => 'Amount', 'type' => 'number', 'required' => true], ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['Requested' => 'Requested', 'Processing' => 'Processing', 'Refunded' => 'Refunded', 'Rejected' => 'Rejected']], ['name' => 'reason', 'label' => 'Reason', 'type' => 'textarea', 'required' => true], ['name' => 'gateway_refund_id', 'label' => 'Gateway Refund ID', 'type' => 'text'], ['name' => 'admin_note', 'label' => 'Admin Note', 'type' => 'textarea']],
            ],
            'admins' => [
                'model' => User::class, 'title' => 'Admin User Management', 'singular' => 'Admin User', 'filter' => fn ($q) => $q->where('role', 'admin'),
                'columns' => ['name', 'email', 'phone', 'created_at'],
                'fields' => [['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true], ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true], ['name' => 'phone', 'label' => 'Phone', 'type' => 'text'], ['name' => 'city', 'label' => 'City', 'type' => 'text'], ['name' => 'password', 'label' => 'Password', 'type' => 'password']],
            ],
            'categories' => [
                'model' => Category::class,
                'title' => 'Category Management',
                'singular' => 'Category',
                'columns' => ['name', 'slug', 'is_active'],
                'fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                    ['name' => 'icon', 'label' => 'Font Awesome Icon', 'type' => 'text'],
                    ['name' => 'image', 'label' => 'Image', 'type' => 'file', 'folder' => 'categories'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox'],
                ],
            ],
            'services' => [
                'model' => Service::class,
                'title' => 'Service Management',
                'singular' => 'Service',
                'columns' => ['title', 'category.name', 'price', 'rating', 'featured', 'is_active'],
                'fields' => [
                    ['name' => 'category_id', 'label' => 'Category', 'type' => 'select', 'required' => true, 'options' => Category::orderBy('name')->pluck('name', 'id')->all()],
                    ['name' => 'subcategory_id', 'label' => 'Subcategory', 'type' => 'select', 'options' => ['' => 'None'] + Subcategory::orderBy('name')->pluck('name', 'id')->all()],
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'],
                    ['name' => 'short_description', 'label' => 'Short Description', 'type' => 'textarea'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                    ['name' => 'price', 'label' => 'Price', 'type' => 'number', 'required' => true],
                    ['name' => 'discount_price', 'label' => 'Discount Price', 'type' => 'number'],
                    ['name' => 'duration', 'label' => 'Duration', 'type' => 'text'],
                    ['name' => 'age_group', 'label' => 'Age Group', 'type' => 'text'],
                    ['name' => 'kids_capacity', 'label' => 'Kids Capacity', 'type' => 'number'],
                    ['name' => 'location', 'label' => 'Location', 'type' => 'text'],
                    ['name' => 'rating', 'label' => 'Rating', 'type' => 'number'],
                    ['name' => 'inclusions', 'label' => 'Inclusions JSON / lines', 'type' => 'json'],
                    ['name' => 'exclusions', 'label' => 'Exclusions JSON / lines', 'type' => 'json'],
                    ['name' => 'requirements', 'label' => 'Customer Requirements JSON / lines', 'type' => 'json'],
                    ['name' => 'add_ons', 'label' => 'Add-ons JSON', 'type' => 'json'],
                    ['name' => 'addon_ids', 'label' => 'Managed Add-ons', 'type' => 'multiselect', 'virtual' => true, 'relation' => 'addons', 'options' => Addon::where('is_active', true)->orderBy('name')->pluck('name', 'id')->all()],
                    ['name' => 'faq', 'label' => 'FAQ JSON', 'type' => 'json'],
                    ['name' => 'cancellation_policy', 'label' => 'Cancellation Policy', 'type' => 'textarea'],
                    ['name' => 'terms', 'label' => 'Service Terms', 'type' => 'textarea'],
                    ['name' => 'video_url', 'label' => 'Video URL', 'type' => 'text'],
                    ['name' => 'advance_percent', 'label' => 'Advance Percent', 'type' => 'number'],
                    ['name' => 'primary_image', 'label' => 'Primary Image', 'type' => 'file', 'folder' => 'services', 'virtual' => true],
                    ['name' => 'featured', 'label' => 'Featured', 'type' => 'checkbox'],
                    ['name' => 'trending', 'label' => 'Trending', 'type' => 'checkbox', 'default' => false],
                    ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox'],
                    ['name' => 'meta_title', 'label' => 'Meta Title', 'type' => 'text'],
                    ['name' => 'meta_description', 'label' => 'Meta Description', 'type' => 'textarea'],
                    ['name' => 'meta_keywords', 'label' => 'Meta Keywords', 'type' => 'text'],
                    ['name' => 'og_image', 'label' => 'OG Image URL', 'type' => 'text'],
                ],
            ],
            'packages' => [
                'model' => PartyPackage::class,
                'title' => 'Package Management',
                'singular' => 'Package',
                'columns' => ['title', 'price', 'discount_price', 'featured', 'is_active'],
                'fields' => [
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                    ['name' => 'price', 'label' => 'Price', 'type' => 'number', 'required' => true],
                    ['name' => 'discount_price', 'label' => 'Discount Price', 'type' => 'number'],
                    ['name' => 'services', 'label' => 'Services JSON / lines', 'type' => 'json'],
                    ['name' => 'service_ids', 'label' => 'Included Services', 'type' => 'multiselect', 'virtual' => true, 'relation' => 'includedServices', 'options' => Service::where('is_active', true)->orderBy('title')->pluck('title', 'id')->all()],
                    ['name' => 'city_ids', 'label' => 'Available Cities', 'type' => 'multiselect', 'virtual' => true, 'relation' => 'cities', 'options' => City::orderBy('sort_order')->pluck('name', 'id')->all()],
                    ['name' => 'inclusions', 'label' => 'Inclusions JSON / lines', 'type' => 'json'],
                    ['name' => 'image', 'label' => 'Image', 'type' => 'file', 'folder' => 'packages'],
                    ['name' => 'duration', 'label' => 'Duration', 'type' => 'text'],
                    ['name' => 'featured', 'label' => 'Featured', 'type' => 'checkbox'],
                    ['name' => 'trending', 'label' => 'Trending', 'type' => 'checkbox', 'default' => false],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox'],
                    ['name' => 'terms', 'label' => 'Package Terms', 'type' => 'textarea'],
                    ['name' => 'meta_title', 'label' => 'Meta Title', 'type' => 'text'],
                    ['name' => 'meta_description', 'label' => 'Meta Description', 'type' => 'textarea'],
                    ['name' => 'meta_keywords', 'label' => 'Meta Keywords', 'type' => 'text'],
                    ['name' => 'og_image', 'label' => 'OG Image URL', 'type' => 'text'],
                ],
            ],
            'bookings' => [
                'model' => Booking::class,
                'title' => 'Booking Management',
                'singular' => 'Booking',
                'columns' => ['booking_no', 'item_title', 'customer_name', 'cityPaymentSetting.city', 'event_date', 'status', 'payable_amount'],
                'fields' => [
                    ['name' => 'service_id', 'label' => 'Service', 'type' => 'select', 'options' => ['' => 'None'] + Service::orderBy('title')->pluck('title', 'id')->all()],
                    ['name' => 'package_id', 'label' => 'Package', 'type' => 'select', 'options' => ['' => 'None'] + PartyPackage::orderBy('title')->pluck('title', 'id')->all()],
                    ['name' => 'city_payment_setting_id', 'label' => 'Payment City', 'type' => 'select', 'options' => CityPaymentSetting::orderBy('city')->pluck('city', 'id')->all()],
                    ['name' => 'customer_name', 'label' => 'Customer Name', 'type' => 'text', 'required' => true],
                    ['name' => 'customer_email', 'label' => 'Customer Email', 'type' => 'email', 'required' => true],
                    ['name' => 'customer_phone', 'label' => 'Customer Phone', 'type' => 'text', 'required' => true],
                    ['name' => 'event_date', 'label' => 'Event Date', 'type' => 'date', 'required' => true],
                    ['name' => 'event_time', 'label' => 'Event Time', 'type' => 'time', 'required' => true],
                    ['name' => 'location', 'label' => 'Location', 'type' => 'text', 'required' => true],
                    ['name' => 'full_address', 'label' => 'Full Event Address', 'type' => 'textarea'],
                    ['name' => 'area_name', 'label' => 'Area / Locality', 'type' => 'text'],
                    ['name' => 'landmark', 'label' => 'Landmark', 'type' => 'text'],
                    ['name' => 'event_type', 'label' => 'Event Type', 'type' => 'text'],
                    ['name' => 'age_group', 'label' => 'Age Group', 'type' => 'text'],
                    ['name' => 'venue_type', 'label' => 'Venue Type', 'type' => 'select', 'options' => ['Home' => 'Home', 'Banquet' => 'Banquet', 'Society' => 'Society', 'School' => 'School', 'Outdoor' => 'Outdoor']],
                    ['name' => 'decoration_theme', 'label' => 'Decoration Theme', 'type' => 'text'],
                    ['name' => 'number_of_kids', 'label' => 'Number of Kids', 'type' => 'number'],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['Pending' => 'Pending', 'Confirmed' => 'Confirmed', 'Completed' => 'Completed', 'Cancelled' => 'Cancelled']],
                    ['name' => 'workflow_status', 'label' => 'Workflow Status', 'type' => 'select', 'options' => ['New' => 'New', 'Payment Pending' => 'Payment Pending', 'Confirmed' => 'Confirmed', 'Assigned' => 'Assigned', 'In Progress' => 'In Progress', 'Completed' => 'Completed', 'Cancelled' => 'Cancelled', 'Refund Requested' => 'Refund Requested', 'Refunded' => 'Refunded']],
                    ['name' => 'payment_status', 'label' => 'Payment Status', 'type' => 'select', 'options' => ['Pending' => 'Pending', 'Paid' => 'Paid', 'Partially Paid' => 'Partially Paid', 'Failed' => 'Failed', 'Refunded' => 'Refunded']],
                    ['name' => 'tracking_status', 'label' => 'Tracking Step', 'type' => 'select', 'options' => ['Booking Placed' => 'Booking Placed', 'Payment Received' => 'Payment Received', 'Booking Confirmed' => 'Booking Confirmed', 'Team Assigned' => 'Team Assigned', 'Team On The Way' => 'Team On The Way', 'Setup Started' => 'Setup Started', 'Completed' => 'Completed']],
                    ['name' => 'payment_type', 'label' => 'Payment Type', 'type' => 'select', 'options' => ['advance' => 'Advance', 'full' => 'Full']],
                    ['name' => 'total_amount', 'label' => 'Total Amount', 'type' => 'number'],
                    ['name' => 'advance_amount', 'label' => 'Advance Amount', 'type' => 'number'],
                    ['name' => 'payable_amount', 'label' => 'Payable Amount', 'type' => 'number'],
                    ['name' => 'message', 'label' => 'Message', 'type' => 'textarea'],
                ],
            ],
            'customers' => [
                'model' => User::class,
                'title' => 'Customer Management',
                'singular' => 'Customer',
                'filter' => fn ($q) => $q->where('role', 'customer'),
                'columns' => ['name', 'email', 'phone', 'city'],
                'fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                    ['name' => 'phone', 'label' => 'Phone', 'type' => 'text'],
                    ['name' => 'city', 'label' => 'City', 'type' => 'text'],
                    ['name' => 'address', 'label' => 'Address', 'type' => 'textarea'],
                    ['name' => 'password', 'label' => 'Password', 'type' => 'password'],
                ],
            ],
            'vendors' => [
                'model' => Vendor::class,
                'title' => 'Vendor Management',
                'singular' => 'Vendor',
                'columns' => ['business_name', 'contact_person', 'phone', 'city', 'status', 'commission_percent'],
                'fields' => [
                    ['name' => 'user_id', 'label' => 'Vendor Login User', 'type' => 'select', 'required' => true, 'options' => User::where('role', 'vendor')->orderBy('name')->pluck('name', 'id')->all()],
                    ['name' => 'city_id', 'label' => 'Primary City', 'type' => 'select', 'options' => ['' => 'None'] + City::orderBy('name')->pluck('name', 'id')->all()],
                    ['name' => 'business_name', 'label' => 'Business Name', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'],
                    ['name' => 'contact_person', 'label' => 'Contact Person', 'type' => 'text', 'required' => true],
                    ['name' => 'phone', 'label' => 'Phone', 'type' => 'text', 'required' => true],
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                    ['name' => 'city', 'label' => 'City', 'type' => 'text'],
                    ['name' => 'state', 'label' => 'State', 'type' => 'text'],
                    ['name' => 'address', 'label' => 'Address', 'type' => 'textarea'],
                    ['name' => 'coverage_areas', 'label' => 'Coverage Areas JSON / lines', 'type' => 'json'],
                    ['name' => 'bank_details', 'label' => 'Bank Details JSON', 'type' => 'json'],
                    ['name' => 'service_ids', 'label' => 'Services Vendor Can Handle', 'type' => 'multiselect', 'virtual' => true, 'relation' => 'services', 'options' => Service::where('is_active', true)->orderBy('title')->pluck('title', 'id')->all()],
                    ['name' => 'commission_percent', 'label' => 'Platform Commission Percent', 'type' => 'number'],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['Pending' => 'Pending', 'Approved' => 'Approved', 'Suspended' => 'Suspended', 'Rejected' => 'Rejected']],
                    ['name' => 'admin_note', 'label' => 'Admin Note', 'type' => 'textarea'],
                ],
            ],
            'booking-assignments' => [
                'model' => BookingAssignment::class,
                'title' => 'Booking Assignment Management',
                'singular' => 'Booking Assignment',
                'columns' => ['booking.booking_no', 'vendor.business_name', 'status', 'assigned_amount', 'vendor_earning'],
                'fields' => [
                    ['name' => 'booking_id', 'label' => 'Booking', 'type' => 'select', 'required' => true, 'options' => Booking::latest()->pluck('booking_no', 'id')->all()],
                    ['name' => 'vendor_id', 'label' => 'Vendor', 'type' => 'select', 'required' => true, 'options' => Vendor::where('status', 'Approved')->orderBy('business_name')->pluck('business_name', 'id')->all()],
                    ['name' => 'status', 'label' => 'Assignment Status', 'type' => 'select', 'options' => ['Assigned' => 'Assigned', 'Accepted' => 'Accepted', 'In Progress' => 'In Progress', 'Completed' => 'Completed', 'Cancelled' => 'Cancelled']],
                    ['name' => 'assigned_amount', 'label' => 'Assigned Amount', 'type' => 'number'],
                    ['name' => 'platform_commission', 'label' => 'Platform Commission', 'type' => 'number'],
                    ['name' => 'vendor_earning', 'label' => 'Vendor Earning', 'type' => 'number'],
                    ['name' => 'notes', 'label' => 'Assignment Notes', 'type' => 'textarea'],
                ],
            ],
            'vendor-earnings' => [
                'model' => VendorEarning::class,
                'title' => 'Vendor Earnings',
                'singular' => 'Vendor Earning',
                'columns' => ['vendor.business_name', 'booking.booking_no', 'gross_amount', 'commission_amount', 'net_amount', 'status'],
                'fields' => [
                    ['name' => 'vendor_id', 'label' => 'Vendor', 'type' => 'select', 'required' => true, 'options' => Vendor::orderBy('business_name')->pluck('business_name', 'id')->all()],
                    ['name' => 'booking_id', 'label' => 'Booking', 'type' => 'select', 'required' => true, 'options' => Booking::latest()->pluck('booking_no', 'id')->all()],
                    ['name' => 'booking_assignment_id', 'label' => 'Assignment', 'type' => 'select', 'options' => ['' => 'None'] + BookingAssignment::latest()->pluck('id', 'id')->all()],
                    ['name' => 'gross_amount', 'label' => 'Gross Amount', 'type' => 'number'],
                    ['name' => 'commission_amount', 'label' => 'Commission Amount', 'type' => 'number'],
                    ['name' => 'net_amount', 'label' => 'Net Amount', 'type' => 'number'],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['Pending' => 'Pending', 'Available' => 'Available', 'Paid' => 'Paid', 'Hold' => 'Hold']],
                ],
            ],
            'vendor-withdrawals' => [
                'model' => VendorWithdrawal::class,
                'title' => 'Vendor Withdrawals',
                'singular' => 'Vendor Withdrawal',
                'columns' => ['vendor.business_name', 'amount', 'status', 'payout_reference', 'created_at'],
                'fields' => [
                    ['name' => 'vendor_id', 'label' => 'Vendor', 'type' => 'select', 'required' => true, 'options' => Vendor::orderBy('business_name')->pluck('business_name', 'id')->all()],
                    ['name' => 'amount', 'label' => 'Amount', 'type' => 'number', 'required' => true],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['Requested' => 'Requested', 'Approved' => 'Approved', 'Paid' => 'Paid', 'Rejected' => 'Rejected']],
                    ['name' => 'bank_details', 'label' => 'Bank Details JSON', 'type' => 'json'],
                    ['name' => 'payout_reference', 'label' => 'Payout Reference', 'type' => 'text'],
                    ['name' => 'admin_note', 'label' => 'Admin Note', 'type' => 'textarea'],
                ],
            ],
            'city-payments' => [
                'model' => CityPaymentSetting::class,
                'title' => 'City Payment Settings',
                'singular' => 'City Payment Setting',
                'columns' => ['city', 'advance_percent', 'service_fee', 'tax_percent', 'is_default', 'is_active'],
                'fields' => [
                    ['name' => 'city', 'label' => 'City', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'City Slug', 'type' => 'text'],
                    ['name' => 'advance_percent', 'label' => 'Advance Percent', 'type' => 'number', 'required' => true],
                    ['name' => 'minimum_advance', 'label' => 'Minimum Advance', 'type' => 'number'],
                    ['name' => 'service_fee', 'label' => 'Convenience Fee', 'type' => 'number'],
                    ['name' => 'tax_percent', 'label' => 'Tax Percent', 'type' => 'number'],
                    ['name' => 'razorpay_key_id', 'label' => 'Razorpay Key ID', 'type' => 'text'],
                    ['name' => 'razorpay_key_secret', 'label' => 'Razorpay Key Secret', 'type' => 'password'],
                    ['name' => 'razorpay_webhook_secret', 'label' => 'Razorpay Webhook Secret', 'type' => 'password'],
                    ['name' => 'payment_instructions', 'label' => 'Customer Payment Note', 'type' => 'textarea'],
                    ['name' => 'is_default', 'label' => 'Default City', 'type' => 'checkbox', 'default' => false],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox'],
                ],
            ],
            'reviews' => [
                'model' => Review::class,
                'title' => 'Review Management',
                'singular' => 'Review',
                'columns' => ['customer_name', 'rating', 'service.title', 'is_approved'],
                'fields' => [
                    ['name' => 'service_id', 'label' => 'Service', 'type' => 'select', 'options' => ['' => 'None'] + Service::orderBy('title')->pluck('title', 'id')->all()],
                    ['name' => 'package_id', 'label' => 'Package', 'type' => 'select', 'options' => ['' => 'None'] + PartyPackage::orderBy('title')->pluck('title', 'id')->all()],
                    ['name' => 'customer_name', 'label' => 'Customer Name', 'type' => 'text', 'required' => true],
                    ['name' => 'rating', 'label' => 'Rating', 'type' => 'number', 'required' => true],
                    ['name' => 'comment', 'label' => 'Comment', 'type' => 'textarea', 'required' => true],
                    ['name' => 'is_approved', 'label' => 'Approved', 'type' => 'checkbox'],
                ],
            ],
            'galleries' => [
                'model' => Gallery::class,
                'title' => 'Gallery Management',
                'singular' => 'Gallery Item',
                'columns' => ['title', 'type', 'sort_order', 'is_active'],
                'fields' => [
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'required' => true],
                    ['name' => 'type', 'label' => 'Type', 'type' => 'text'],
                    ['name' => 'image', 'label' => 'Image', 'type' => 'file', 'folder' => 'gallery', 'required' => true],
                    ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox'],
                ],
            ],
            'enquiries' => [
                'model' => Enquiry::class,
                'title' => 'Enquiry Management',
                'singular' => 'Enquiry',
                'columns' => ['name', 'phone', 'subject', 'status'],
                'fields' => [
                    ['name' => 'service_id', 'label' => 'Service', 'type' => 'select', 'options' => ['' => 'None'] + Service::orderBy('title')->pluck('title', 'id')->all()],
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
                    ['name' => 'phone', 'label' => 'Phone', 'type' => 'text', 'required' => true],
                    ['name' => 'subject', 'label' => 'Subject', 'type' => 'text'],
                    ['name' => 'message', 'label' => 'Message', 'type' => 'textarea', 'required' => true],
                    ['name' => 'source', 'label' => 'Source', 'type' => 'text'],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['New' => 'New', 'Contacted' => 'Contacted', 'Closed' => 'Closed']],
                ],
            ],
            'coupons' => [
                'model' => Coupon::class,
                'title' => 'Coupon Management',
                'singular' => 'Coupon',
                'columns' => ['code', 'type', 'value', 'expires_at', 'is_active'],
                'fields' => [
                    ['name' => 'code', 'label' => 'Code', 'type' => 'text', 'required' => true],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'text'],
                    ['name' => 'type', 'label' => 'Type', 'type' => 'select', 'options' => ['fixed' => 'Fixed', 'percent' => 'Percent']],
                    ['name' => 'value', 'label' => 'Value', 'type' => 'number', 'required' => true],
                    ['name' => 'min_order', 'label' => 'Minimum Order', 'type' => 'number'],
                    ['name' => 'max_discount', 'label' => 'Max Discount', 'type' => 'number'],
                    ['name' => 'starts_at', 'label' => 'Starts At', 'type' => 'datetime'],
                    ['name' => 'expires_at', 'label' => 'Expires At', 'type' => 'datetime'],
                    ['name' => 'usage_limit', 'label' => 'Usage Limit', 'type' => 'number'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox'],
                ],
            ],
            'blogs' => [
                'model' => Blog::class,
                'title' => 'Blog Management',
                'singular' => 'Blog Post',
                'columns' => ['title', 'slug', 'published_at', 'is_active'],
                'fields' => [
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'],
                    ['name' => 'excerpt', 'label' => 'Excerpt', 'type' => 'textarea'],
                    ['name' => 'content', 'label' => 'Content', 'type' => 'textarea', 'required' => true],
                    ['name' => 'image', 'label' => 'Image', 'type' => 'file', 'folder' => 'blogs'],
                    ['name' => 'published_at', 'label' => 'Published At', 'type' => 'datetime'],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox'],
                    ['name' => 'meta_title', 'label' => 'Meta Title', 'type' => 'text'],
                    ['name' => 'meta_description', 'label' => 'Meta Description', 'type' => 'textarea'],
                ],
            ],
            'pages' => [
                'model' => Page::class,
                'title' => 'CMS Page Management',
                'singular' => 'Page',
                'columns' => ['title', 'slug', 'is_active'],
                'fields' => [
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'required' => true],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'],
                    ['name' => 'content', 'label' => 'Content', 'type' => 'textarea', 'required' => true],
                    ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox'],
                    ['name' => 'meta_title', 'label' => 'Meta Title', 'type' => 'text'],
                    ['name' => 'meta_description', 'label' => 'Meta Description', 'type' => 'textarea'],
                ],
            ],
            'settings' => [
                'model' => Setting::class,
                'title' => 'Website Settings',
                'singular' => 'Setting',
                'columns' => ['key', 'value', 'type'],
                'fields' => [
                    ['name' => 'key', 'label' => 'Key', 'type' => 'text', 'required' => true],
                    ['name' => 'value', 'label' => 'Value', 'type' => 'textarea'],
                    ['name' => 'type', 'label' => 'Type', 'type' => 'text'],
                ],
            ],
        ];

        abort_unless(isset($resources[$resource]), 404);

        return $resources[$resource];
    }

    private function query(array $config)
    {
        $query = $config['model']::query();

        if (isset($config['filter'])) {
            $config['filter']($query);
        }

        return $query;
    }

    private function applySearch(Builder $query, array $config, string $search): void
    {
        $model = new $config['model'];
        $columns = Schema::getColumnListing($model->getTable());
        $excluded = ['password', 'razorpay_key_secret', 'razorpay_webhook_secret', 'signature', 'raw_response'];
        $searchable = collect($config['fields'])
            ->filter(fn ($field) => ! ($field['virtual'] ?? false)
                && in_array($field['type'], ['text', 'email', 'number', 'date', 'datetime', 'textarea'], true)
                && in_array($field['name'], $columns, true)
                && ! in_array($field['name'], $excluded, true))
            ->pluck('name')
            ->unique()
            ->values();
        $relationColumns = collect($config['columns'])
            ->filter(fn ($column) => str_contains($column, '.'))
            ->map(fn ($column) => explode('.', $column, 2))
            ->values();

        $query->where(function (Builder $builder) use ($model, $searchable, $relationColumns, $search) {
            foreach ($searchable as $column) {
                $builder->orWhere($model->qualifyColumn($column), 'like', '%'.$search.'%');
            }

            foreach ($relationColumns as [$relation, $column]) {
                $builder->orWhereHas($relation, fn (Builder $related) => $related->where($column, 'like', '%'.$search.'%'));
            }
        });
    }

    private function data(Request $request, array $config, ?Model $item = null): array
    {
        if (collect($config['fields'])->contains('name', 'slug') && ! $request->filled('slug')) {
            $request->merge([
                'slug' => Str::slug($request->input('title', $request->input('name', $request->input('city', Str::random(8))))),
            ]);
        }

        $rules = [];
        foreach ($config['fields'] as $field) {
            if ($field['type'] === 'multiselect') {
                $rules[$field['name']] = ['nullable', 'array'];
                $rules[$field['name'].'.*'] = [Rule::in(array_map('strval', array_keys($field['options'])))];
                continue;
            }

            if ($field['type'] === 'file') {
                $rule = ($field['required'] ?? false) && ! $item ? 'required' : 'nullable';
                $rules[$field['name']] = [$rule, 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
                continue;
            }

            if ($field['type'] === 'checkbox') {
                continue;
            }

            $rule = ($field['required'] ?? false) ? ['required'] : ['nullable'];
            $rule[] = match ($field['type']) {
                'email' => 'email',
                'number' => 'numeric',
                'date' => 'date',
                'datetime' => 'date',
                'password' => 'string',
                default => 'string',
            };

            if ($field['name'] === 'email' && ($config['model'] ?? null) === User::class) {
                $rule[] = Rule::unique('users', 'email')->ignore($item?->id);
            }

            if ($field['name'] === 'slug') {
                $rule[] = Rule::unique((new $config['model'])->getTable(), 'slug')->ignore($item?->id);
            }

            $rules[$field['name']] = $rule;
        }

        $validated = $request->validate($rules);
        $data = [];

        foreach ($config['fields'] as $field) {
            $name = $field['name'];

            if ($field['type'] === 'checkbox') {
                $data[$name] = $request->boolean($name);
                continue;
            }

            if (($field['virtual'] ?? false) === true) {
                continue;
            }

            if ($field['type'] === 'file') {
                if ($request->hasFile($name)) {
                    $data[$name] = $request->file($name)->store($field['folder'] ?? 'uploads', 'public');
                }
                continue;
            }

            if (! array_key_exists($name, $validated)) {
                continue;
            }

            if ($field['type'] === 'json') {
                $data[$name] = $this->jsonValue($validated[$name] ?? null);
                continue;
            }

            if ($name === 'slug' && empty($validated[$name])) {
                $data[$name] = Str::slug($request->input('title', $request->input('name', $request->input('city', Str::random(8)))));
                continue;
            }

            if ($name === 'code' && ! empty($validated[$name])) {
                $data[$name] = Str::upper($validated[$name]);
                continue;
            }

            if ($field['type'] === 'password' && empty($validated[$name])) {
                continue;
            }

            if (in_array($field['type'], ['select', 'number', 'date', 'time', 'datetime'], true) && $validated[$name] === '') {
                $data[$name] = null;
                continue;
            }

            $data[$name] = $validated[$name];
        }

        if (isset($data['booking_no']) === false && ($config['model'] ?? null) === Booking::class && ! $item) {
            $data['booking_no'] = 'KPP-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        }

        if (($config['model'] ?? null) === Banner::class && array_key_exists('title', $data)) {
            $data['title'] = $data['title'] ?? '';
        }

        return $data;
    }

    private function jsonValue(?string $value): ?array
    {
        if (! $value) {
            return null;
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return collect(preg_split('/\r\n|\r|\n/', $value))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    private function afterSave(Request $request, string $resource, Model $item): void
    {
        if ($resource === 'services') {
            $item->addons()->sync($request->input('addon_ids', []));
        }

        if ($resource === 'vendors') {
            $item->services()->sync($request->input('service_ids', []));
            if ($item->status === 'Approved' && ! $item->approved_at) {
                $item->forceFill(['approved_at' => now()])->save();
            }
        }

        if ($resource === 'booking-assignments') {
            $this->syncAssignmentFinancials($item);
        }

        if ($resource === 'vendor-withdrawals' && in_array($item->status, ['Paid', 'Rejected'], true) && ! $item->processed_at) {
            $item->forceFill(['processed_at' => now()])->save();
            if ($item->status === 'Paid') {
                $this->markWithdrawalPaid($item);
            }
        }

        if ($resource === 'packages') {
            $item->includedServices()->sync($request->input('service_ids', []));
            $item->cities()->sync($request->input('city_ids', []));
        }

        if ($resource === 'service-images' && $item->is_primary) {
            ServiceImage::where('service_id', $item->service_id)->whereKeyNot($item->id)->update(['is_primary' => false]);
        }

        if ($resource === 'city-payments' && $item->is_default) {
            CityPaymentSetting::whereKeyNot($item->id)->update(['is_default' => false]);
        }

        if ($resource !== 'services' || ! $request->hasFile('primary_image')) {
            return;
        }

        $path = $request->file('primary_image')->store('services', 'public');
        $primaryImage = ServiceImage::where('service_id', $item->id)->where('is_primary', true)->first();
        $oldPath = $primaryImage?->path;
        $otherImages = ServiceImage::where('service_id', $item->id);
        if ($primaryImage) {
            $otherImages->whereKeyNot($primaryImage->id);
        }
        $otherImages->update(['is_primary' => false]);

        if ($primaryImage) {
            $primaryImage->update([
                'path' => $path,
                'alt_text' => $item->title,
                'is_primary' => true,
            ]);
        } else {
            $item->images()->create([
                'path' => $path,
                'alt_text' => $item->title,
                'is_primary' => true,
            ]);
        }

        if ($oldPath && $oldPath !== $path && ! Str::startsWith($oldPath, ['http://', 'https://'])) {
            Storage::disk('public')->delete($oldPath);
        }
    }

    private function deleteReplacedFiles(array $replacedFiles, array $data): void
    {
        foreach ($replacedFiles as $name => $oldPath) {
            if (! $oldPath || $oldPath === ($data[$name] ?? null) || Str::startsWith($oldPath, ['http://', 'https://'])) {
                continue;
            }

            Storage::disk('public')->delete($oldPath);
        }
    }

    public static function resources(): string
    {
        return 'cities|areas|categories|subcategories|services|service-images|service-prices|addons|packages|bookings|payments|customers|admins|vendors|booking-assignments|vendor-earnings|vendor-withdrawals|city-payments|reviews|galleries|banners|faqs|enquiries|coupons|refunds|blogs|pages|settings';
    }

    private function syncAssignmentFinancials(BookingAssignment $assignment): void
    {
        $assignment->loadMissing(['booking', 'vendor']);
        $assignedAmount = (float) ($assignment->assigned_amount ?: $assignment->booking->total_amount);
        $commission = (float) ($assignment->platform_commission ?: round($assignedAmount * ((float) $assignment->vendor->commission_percent / 100), 2));
        $vendorEarning = (float) ($assignment->vendor_earning ?: max(0, $assignedAmount - $commission));

        $assignment->forceFill([
            'assigned_by' => $assignment->assigned_by ?: auth()->id(),
            'assigned_at' => $assignment->assigned_at ?: now(),
            'assigned_amount' => $assignedAmount,
            'platform_commission' => $commission,
            'vendor_earning' => $vendorEarning,
            'completed_at' => $assignment->status === 'Completed' ? ($assignment->completed_at ?: now()) : $assignment->completed_at,
        ])->save();

        VendorEarning::updateOrCreate(
            ['booking_assignment_id' => $assignment->id],
            [
                'vendor_id' => $assignment->vendor_id,
                'booking_id' => $assignment->booking_id,
                'gross_amount' => $assignedAmount,
                'commission_amount' => $commission,
                'net_amount' => $vendorEarning,
                'status' => $assignment->status === 'Completed' ? 'Available' : 'Pending',
                'available_at' => $assignment->status === 'Completed' ? now() : null,
            ]
        );

        $assignment->booking->update([
            'workflow_status' => $assignment->status === 'Completed' ? 'Completed' : 'Assigned',
            'tracking_status' => $assignment->status === 'Completed' ? 'Completed' : 'Team Assigned',
        ]);
    }

    private function markWithdrawalPaid(VendorWithdrawal $withdrawal): void
    {
        $remaining = (float) $withdrawal->amount;
        $earnings = VendorEarning::where('vendor_id', $withdrawal->vendor_id)
            ->where('status', 'Available')
            ->oldest()
            ->get();

        foreach ($earnings as $earning) {
            if ($remaining <= 0) {
                break;
            }

            $remaining -= (float) $earning->net_amount;
            $earning->update(['status' => 'Paid', 'paid_at' => now()]);
        }
    }
}
