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

    let url = req.query.url



    console.log(`${_y}${requestId}${reset} Starting scan for${reset}: ${_b}${url}${reset}`)
    
    const browser = await puppeteer.launch()
    const page = await browser.newPage()
    await page.goto(url)

    let metrics = await page.metrics()
    
    let links = []
    links = await page.$$eval('a', e => e.map(node => ({
        'href': node.getAttribute('href'),
        'text': node.innerText,
    })))

    let images = []
    images = await page.$$eval('img', e => e.map(node => ({
        'src': node.getAttribute('src'),
        'alt': node.getAttribute('alt') || null,
        'width': node.naturalWidth,
        'height': node.naturalHeight,
        'visibleWidth': node.offsetWidth,
        'visibleHight': node.offsetHeight,
    })))

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
    srcLink = await page.$$eval('link', e => e.map(node => ({
        'rel': node.getAttribute('rel'),
        'href': node.getAttribute('href'),
        'title': node.getAttribute('title'),
    })))
    
    await browser.close()


    
    console.log(`${_y}${requestId}${reset} ${_g}Finished scan for${reset}: ${_b}${url}${reset}`)
    
    res.setHeader('Content-Type', 'application/json')
    return res.end(JSON.stringify({links, metrics, images, title, metaDescription, meta, srcLinks}))
})

app.listen(999)

console.log(`Express running on: ${_g}127.0.0.1:999`+reset)