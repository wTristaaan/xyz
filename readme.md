# xyz is a website makes for found any music on a web to listen it for free !

This website is made for fun, not for illegal use, use it to download only free license track or one you have the rights to.

## How does it work ?

Thanks to [exec()](https://www.php.net/manual/en/function.exec.php) function. PHP execute node js scripts. These uses puppeteer to scrap many websites like Google to find links, Youtube to find information about the tracks.

```PHP
/**
* launch js script who returns Urls
*/
exec("node ../js/getAllUrls.js --ytUrl=" . $searchUrls[0] . "  --spUrl=". $searchUrls[1] . " --accurate=false --playlistUrl=" . $searchUrls[2] , $urls, $error);
```

## Installation

- Command : `git@github.com:wTristaaan/xyz.git`
- Follow commands instruction in [command.txt](command.txt)

## Contributor / languages used
@wTristaan : HTML :shipit:, CSS :compass:, JS :electron:, PHP :atom:.
