/**
 * send puppeteer to https://spotify-downloader.com/ for download a Spotify playlist passed in parameter
 */

const argv = require('minimist')(process.argv.slice(2));
const puppeteer = require("puppeteer");
const path = require('path');
const { url } = require('inspector');
const spUrl = argv.spPlaylist;

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
    await page.waitForSelector('#result > article > footer > button');
    const client = await page.target().createCDPSession()
    await client.send('Page.setDownloadBehavior', {
        behavior: 'allow',
        downloadPath: path.resolve('./mp3'),
    });
    const number = await page.evaluate(() => {
        pTag = document.querySelectorAll("#result > article");
        return pTag.length;
    });
    var test = 0;
    for(var i = 2; i < number; i++){
        if((await page.$("#result > article:nth-child("+ i +") > footer > button")) !== null) {
            const nameMusic = await page.evaluate((i) => {
                h2Tag = document.querySelector("#result > article:nth-child("+ i +") > h2");
                return h2Tag.innerHTML;
            }, i);
            const nameArtist = await page.evaluate((i) => {
                pTag = document.querySelector("#result > article:nth-child("+ i +") > p");
                return pTag.innerHTML;
            }, i);
            await page.click("#result > article:nth-child("+ i +") > footer > button");

            const one = await page.waitForResponse((response) => {
                return response.url().startsWith(`https://api.spotify-downloader.com/download/`) && response.status() === 302;
            }, {timeout: 90000});
            const oneImg = await page.waitForResponse((response) => {
                return response.url().startsWith(`https://i.scdn.co/image/`) && response.status() === 200;
            }, {timeout: 90000});
            console.log(nameMusic);
            console.log(nameArtist);
            console.log(one.url());
            console.log(oneImg.url());
            test++
            if(test == 3){
                break;
            }
        }else{
            
        }
    }
    await delay(1500)
    await browser.close();
})();