const express = require("express");
const app = express();

const { Resolver } = require("dns");
const resolver = new Resolver();

resolver.setServers(["127.0.0.1"]);

app.get("/", (req, res) =>
  resolver.resolve("www.pudim.com", (err, addresses) => {
    res.send(JSON.stringify(addresses));
    console.log("New request responded successfully!");
  })
);

app.listen(3000, () => {
  console.log("Running server 🚀");
});
