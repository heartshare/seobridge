<?php

namespace App\Console\Commands;

use App\Models\CrawledUrl;
use Illuminate\Console\Command;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\Crawler;

class Test extends \Spatie\Crawler\CrawlObservers\CrawlObserver
{
    /**
     * Called when the crawler will crawl the url.
     *
     * @param \Psr\Http\Message\UriInterface $url
     */
    public function willCrawl(UriInterface $url)
    {
        // echo 'Will crawl:'.$url;
    }

    /**
     * Called when the crawler has crawled the given url successfully.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
     */
    public function crawled( UriInterface $url, ResponseInterface $response, UriInterface $foundOnUrl = null)
    {
        CrawledUrl::firstOrCreate([
            'domain' => parse_url($url, PHP_URL_HOST),
            'url' => $url,
            'found_on_domain' => parse_url($foundOnUrl, PHP_URL_HOST),
            'found_on_url' => $foundOnUrl,
        ]);
    }

    /**
     * Called when the crawler had a problem crawling the given url.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param \GuzzleHttp\Exception\RequestException $requestException
     * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
     */
    public function crawlFailed( UriInterface $url, RequestException $requestException, UriInterface $foundOnUrl = null)
    {
        // echo '<p style="font-family: roboto">'.$url.'<br>'.$requestException.'</p>';
    }

    /**
     * Called when the crawl has ended.
     */
    public function finishedCrawling()
    {

    }
}

class Crawl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawls URL';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
        Crawler::create()
        ->setCrawlObserver(new Test)
        ->ignoreRobots()
        ->acceptNofollowLinks()
        ->setUserAgent('seo-bridge-crawler')
        ->setConcurrency(100)
        ->startCrawling($this->argument('url'));
        
        $this->line($this->argument('url'));
    }
}
