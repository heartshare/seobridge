const puppeteer = require('puppeteer')
const express = require('express')
const {v4: uuid} = require('uuid')
const fetch = require('node-fetch')
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

const scanUrl = async (url) => {
    const browser = await puppeteer.launch()
    const page = await browser.newPage()

    await page.setViewport({
        width: 1920,
        height: 1080,
    })
    
    await page.goto(url)

    let metrics = await page.metrics()
    let errorData = {
        errors: [],
        count: {
            total: 0,
            imgMissingAlt: 0,
        }
    }
    let warningData = {
        warnings: [],
        count: {
            total: 0,
        }
    }
    
    let links = []
    links = await page.$$eval('a', e => e.map(node => {
        return {
            'href': node.getAttribute('href'),
            'text': node.innerText,
        }
    }))

    let internalLinks = []
    let outboundLinks = []

    for (const link of links)
    {
        if (new URL(link.href, url.origin).origin === url.origin)
        {
            internalLinks.push(link.href)
        }
        else
        {
            outboundLinks.push(link.href)
        }
    }
    internalLinks = [...new Set(internalLinks)]
    outboundLinks = [...new Set(outboundLinks)]

    let images = []
    images = await page.$$eval('img', e => e.map(node => ({
        'src': node.getAttribute('src'),
        'href': null,
        'alt': node.getAttribute('alt') || null,
        'width': node.naturalWidth,
        'height': node.naturalHeight,
        'visibleWidth': node.offsetWidth,
        'visibleHeight': node.offsetHeight,
    })))

    for (let img of images)
    {
        img.href = new URL(img.src, url.origin).href

        if (!img.alt)
        {
            errorData.errors.push({type: 'IMG_MISSING_ALT', desc: 'An image is missing an "alt"-attribute.', weight: 1,})
            errorData.count.imgMissingAlt += 1
        }
    }

    let title
    try {
        title = await page.$eval('title', node => node.textContent)
    }
    catch (error) {
        title = null
    }

    let metaTags = []
    metaTags = await page.$$eval('meta', e => e.map(node => ({
        'name': node.getAttribute('name') ,
        'property': node.getAttribute('property'),
        'content': node.getAttribute('content'),
        'charset': node.getAttribute('charset'),
        'httpEquiv': node.getAttribute('http-equiv'),
    })))



    let openGraph = {}
    let twitterCard = {}
    let themeColor = null
    let viewport = null
    let description = null
    let generator = null
    let cms = null
    let keywords = []

    for (const metaTag of metaTags)
    {

        let prop = metaTag.name || metaTag.property

        // console.log(metaTag.name, metaTag.property, prop)

        if (!prop) continue

        if (!prop.startsWith('twitter:') && !prop.startsWith('og:'))
        {
            switch (prop.toLowerCase()) {
                case 'viewport':
                    viewport = metaTag.content
                    break;
                case 'theme-color':
                    themeColor = metaTag.content
                    break;
                case 'generator':
                    generator = metaTag.content

                    let gen = generator.toUpperCase()
                    if      (gen.startsWith('JOOMLA!')) cms = 'JOOMLA'
                    else if (gen.startsWith('TYPO3'))   cms = 'TYPO3'
                    else if (gen.startsWith('DRUPAL'))  cms = 'DRUPAL'
                    else if (gen.startsWith('HUGO'))    cms = 'HUGO'
                    break;
                case 'description':
                    description = metaTag.content
                    break;
                case 'keywords':
                    keywords = metaTag.content.split(',')
                    break;
            }
        }
        else if (prop.startsWith('twitter:'))
        {
            twitterCard[prop] = metaTag.content
        }
        else if (prop.startsWith('og:'))
        {
            openGraph[prop] = metaTag.content

            if (prop === 'og:url')
            {
                let ogUrl = new URL(metaTag.content)
                openGraph[prop] = {
                    url: metaTag.content,
                    host: ogUrl.host,
                    origin: ogUrl.origin,
                    path: ogUrl.path,
                }
            }
        }
    }

    openGraph.hasOpenGraph = Object.keys(openGraph).length > 0 ? true : false
    twitterCard.hasTwitterCard = Object.keys(twitterCard).length > 0 ? true : false



    let srcLinks = []
    srcLinks = await page.$$eval('link', e => e.map(node => ({
        'rel': node.getAttribute('rel'),
        'href': node.getAttribute('href'),
        'title': node.getAttribute('title'),
    })))

    let headings = []
    headings = await page.$$eval('h1, h2, h3, h4, h5, h6', e => e.map(node => ({
        'tag': node.tagName,
        'text': node.innerText,
    })))

    h1Count = headings.filter(e => e.tag === 'H1').length

    if (h1Count > 1)
    {
        warningData.warnings.push({type: 'TOO_MANY_H1', desc: `Semantic HTML should only contain one H1 tag. This page contains ${h1Count}.`, weight: 1,})
    }
    else if (h1Count == 0)
    {
        warningData.warnings.push({type: 'NO_H1', desc: `Semantic HTML should contain one H1 tag. This page contains ${h1Count}.`, weight: 1,})
    }



    let favicon = srcLinks.find(e => e.rel === 'shortcut icon')

    favicon = favicon ? new URL(favicon.href, url.href).href : null

    
    if (!favicon)
    {
        let ProbeUrls = [
            new URL('/favicon.ico', url.origin).href,
            new URL('/favicon.png', url.origin).href,
        ]

        for (const probeUrl of ProbeUrls)
        {
            let probe = await fetch(probeUrl)
            if (probe.status >= 200 && probe.status <= 299) favicon = probeUrl
        }
    }


    
    let appleTouchIcon = srcLinks.find(e => e.rel === 'apple-touch-icon')
    appleTouchIcon = appleTouchIcon ? appleTouchIcon.href : null

    let preview = 'data:image/png;base64,'+await page.screenshot({ encoding: "base64" })



    let errorPage = {
        title: null,
        probeUrl: null,
        hasCustom404Page: false,
    }

    let probeErrorPageUrls = [
        url.origin + '/' + uuid() + Math.floor(Math.random() * 100000),
        url.origin + '/index.php/' + uuid() + Math.floor(Math.random() * 100000),
    ]

    for (const probeErrorPageUrl of probeErrorPageUrls)
    {
        let response = await page.goto(probeErrorPageUrl)

        if (response.status() == 404)
        {
            try {
                errorPage.title = await page.$eval('title', node => node.textContent)
            }
            catch (error) {}
        
            // TODO: create better detection
            if (errorPage.title !== '404 Not Found')
            {
                errorPage.hasCustom404Page = true
            }
        }
    }



    let score = {
        totalPageScore: 0,

        hasTitle: title ? true : false,
        hasDescription: description ? true : false,
        hasFavicon: favicon ? true : false,
        hasViewport: viewport ? true : false,

        errorData,
        errorPage,
        warningData,
    }

    if (score.hasTitle) score.totalPageScore += 20
    if (score.hasDescription) score.totalPageScore += 20
    if (score.hasFavicon) score.totalPageScore += 20
    if (score.hasViewport) score.totalPageScore += 20
    if (score.errorPage.hasCustom404Page) score.totalPageScore += 20



    let metaData = {
        openGraph,
        twitterCard,
        viewport,
        description,
        generator,
        cms,
        themeColor,
        keywords,
        title,
        favicon,
    }


    
    await browser.close()

    return {
        url: {
            origin: url.origin,
            host: url.host,
            href: url.href,
        },
        preview,
        appleTouchIcon,
        links,
        internalLinks,
        outboundLinks,
        metrics,
        images,
        meta: metaTags,
        srcLinks,
        headings,
        score,
        metaData,
    }
}


app.listen(999, '127.0.0.1')

console.log(`Express running on: ${green}127.0.0.1:999`+reset)