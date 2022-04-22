<?php

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkRenderer;
use League\CommonMark\Normalizer\SlugNormalizer;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;

class markdown {

    static function convertToHtml($content) {
        //$content = str_replace("\n", '<br>', $content);
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new HeadingPermalinkExtension());
        $environment->addExtension(new TableOfContentsExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        $environment->mergeConfig([
            'heading_permalink' => [
                'html_class' => 'markdown-heading-permalink',
                'min_heading_level' => 2,
                'symbol' => ''
            ]
        ]);

        $environment->mergeConfig([
            'table_of_contents' => [
                'html_class' => 'markdown-table-of-contents',
                'position' => 'placeholder',
                'min_heading_level' => 2,
                'max_heading_level' => 2,
                'placeholder' => '<table-of-contents>',
            ]
        ]);

        $converter = new CommonMarkConverter([], $environment);

        return $converter->convertToHtml($content);
    }

}
