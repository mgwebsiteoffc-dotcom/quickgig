{{-- SEO + AEO component — @include('components.seo', ['seo'=>$seo]) --}}
@php
  $seo = $seo ?? [];
  $title = $seo['title'] ?? 'Quick GIGS — Hire verified creators in minutes, not weeks';
  $desc = $seo['description'] ?? 'Quick GIGS is the fast gig marketplace for reels, thumbnails, AI video and design. Post a brief, get matched to a verified pro in minutes, track delivery live and pay only when you approve.';
  $canonical = $seo['canonical'] ?? url()->current();
  $image = $seo['image'] ?? url('/og-default.jpg');
  $type = $seo['type'] ?? 'website';
  $keywords = $seo['keywords'] ?? 'quick gigs, gig marketplace, hire video editor, reels editing, thumbnail designer, AI video, freelance india, escrow payments';
  $author = $seo['author'] ?? 'Quick GIGS';
@endphp
<title>{{ $title }}</title>
<meta name="description" content="{{ $desc }}">
<meta name="keywords" content="{{ $keywords }}">
<link rel="canonical" href="{{ $canonical }}">
<meta name="author" content="{{ $author }}">
<meta name="theme-color" content="#06060B">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

{{-- Open Graph --}}
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $desc }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:type" content="{{ $type === 'article' ? 'article' : 'website' }}">
<meta property="og:site_name" content="Quick GIGS">
<meta property="og:locale" content="en_IN">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $desc }}">
<meta name="twitter:image" content="{{ $image }}">

@if($type === 'article' && isset($seo['published']))
<meta property="article:published_time" content="{{ $seo['published'] instanceof \Carbon\Carbon ? $seo['published']->toIso8601String() : $seo['published'] }}">
<meta property="article:author" content="{{ $author }}">
@if(!empty($seo['tags']))<meta property="article:tag" content="{{ is_array($seo['tags']) ? implode(', ', $seo['tags']) : $seo['tags'] }}">@endif
@endif

{{-- AEO: speakable for answer engines --}}
<meta name="speakable" content="headline, description">

{{-- JSON-LD Organization (always) --}}
@php
$orgJsonLd = [
  '@context'=>'https://schema.org',
  '@type'=>'Organization',
  'name'=>'Quick GIGS',
  'alternateName'=>'QuickGigs — the fast gig marketplace',
  'url'=>url('/'),
  'logo'=>url('/logo.png'),
  'description'=>$desc,
  'foundingLocation'=>['@type'=>'Place','address'=>['@type'=>'PostalAddress','addressLocality'=>'Ghaziabad','addressRegion'=>'Uttar Pradesh','addressCountry'=>'IN']],
  'sameAs'=>['https://www.linkedin.com/company/quickgigs','https://x.com/quickgigs'],
  'contactPoint'=>['@type'=>'ContactPoint','telephone'=>'+91-98765-43210','contactType'=>'customer support','areaServed'=>'IN','availableLanguage'=>['en','hi']],
];
@endphp
<script type="application/ld+json">{!! json_encode($orgJsonLd, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>

{{-- Breadcrumbs JSON-LD if provided --}}
@if(!empty($breadcrumbs))
@php
$bc = [
  '@context'=>'https://schema.org',
  '@type'=>'BreadcrumbList',
  'itemListElement'=> collect($breadcrumbs)->map(fn($b,$i)=>[
    '@type'=>'ListItem','position'=>$i+1,'name'=>$b['name'],'item'=>$b['url']
  ])->values()->toArray()
];
@endphp
<script type="application/ld+json">{!! json_encode($bc, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endif

{{-- FAQ JSON-LD if $faqs provided --}}
@if(!empty($faqs) && $faqs->count())
@php
  $faqForLd = $faqs->filter(fn($f)=> !empty($f->question ?? $f['question'] ?? null));
  $faqJson = [
    '@context'=>'https://schema.org',
    '@type'=>'FAQPage',
    'mainEntity'=> $faqForLd->map(function($f){
      $q = $f->question ?? $f['question'] ?? '';
      $a = $f->answer ?? $f['answer'] ?? '';
      return ['@type'=>'Question','name'=>$q,'acceptedAnswer'=>['@type'=>'Answer','text'=>strip_tags($a)]];
    })->values()->toArray()
  ];
@endphp
<script type="application/ld+json">{!! json_encode($faqJson, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endif

{{-- Article JSON-LD if $blog provided --}}
@if(!empty($blog))
<script type="application/ld+json">{!! json_encode($blog->jsonLdArticle(), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endif
