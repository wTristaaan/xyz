/**
 * send puppeteer to youtube mp3 downloader for download the audio in video passed in parameter
 */
const argv = require('minimist')(process.argv.slice(2));
const puppeteer = require("puppeteer");
const path = require('path');
const ytUrl = argv.ytUrl;

function delay(time) {
    return new Promise(function(resolve) { 
        setTimeout(resolve, time)
    });
 }

 (async () => {
    const browser = await puppeteer.launch({ headless: true, args: ['--no-sandbox', '--disable-setuid-sandbox']});
    const page = await browser.newPage();
    await page.goto("https://notube.io/fr/youtube-app-v19", {waitUntil: 'load', timeout: 0});
    await page.type('#keyword', ytUrl);
    await page.waitForSelector('#submit-button')
    if ((await page.$('#submit-button')) !== null) {
        await delay(1000);
        await page.click("#submit-button");
    }
    await delay(2500);
    let pages = await browser.pages();
    if(pages[2] != null){
        await pages[2].close();
    }else{
        //
    } 
    if(await page.$("#main > section > div > p > div > div > div > div > iframe") == null){
        await page.waitForSelector('#downloadButton');
        if ((await page.$('#downloadButton')) !== null) {
            const url = await page.evaluate(() => {
                const aTag = document.querySelector("#downloadButton").getAttribute("href");
                return aTag;
            });
            console.log(url);
        }
    }else{
        await page.goto("https://fr.onlinevideoconverter.pro/30/youtube-converter-mp3", {waitUntil: 'load', timeout: 0});
        await page.type("#texturl", ytUrl);
        await page.click("#convert1");
        await page.waitForSelector("#download-720-MP4");
        const client = await page.target().createCDPSession()
        await client.send('Page.setDownloadBehavior', {
            behavior: 'allow',
            downloadPath: path.resolve('./mp3'),
        });
        await page.click("#download-720-MP4");
        await delay(2500);
        let newPages = await browser.pages();
        if(newPages[2] != null){
            await newPages[2].close();
        }else{
            //
        }
        if(newPages[1].on('dialog', async dialog => {dialog.message() })){
            await page.goto("https://video-to-mp3-converter.com/fr8", {waitUntil: 'load', timeout: 0});
            await page.type("#convertForm > input", ytUrl);
            await page.click("#convertForm > div.form-footer > div > button");
            await delay(2500);
            let newNewPages = await browser.pages();
            if(newNewPages[2] != null){
                await newNewPages[2].close();
            }else{
                //
            }
            await page.waitForSelector("#__layout > div > main > section.preview > div > div:nth-child(1) > ul > li:nth-child(1) > button")
            await page.click("#__layout > div > main > section.preview > div > div:nth-child(1) > ul > li:nth-child(1) > button");
            await delay(2000);
            await page.waitForSelector("#__layout > div > main > section.top-content > div.orange-wave-back > div > div > div > div > a")
            await delay(2000);
            const downloadUrl = await page.evaluate(() => {
                aTag = document.querySelector("#__layout > div > main > section.top-content > div.orange-wave-back > div > div > div > div > a");
                return aTag.getAttribute("href");
            })
            await page.goto(downloadUrl);
        }else{
            const downloadUrl = await page.waitForResponse((response) => {
                return response.url().startsWith(`https://fr.onlinevideoconverter.pro/api/storage/`) && response.status() === 200;
            });
            console.log(downloadUrl.url());
        }
        
    }
    await browser.close();
})();
