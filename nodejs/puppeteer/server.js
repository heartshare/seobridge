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
const fetchQueue = new FetchQueue(5)

crawlQueue.on('status.crawling.started', (event) => {
    console.log(`${blue}Crawling:${reset} started  for ${blue}${event.id}${reset}`)

    axios.post('http://localhost/seobridge/public/api/webhooks/reports/status-crawling', {
        jobId: event.id,
        type: 'crawling',
    })
    .then(res => {})
    .catch(error => {})
})

crawlQueue.on('status.crawling.completed', (event) => {
    console.log(`${blue}Crawling:${reset} complete for ${blue}${event.id}${reset}`)

    fetchQueue.add(event.item)

    axios.post('http://localhost/seobridge/public/api/webhooks/reports/status-crawling-completed', {
        jobId: event.id,
        type: 'crawling_completed',
        progress: 0,
        progressMax: event.item.crawledUrls.length,
    })
    .then(res => {})
    .catch(error => {})
})



fetchQueue.on('status.fetching.started', (event) => {
    console.log(`${yellow}Fetching:${reset} started for ${blue}${event.id}${reset}`)

    axios.post('http://localhost/seobridge/public/api/webhooks/reports/status-crawling', {
        jobId: event.id,
        type: 'crawling',
    })
    .then(res => {})
    .catch(error => {})
})

fetchQueue.on('status.fetching.completed', (event) => {
    console.log(`${yellow}Fetching:${reset} complete for ${blue}${event.id}${reset}`)

    axios.post('http://localhost/seobridge/public/api/webhooks/reports/status-fetching-completed', {
        jobId: event.id,
        type: 'fetching_completed',
        progress: event.item.pages.length,
    })
    .then(res => {})
    .catch(error => {})

    axios.post('http://localhost/seobridge/public/api/webhooks/reports/add-page', {
        id: event.id,
        data: event.page,
    })
    .then(res => {})
    .catch(error => {})
})

fetchQueue.on('status.completed', (event) => {
    console.log(`${green}Request:${reset} complete for ${blue}${event.id}${reset}`)

    setTimeout(() => {
        axios.post('http://localhost/seobridge/public/api/webhooks/reports/status-completed', {
            jobId: event.id,
            type: 'completed',
            progress: event.item.pages.length,
        })
        .then(res => {})
        .catch(error => {})
    }, 100)
})

crawlQueue.start()
fetchQueue.start()



app.post('/request-site-analysis', async (req, res) => {
    if (!req.body.job_id)
    {
        res.status(422)
        return res.send('JOB_ID_MISSING')
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

    crawlQueue.add(new QueueItem(req.body.job_id, req.body.url, req.body.mode))
    
    res.status(200)
    return res.send('OK')
})



app.listen(999, '127.0.0.1')

console.log(`Express running on: ${green}127.0.0.1:999`+reset)