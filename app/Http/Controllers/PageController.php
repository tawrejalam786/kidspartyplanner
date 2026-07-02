<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Gallery;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Review;
use App\Models\Service;

class PageController extends Controller
{
    public function about()
    {
        $page = Page::where('slug', 'about')->firstOrFail();

        return view('pages.about', [
            'metaTitle' => $page->meta_title,
            'metaDescription' => $page->meta_description,
            'page' => $page,
        ]);
    }

    public function gallery()
    {
        return view('pages.gallery', [
            'metaTitle' => 'Kids Party Gallery',
            'metaDescription' => 'See kids birthday party setups and activities by Kids Party Planner.',
            'gallery' => Gallery::where('is_active', true)->orderBy('sort_order')->paginate(12),
        ]);
    }

    public function reviews()
    {
        return view('pages.reviews', [
            'metaTitle' => 'Customer Reviews',
            'metaDescription' => 'Read parent reviews for Kids Party Planner services.',
            'reviews' => Review::where('is_approved', true)->latest()->paginate(12),
            'services' => Service::where('is_active', true)->orderBy('title')->get(),
        ]);
    }

    public function contact()
    {
        return view('pages.contact', [
            'metaTitle' => 'Contact Kids Party Planner',
            'metaDescription' => 'Contact Kids Party Planner for birthday services in Delhi NCR.',
            'services' => Service::where('is_active', true)->orderBy('title')->get(),
        ]);
    }

    public function faq()
    {
        return view('pages.faq', [
            'metaTitle' => 'Frequently Asked Questions | Kids Party Planner',
            'metaDescription' => 'Answers about kids party booking, payments, cities, rescheduling and service inclusions.',
            'faqs' => Faq::where('is_active', true)->whereNull('service_id')->orderBy('group')->orderBy('sort_order')->get()->groupBy('group'),
        ]);
    }

    public function policy(string $slug)
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return view('pages.policy', [
            'metaTitle' => $page->meta_title,
            'metaDescription' => $page->meta_description,
            'page' => $page,
        ]);
    }

    public function blogIndex()
    {
        return view('pages.blog-index', [
            'metaTitle' => 'Party Planning Blog',
            'metaDescription' => 'Ideas and guides for kids birthday parties in Delhi NCR.',
            'blogs' => Blog::where('is_active', true)->latest('published_at')->paginate(9),
        ]);
    }

    public function blogShow(Blog $blog)
    {
        abort_unless($blog->is_active, 404);

        return view('pages.blog-show', [
            'metaTitle' => $blog->meta_title ?: $blog->title,
            'metaDescription' => $blog->meta_description ?: $blog->excerpt,
            'blog' => $blog,
        ]);
    }

    public function sitemap()
    {
        $services = Service::where('is_active', true)->get();
        $blogs = Blog::where('is_active', true)->get();

        return response()
            ->view('sitemap', compact('services', 'blogs'))
            ->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        return response("User-agent: *\nAllow: /\nSitemap: ".route('sitemap')."\n", 200)
            ->header('Content-Type', 'text/plain');
    }
}
