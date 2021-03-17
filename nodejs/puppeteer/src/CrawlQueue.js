const EventEmitter = require('events')
const CrawlQueueWorker = require('./CrawlQueueWorker')

module.exports = class CrawlQueue extends EventEmitter
{
    constructor (maxConcurrentWorkers = 10)
    {
        super()

        this.maxConcurrentWorkers = maxConcurrentWorkers
        this.queue = []
        this.workers = []
        this.interval = null
    }

    start()
    {
        this.interval = setInterval(() => {
            // Put queue item in worker if current worker count is below max concurrent workers
            if (this.workers.length < this.maxConcurrentWorkers && this.queue.length > 0)
            {
                let item = this.queue.shift()

                this.emit('status.crawling.started', {
                    item
                })
                
                this.workers.push(new CrawlQueueWorker(item).on('complete', (crawledItem) => {
                    this.emit('status.crawling.completed', {
                        item: crawledItem
                    })

                    this.freeWorker(item.id)
                }))
            }
        }, 1)
    }

    freeWorker(id)
    {
        this.workers.splice(this.workers.findIndex(e => e.item.id === id), 1)
    }

    stop()
    {
        clearInterval(this.interval)
    }

    add(queueItem)
    {
        this.queue.push(queueItem)
    }
}