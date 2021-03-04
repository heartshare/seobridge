const EventEmitter = require('events')
const FetchQueueWorker = require('./FetchQueueWorker')

module.exports = class FetchQueue extends EventEmitter
{
    constructor (maxConcurrentWorkers = 5)
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

                this.emit('status.fetching.started', {
                    id: item.id
                })
                
                this.workers.push(new FetchQueueWorker(item).on('fetch-complete', (fetchedItem) => {
                    this.emit('status.fetching.completed', {
                        id: fetchedItem.id,
                        item: fetchedItem.item,
                        page: fetchedItem.page,
                    })
                }).on('complete', (fetchedItem) => {
                    this.emit('status.completed', {
                        id: fetchedItem.id,
                        item: fetchedItem.item
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