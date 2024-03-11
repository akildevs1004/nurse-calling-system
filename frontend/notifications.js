const WebSocket = require("ws");
const fs = require("fs");
require("dotenv").config();
const { Agent } = require('https');

const axios = require("axios");

const agent = new Agent({
    rejectUnauthorized: false
});

let { Pool } = require("pg");
const format = require('pg-format');
const { log } = require("console");


const pool = new Pool({
    host: process.env.DB_HOST,
    user: process.env.DB_USERNAME,
    port: process.env.DB_PORT,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_DATABASE,
    max: 100,
    idleTimeoutMillis: 30000,
});

pool.on('error', async (err, client) => {
    console.error('Unexpected error on idle client', err);
    await sendWhatsappMessage(err);
    process.exit(-1);
});
let { WHATSAPP_ENDPOINT, NUMBER, INSTANCE_ID, TOKEN, SOCKET_ENDPOINT,ADMIN_LEAVE_NOTIFICATION_SOCKET_ENDPOINT } = process.env;

let socket = new WebSocket(ADMIN_LEAVE_NOTIFICATION_SOCKET_ENDPOINT);

const sendWhatsappMessage = async (msg) => {
    axios.get(`${WHATSAPP_ENDPOINT}?number=${NUMBER}&type=text&message=${msg}message&instance_id=${INSTANCE_ID}&access_token=${TOKEN}`, { httpsAgent: agent })
        .then(({ data }) => console.log(msg))
        .catch(error => console.error(error));
};
socket.onopen = async () => {
    const msg = "connected to notification\n";
    // await sendWhatsappMessage(msg);
    console.log(msg);

};
socket.onerror = ({ message: msg }) => {
    // sendWhatsappMessage(msg);
    console.log(msg);
};

socket.onmessage = async ({ data }) => {
    try {
        let json = JSON.parse(data);

        console.log(json);
    } catch ({ message }) {
        // sendWhatsappMessage(message);
        console.log(message);
    }
};
