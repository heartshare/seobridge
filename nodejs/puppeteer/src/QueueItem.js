module.exports = class CrawlQueueItem
{
    constructor (id, ownerId, url, mode = 'single')
    {
        this.id = id
        this.ownerId = ownerId
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