const puppeteer = require('puppeteer')
const EventEmitter = require('events')
const {v4: uuid} = require('uuid')

module.exports = class FetchWorker extends EventEmitter
{
    constructor(item)
    {
        super()

        this.item = item
        this.browser = null
        this.interval = null

        this.initialize()
    }

    async initialize()
    {
        this.item.status = 'fetching'
        
        this.browser = await puppeteer.launch()

        for (const url of this.item.crawledUrls)
        {
            this.scan(url)
        }

        this.interval = setInterval(() => {
            if (this.item.pages.length === this.item.crawledUrls.length)
            {
                this.emit('complete', {
                    id: this.item.id,
                    item: this.item,
                })

                this.browser.close()

                clearInterval(this.interval)
            }
        }, 10)
    }

    async scan(url_)
    {
        const url = new URL(url_)

        const page = await this.browser.newPage()
        await page.setDefaultNavigationTimeout(0)

        await page.setViewport({
            width: 1920,
            height: 1080,
        })
        
        try {
            await page.goto(url)
        }
        catch (error)
        {
            await page.close()
            this.item.addPage(null)
        }

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

        let h1Count = headings.filter(e => e.tag === 'H1').length

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


        
        // await browser.close()
        await page.close()

        let pageData = {
            url: {
                origin: url.origin,
                host: url.host,
                href: url.href,
            },
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

        this.item.addPage(pageData)

        this.emit('fetch-complete', {
            id: this.item.id,
            item: this.item,
            page: pageData,
        })
    }
}