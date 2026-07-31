<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title><![CDATA[{{ $title }}]]></title>
        <link>{{ url('/') }}</link>
        <atom:link href="{{ url()->full() }}" rel="self" type="application/rss+xml" />
        <description><![CDATA[{{ $description }}]]></description>
        <language>fr</language>
        <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>
        @foreach($articles as $article)
            <item>
                <title><![CDATA[{{ $article->title }}]]></title>
                <link>{{ route('articles.show', $article->slug) }}</link>
                <guid isPermaLink="true">{{ route('articles.show', $article->slug) }}</guid>
                <pubDate>{{ ($article->published_at ?? $article->created_at)->toRfc2822String() }}</pubDate>
                @if($article->category)
                    <category><![CDATA[{{ $article->category->name }}]]></category>
                @endif
                @if($article->author)
                    <dc:creator xmlns:dc="http://purl.org/dc/elements/1.1/"><![CDATA[{{ $article->author->name }}]]></dc:creator>
                @endif
                <description><![CDATA[{{ $article->excerpt }}]]></description>
                <content:encoded><![CDATA[{{ $article->content }}]]></content:encoded>
                @if($article->featured_image_url)
                    <enclosure url="{{ $article->featured_image_url }}" type="image/jpeg" />
                @endif
            </item>
        @endforeach
    </channel>
</rss>
