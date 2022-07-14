/**
 * send puppeteer to search on the web spotify track url, youtube url and playlist url to download them after
 */
const argv = require('minimist')(process.argv.slice(2));
const puppeteer = require("puppeteer");
const accurate = argv.accurate;
const playlistUrl = argv.playlistUrl;
if(accurate === "true"){
    var ytSearch = argv.ytUrl.replace("'", '"').slice(0,-1) + '"' + "&tbm=vid";
    var spSearch = argv.spUrl.replace("'", '"').slice(0,-1) + '"' + "+track";
}else{
    var ytSearch = argv.ytUrl;
    var spSearch = argv.spUrl;
}
(async () => {
    const browser = await puppeteer.launch({ headless: true, args: ['--no-sandbox', '--disable-setuid-sandbox']});
    const page = await browser.newPage();
    await page.goto(ytSearch, {waitUntil: 'load', timeout: 0});
    await page.click("#W0wltc");
    await page.waitForSelector('#rcnt')
    const getYtUrls = await page.evaluate(() => {
        const aTags = document.querySelectorAll("a");
        let aSrcs = [];
        for (let i = 0; i < aTags.length; i++) {
            if(aTags[i].getAttribute("href") == null){
                //
            }else{
                if(aTags[i].getAttribute("href").startsWith("https://www.youtube.com/watch?v=")){
                    aSrcs.push(aTags[i].getAttribute("href"))
                }
            }
        }
        return aSrcs[0];
    });
    await page.goto(spSearch, {waitUntil: 'load', timeout: 0});
    await page.waitForSelector('#rcnt')
    const getSpUrls = await page.evaluate(() => {
        const aTags = document.querySelectorAll("a");
        let aSrcs = [];
        for (let i = 0; i < aTags.length; i++) {
            if(aTags[i].getAttribute("href") == null){
                //
            }else{
                if(aTags[i].getAttribute("href").startsWith("https://open.spotify.com/track")){
                    aSrcs.push(aTags[i].getAttribute("href"))
                }
            }
        }
        return aSrcs[0];
    });
    await page.goto(playlistUrl, {waitUntil: 'load', timeout: 0});
    await page.waitForSelector('#rcnt')
    const getplaylistUrls = await page.evaluate(() => {
        const aTags = document.querySelectorAll("a");
        let aSrcs = [];
        for (let i = 0; i < aTags.length; i++) {
            if(aTags[i].getAttribute("href") == null){
                //
            }else{
                if(aTags[i].getAttribute("href").startsWith("https://open.spotify.com/playlist/" || aTags[i].getAttribute("href").startsWith("https://open.spotify.com/album/"))){
                    aSrcs.push(aTags[i].getAttribute("href"))
                }
            }
        }
        return aSrcs[0];
    });
    console.log(getYtUrls);
    console.log(getSpUrls);
    console.log(getplaylistUrls);
    await browser.close();
})();
