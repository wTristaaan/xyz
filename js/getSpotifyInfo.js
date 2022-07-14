/**
 * send puppeteer to https://spotify-downloader.com/ for download the track song passed in parameter
 */

const argv = require('minimist')(process.argv.slice(2));
const puppeteer = require("puppeteer");
const path = require('path');
const spUrl = argv.spUrl;

function delay(time) {
    return new Promise(function(resolve) { 
        setTimeout(resolve, time)
    });
 }
 
(async () => {
    const browser = await puppeteer.launch({ headless: true, args: ['--no-sandbox', '--disable-setuid-sandbox']});
    const page = await browser.newPage();
    await page.goto("https://spotify-downloader.com/", {waitUntil: 'load', timeout: 0});
    await page.click("#link",{ clickCount: 3 });
    await page.keyboard.press('Backspace');
    await page.type("#link", spUrl);
    await page.click("#submit");
    await page.waitForSelector('#result > article > footer > button')
    await page.click("#result > article > footer > button");
    const client = await page.target().createCDPSession()
        await client.send('Page.setDownloadBehavior', {
            behavior: 'allow',
            downloadPath: path.resolve('./mp3'),
        })
    const musicName = await page.evaluate(() => {
        const aTag = document.querySelector("#result > article > h2");
        return aTag.innerHTML;
    });
    const artistName = await page.evaluate(() => {
        const aTag = document.querySelector("#result > article > p");
        return aTag.innerHTML;
    });
    console.log(musicName);
    console.log(artistName);
    await page.on('response', (response)=>{ 
        console.log(response.url());
    });
    await page.waitForResponse((response) => {
        return response.url().startsWith(`https://i.scdn.co/image/`) && response.status() === 200;
    });
    await delay(3000);
    browser.close();
})();