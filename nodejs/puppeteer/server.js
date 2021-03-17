const express = require('express')
const axios = require('axios')

const { reset, green, blue, yellow } = require('./src/Colors')
const CrawlQueue = require('./src/CrawlQueue')
const QueueItem = require('./src/QueueItem')
const FetchQueue = require('./src/FetchQueue')

const app = express()

app.use(express.urlencoded({extended: true}))
app.use(express.json())



const crawlQueue = new CrawlQueue(10)
const fetchQueue = new FetchQueue(3)

crawlQueue.on('status.crawling.started', (event) => {
    console.log(`${blue}Crawling:${reset} started  for ${blue}${event.item.id}${reset}`)

    sendStatusUpdate('crawling', event)
})

crawlQueue.on('status.crawling.completed', (event) => {
    console.log(`${blue}Crawling:${reset} complete for ${blue}${event.item.id}${reset}`)

    sendStatusUpdate('crawling_completed', event)

    fetchQueue.add(event.item)
})



fetchQueue.on('status.fetching.started', (event) => {
    console.log(`${yellow}Fetching:${reset} started for ${blue}${event.item.id}${reset}`)

    sendStatusUpdate('fetching', event)
})

fetchQueue.on('status.fetching.completed', (event) => {
    console.log(`${yellow}Fetching:${reset} complete for ${blue}${event.item.id}${reset}`)
    
    // sendStatusUpdate('fetching_completed', event)

    axios.post('http://localhost/seobridge/public/api/webhooks/reports/add-page', {
        jobId: event.item.id,
        ownerId: event.item.ownerId,
        data: event.page,
    })
    .then(res => {})
    .catch(error => {
        console.log(error.response.data)
    })
})

fetchQueue.on('status.completed', (event) => {
    console.log(`${green}Request:${reset} complete for ${blue}${event.item.id}${reset}`)

    // TODO: make sure this event is recognized
    setTimeout(() => {
        sendStatusUpdate('completed', event)
    }, 100)
})

function sendStatusUpdate(status, event)
{
    let urlDict = {
        'crawling':             'http://localhost/seobridge/public/api/webhooks/reports/status-crawling',
        'crawling_completed':   'http://localhost/seobridge/public/api/webhooks/reports/status-crawling-completed',
        'fetching':             'http://localhost/seobridge/public/api/webhooks/reports/status-fetching',
        'fetching_completed':   'http://localhost/seobridge/public/api/webhooks/reports/status-fetching-completed', // Will never be used
        'completed':            'http://localhost/seobridge/public/api/webhooks/reports/status-completed',
    }

    axios.post(urlDict[status], {
        jobId: event.item.id,
        ownerId: event.item.ownerId,
        type: status,
        progress: event.item.pages.length || 0,
        progressMax: event.item.crawledUrls.length || 0,
    })
    .then(res => {})
    .catch(error => {
        console.log(error.response.data)
    })
}



crawlQueue.start()
fetchQueue.start()



app.post('/request-site-analysis', async (req, res) => {
    if (!req.body.job_id)
    {
        res.status(422)
        return res.send('JOB_ID_MISSING')
    }

    if (!req.body.owner_id)
    {
        res.status(422)
        return res.send('OWNER_ID_MISSING')
    }

    if (!req.body.url)
    {
        res.status(422)
        return res.send('URL_MISSING')
    }

    if (!['full', 'single'].includes(req.body.mode))
    {
        res.status(422)
        return res.send('INVALID_MODE')
    }

    crawlQueue.add(new QueueItem(req.body.job_id, req.body.owner_id, req.body.url, req.body.mode))
    
    res.status(200)
    return res.send('OK')
})



app.listen(999, '127.0.0.1')

console.log(`Express running on: ${green}127.0.0.1:999`+reset)