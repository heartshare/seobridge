const EventEmitter = require('events')
const Crawler = require("simplecrawler")

module.exports = class CrawlQueueWorker extends EventEmitter
{
    constructor(item)
    {
        super()

        this.item = item
        this.crawler = new Crawler(item.url)

        this.initialize()
        this.start()
    }

    initialize()
    {
        this.crawler.maxDepth = (this.item.mode === 'full') ? 3 : 1

        this.crawler.maxConcurrency = 10
        this.crawler.interval = 1

        this.crawler.addDownloadCondition((queueItem, response, callback) => {
            callback(null, queueItem.stateData.contentType.startsWith('text/html'))
        })

        this.crawler.on("fetchcomplete", (queueItem) => {
            this.item.addUrl(queueItem.url)
        })

        this.crawler.on("complete", () => {
            this.item.status = 'crawled'
            this.emit('complete', this.item)
        })

    }
    
    start()
    {
        this.item.status = 'crawling'
        this.crawler.start()
    }
}