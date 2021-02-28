const puppeteer = require('puppeteer')
const express = require('express')
const {v4: uuid} = require('uuid')
const fetch = require('node-fetch')

const app = express()

app.use(express.urlencoded({extended: true}))
app.use(express.json())



const _r = "\x1b[31m"
const _g = "\x1b[32m"
const _y = "\x1b[33m"
const _b = "\x1b[34m"
const _m = "\x1b[35m"
const _c = "\x1b[36m"
const _w = "\x1b[37m"
const bright = "\x1b[1m"
const reset = "\x1b[0m"



app.post('/analyse', async (req, res) => {
    const requestId = uuid()
    
    if (!req.body.url)
    {
        console.log(`${_y}${requestId}${reset} Invalid request with no url parameter.`)
        
        res.status(422)
        return res.send('URL_MISSING')
    }

    const url = new URL(req.body.url)

    console.log(`${_y}${requestId}${reset} Starting scan for${reset}: ${_b}${url}${reset}`)
    
    let page = await scanUrl(url)
    
    console.log(`${_y}${requestId}${reset} ${_g}Finished scan for${reset}: ${_b}${url}${reset}`)
    
    res.setHeader('Content-Type', 'application/json')
    return res.end(JSON.stringify(page))
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

console.log(`Express running on: ${_g}127.0.0.1:999`+reset)