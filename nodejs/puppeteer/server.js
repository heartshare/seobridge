const puppeteer = require('puppeteer')
const express = require('express')
const {v4: uuid} = require('uuid')
const cors = require('cors')
const fetch = require('node-fetch')

const app = express()



const _r = "\x1b[31m"
const _g = "\x1b[32m"
const _y = "\x1b[33m"
const _b = "\x1b[34m"
const _m = "\x1b[35m"
const _c = "\x1b[36m"
const _w = "\x1b[37m"
const bright = "\x1b[1m"
const reset = "\x1b[0m"



app.use(cors())

app.all('/analyse', async (req, res) => {
    const requestId = uuid()
    var ip = req.headers['x-forwarded-for'];

    if (ip !== '127.0.0.1')
    {
        console.log(`${_y}${requestId}${reset} Unauthorized request from: ${_r}${ip}${reset}`)

        res.status(403)
        return res.send('UNAUTHORIZED')
    }
    
    if (!req.query.url)
    {
        console.log(`${_y}${requestId}${reset} Invalid request with no url parameter.`)
        
        res.status(422)
        return res.send('URL_MISSING')
    }

    const url = new URL(req.query.url)

    console.log(`${_y}${requestId}${reset} Starting scan for${reset}: ${_b}${url}${reset}`)
    
    let page = await scanUrl(url)
    
    console.log(`${_y}${requestId}${reset} ${_g}Finished scan for${reset}: ${_b}${url}${reset}`)
    
    res.setHeader('Content-Type', 'application/json')
    return res.end(JSON.stringify(page))
})

const scanUrl = async (url) => {
    const browser = await puppeteer.launch()
    const page = await browser.newPage()
    const sourceChecker = await browser.newPage()

    await page.setViewport({
        width: 1920,
        height: 1080,
    })
    
    await page.goto(url)

    let metrics = await page.metrics()
    
    let links = []
    links = await page.$$eval('a', e => e.map(node => {
        return {
            'href': node.getAttribute('href'),
            'text': node.innerText,
        }
    }))

    let internalLinks = []
    internalLinks = links.filter(e => {
        return new URL(e.href, url.origin).origin === url.origin
    }).map(e => e.href)
    internalLinks = [...new Set(internalLinks)]

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

    images = images.map(e => ({...e, href: new URL(e.src, url.origin).href}))

    let title
    try {
        title = await page.$eval('title', node => node.textContent)
    }
    catch (error) {
        title = null
    }

    let metaTags = []
    metaTags = await page.$$eval('meta', e => e.map(node => ({
        'name': node.getAttribute('name'),
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
    let keywords = []

    for (const metaTag of metaTags)
    {
        if (metaTag.name === 'viewport')
        {
            viewport = metaTag.content
        }
        else if (metaTag.name === 'theme-color')
        {
            themeColor = metaTag.content
        }
        else if (metaTag.name === 'generator')
        {
            generator = metaTag.content
        }
        else if (metaTag.name === 'description')
        {
            description = metaTag.content
        }
        else if (metaTag.name === 'keywords')
        {
            keywords = metaTag.content.split(',')
        }
        else if (['twitter:card', 'twitter:site', 'twitter:title', 'twitter:description', 'twitter:image'].includes(metaTag.name))
        {
            twitterCard[metaTag.name] = metaTag.content
        }
        else if (['og:title', 'og:type', 'og:url', 'og:image', 'og:description'].includes(metaTag.property))
        {
            openGraph[metaTag.property] = metaTag.content
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

    let score = {
        totalPageScore: 0,

        hasTitle: title ? true : false,
        hasDescription: description ? true : false,
        hasFavicon: favicon ? true : false,
        hasViewport: viewport ? true : false,
    }

    if (score.hasTitle) score.totalPageScore += 25
    if (score.hasDescription) score.totalPageScore += 25
    if (score.hasFavicon) score.totalPageScore += 25
    if (score.hasViewport) score.totalPageScore += 25



    let metaData = {
        openGraph,
        twitterCard,
        viewport,
        description,
        generator,
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
        metrics,
        images,
        meta: metaTags,
        srcLinks,
        headings,
        score,
        metaData,
    }
}



app.listen(999)

console.log(`Express running on: ${_g}127.0.0.1:999`+reset)