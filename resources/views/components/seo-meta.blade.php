@php
    $seoConfig = config('seo');
    $defaultConfig = $seoConfig['default'];
    $pageConfig = $seoConfig['pages'][$page ?? 'default'] ?? [];
    
    // Merge page-specific config with defaults
    $title = $pageConfig['title'] ?? $defaultConfig['site_name'];
    $description = $pageConfig['description'] ?? $defaultConfig['site_description'];
    $keywords = $pageConfig['keywords'] ?? $defaultConfig['site_keywords'];
    $canonical = $pageConfig['canonical'] ?? request()->url();
    $robots = $pageConfig['robots'] ?? 'index, follow';
    
    // Open Graph data
    $ogTitle = $ogTitle ?? $title;
    $ogDescription = $ogDescription ?? $description;
    $ogImage = $ogImage ?? $defaultConfig['site_logo'];
    $ogUrl = $ogUrl ?? $canonical;
    $ogType = $ogType ?? $seoConfig['open_graph']['type'];
    
    // Twitter Card data
    $twitterCard = $twitterCard ?? $seoConfig['twitter']['card'];
    $twitterSite = $twitterSite ?? $seoConfig['twitter']['site'];
    $twitterCreator = $twitterCreator ?? $seoConfig['twitter']['creator'];
    
    // Structured data
    $structuredData = $structuredData ?? [];
    $organizationData = $seoConfig['structured_data']['organization'];
    $webSiteData = $seoConfig['structured_data']['web_site'];
    $webApplicationData = $seoConfig['structured_data']['web_application'];
    
    // Analytics
    // Google Analytics and Google Tag Manager removed to prevent doubleclick requests
    $facebookPixel = $seoConfig['analytics']['facebook_pixel'];
@endphp

<!-- Primary Meta Tags -->
<title>{{ $title }}</title>
<meta name="title" content="{{ $title }}">
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<meta name="author" content="{{ $defaultConfig['site_author'] }}">
<meta name="robots" content="{{ $robots }}">
<meta name="language" content="{{ $defaultConfig['site_language'] }}">
<meta name="revisit-after" content="7 days">
<meta name="distribution" content="global">
<meta name="rating" content="general">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">

<!-- Canonical URL -->
<link rel="canonical" href="{{ $canonical }}">

<!-- Alternate Language Versions -->
<link rel="alternate" hreflang="id" href="{{ $canonical }}">
<link rel="alternate" hreflang="x-default" href="{{ $canonical }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:url" content="{{ $ogUrl }}">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="{{ $ogTitle }}">
<meta property="og:site_name" content="{{ $defaultConfig['site_name'] }}">
<meta property="og:locale" content="{{ $seoConfig['open_graph']['locale'] }}">
<meta property="og:updated_time" content="{{ now()->toISOString() }}">

<!-- Twitter Card -->
<meta name="twitter:card" content="{{ $twitterCard }}">
<meta name="twitter:url" content="{{ $ogUrl }}">
<meta name="twitter:title" content="{{ $ogTitle }}">
<meta name="twitter:description" content="{{ $ogDescription }}">
<meta name="twitter:image" content="{{ $ogImage }}">
<meta name="twitter:image:alt" content="{{ $ogTitle }}">
@if($twitterSite)
<meta name="twitter:site" content="{{ $twitterSite }}">
@endif
@if($twitterCreator)
<meta name="twitter:creator" content="{{ $twitterCreator }}">
@endif

<!-- Additional Meta Tags -->
<meta name="theme-color" content="#667eea">
<meta name="msapplication-TileColor" content="#667eea">
<meta name="msapplication-config" content="/browserconfig.xml">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="{{ $defaultConfig['site_name'] }}">
<meta name="application-name" content="{{ $defaultConfig['site_name'] }}">
<meta name="mobile-web-app-capable" content="yes">

<!-- Favicon and Icons -->
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">

<!-- Preconnect to external domains for performance -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://cdnjs.cloudflare.com">
<link rel="preconnect" href="https://cdn.jsdelivr.net">

<!-- DNS Prefetch for external resources -->
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
<link rel="dns-prefetch" href="//cdn.jsdelivr.net">

<!-- Structured Data - Organization -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "{{ $organizationData['@type'] }}",
    "name": "{{ $organizationData['name'] }}",
    "url": "{{ $organizationData['url'] }}",
    "logo": "{{ $organizationData['logo'] }}",
    "description": "{{ $organizationData['description'] }}",
    "address": {
        "@type": "{{ $organizationData['address']['@type'] }}",
        "addressCountry": "{{ $organizationData['address']['addressCountry'] }}",
        "addressRegion": "{{ $organizationData['address']['addressRegion'] }}"
    },
    "contactPoint": {
        "@type": "{{ $organizationData['contactPoint']['@type'] }}",
        "telephone": "{{ $organizationData['contactPoint']['telephone'] }}",
        "contactType": "{{ $organizationData['contactPoint']['contactType'] }}",
        "availableLanguage": {{ json_encode($organizationData['contactPoint']['availableLanguage']) }}
    }
}
</script>

<!-- Structured Data - WebSite -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "{{ $webSiteData['@type'] }}",
    "name": "{{ $webSiteData['name'] }}",
    "url": "{{ $webSiteData['url'] }}",
    "description": "{{ $webSiteData['description'] }}",
    "potentialAction": {
        "@type": "{{ $webSiteData['potentialAction']['@type'] }}",
        "target": "{{ $webSiteData['potentialAction']['target'] }}",
        "query-input": "{{ $webSiteData['potentialAction']['query-input'] }}"
    }
}
</script>

<!-- Structured Data - WebApplication -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "{{ $webApplicationData['@type'] }}",
    "name": "{{ $webApplicationData['name'] }}",
    "url": "{{ $webApplicationData['url'] }}",
    "description": "{{ $webApplicationData['description'] }}",
    "applicationCategory": "{{ $webApplicationData['applicationCategory'] }}",
    "operatingSystem": "{{ $webApplicationData['operatingSystem'] }}",
    "offers": {
        "@type": "{{ $webApplicationData['offers']['@type'] }}",
        "price": "{{ $webApplicationData['offers']['price'] }}",
        "priceCurrency": "{{ $webApplicationData['offers']['priceCurrency'] }}"
    }
}
</script>

@if(!empty($structuredData))
<!-- Additional Structured Data -->
@foreach($structuredData as $data)
<script type="application/ld+json">
{!! json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endforeach
@endif

<!-- Facebook Pixel -->
@if($facebookPixel)
<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{{ $facebookPixel }}');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={{ $facebookPixel }}&ev=PageView&noscript=1"
/></noscript>
@endif

<!-- Performance and Security Headers -->
<meta http-equiv="X-Content-Type-Options" content="nosniff">
<!-- X-Frame-Options must be set via HTTP headers, not meta tags -->
<meta http-equiv="X-XSS-Protection" content="1; mode=block">
<meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">
<meta http-equiv="Permissions-Policy" content="camera=(), microphone=(), geolocation=()">

<!-- Preload Critical Resources -->
@if(config('seo.performance.preload_critical_resources', true))
{{-- Vite handles CSS/JS preloading automatically, so we only preload external resources --}}
<link rel="preload" href="https://cdn.tailwindcss.com" as="script">
<link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" as="style">
@endif
