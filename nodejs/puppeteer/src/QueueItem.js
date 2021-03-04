module.exports = class CrawlQueueItem
{
    constructor (id, url, mode = 'single')
    {
        this.id = id
        this.url = url
        this.mode = mode
        this.status = null
        this.crawledUrls = []
        this.pages = []
    }

    addUrl(url)
    {
        this.crawledUrls.push(url)
    }

    addPage(page)
    {
        this.pages.push(page)
    }
}