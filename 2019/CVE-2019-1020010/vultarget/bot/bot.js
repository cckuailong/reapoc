#!/bin/bash
const puppeteer = require("puppeteer");
const fs = require("fs");
const base_url = "http://web:3000/";
const dir = "./screenshots";

if (!fs.existsSync(dir)) {
  fs.mkdirSync(dir);
}

(async () => {
  const browser = await puppeteer.launch({args: ['--no-sandbox', '--disable-setuid-sandbox']});
  const page = await browser.newPage();
  await page.setViewport({
    width: 1920,
    height: 1080,
  });
  try {
    // Login in with admin
    await page.goto(base_url);
    await page.waitFor(3000);
    await page.click(".signin");
    await page.type("input[type=text]", "dxy");
    await page.type("input[type=password]", "dxy0411");
    await page.click("button[type=submit]");
    await page.waitFor(4500);

    // Go to #优惠分享 tag
    await page.goto(base_url + 'tags/%E4%BC%98%E6%83%A0%E5%88%86%E4%BA%AB')
    await page.waitFor(3000);
    await page.screenshot({path: dir + '/posts.png'});

    // Define a promiss to return new tab page
    // ref: https://www.lfhacks.com/tech/puppeteer-new-tab
    const newPagePromise = new Promise((res, reject) =>
    {
      browser.once("targetcreated", (target) => res(target.page()))
      setTimeout(() => { reject('Promise timed out after ' + 500 + ' ms, no link found.')}, 500);
    }
    );

    // Click all links
    await page.evaluate(() => {
      let elements = document.getElementsByClassName("mk-url");
      for (let e of elements) {
        e.click();
      }
    });
    // page.click(".mk-url");


    // Screenshot for one link
    let newPage = await newPagePromise;
    await page.waitFor(500);
    await newPage.screenshot({path: dir + '/link.png'});
    await newPage.close();
    await page.bringToFront();
    await page.waitFor(1000);
 
  } catch (e) {
    console.log(e.toString());
    process.exit(1);
  } finally {
    await browser.close();
  }
})();
