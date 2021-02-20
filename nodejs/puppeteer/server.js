const puppeteer = require('puppeteer')
const express = require('express')
const {v4: uuid} = require('uuid')
const cors = require('cors')

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
    links = await page.$$eval('a', e => e.map(node => ({
        'href': node.getAttribute('href'),
        'text': node.innerText,
    })))

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
    } catch (error) {
        title = null
    }

    let metaDescription
    try {
        metaDescription = await page.$eval('meta[name="description"]', node => node.getAttribute('content'))
    } catch (error) {
        metaDescription = null
    }

    let meta = []
    meta = await page.$$eval('meta', e => e.map(node => ({
        'name': node.getAttribute('name'),
        'property': node.getAttribute('property'),
        'content': node.getAttribute('content'),
        'charset': node.getAttribute('charset'),
        'httpEquiv': node.getAttribute('http-equiv'),
    })))

    let srcLinks = []
    srcLinks = await page.$$eval('link', e => e.map(node => ({
        'rel': node.getAttribute('rel'),
        'href': node.getAttribute('href'),
        'title': node.getAttribute('title'),
    })))



    let favicon = srcLinks.find(e => e.rel === 'shortcut icon')

    faviconUrl = favicon ? new URL(favicon.href, url.href) : null

    if (!faviconUrl)
    {
        try
        {
            probeUrl = new URL('/favicon.ico', url.href)
            await sourceChecker.goto(probeUrl.href, {waitUntil: 'load', timeout: 0});
            faviconUrl = probeUrl.href
        }
        catch(e)
        {}
    }

    if (!faviconUrl)
    {
        try
        {
            probeUrl = new URL('/favicon.png', url.href)
            await sourceChecker.goto(probeUrl.href, {waitUntil: 'load', timeout: 0});
            faviconUrl = probeUrl.href
        }
        catch(e)
        {}
    }


    
    let appleTouchIcon = srcLinks.find(e => e.rel === 'apple-touch-icon')
    appleTouchIcon = appleTouchIcon ? appleTouchIcon.href : null

    let preview = 'data:image/png;base64,'+await page.screenshot({ encoding: "base64" })
    
    await browser.close()

    return {
        url: {
            origin: url.origin,
            href: url.href,
        },
        preview,
        favicon: faviconUrl,
        appleTouchIcon,
        links,
        internalLinks,
        metrics,
        images,
        title,
        metaDescription,
        meta,
        srcLinks,
    }
}



app.listen(999)

console.log(`Express running on: ${_g}127.0.0.1:999`+reset)