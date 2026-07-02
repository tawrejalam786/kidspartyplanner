<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach([route('home'), route('about'), route('services.index'), route('packages.index'), route('gallery'), route('reviews'), route('contact'), route('terms'), route('privacy'), route('refund'), route('blog.index')] as $url)
        <url><loc>{{ $url }}</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>
    @endforeach
    @foreach($services as $service)
        <url><loc>{{ route('services.show', $service) }}</loc><changefreq>weekly</changefreq><priority>0.7</priority></url>
    @endforeach
    @foreach($blogs as $blog)
        <url><loc>{{ route('blog.show', $blog) }}</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>
    @endforeach
</urlset>
