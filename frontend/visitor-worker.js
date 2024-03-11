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
    // await sendWhatsappMessage(err);
    process.exit(-1);
});
let { WHATSAPP_ENDPOINT, NUMBER, INSTANCE_ID, TOKEN, SOCKET_ENDPOINT } = process.env;

let socket = new WebSocket(SOCKET_ENDPOINT);

const sendWhatsappMessage = async (msg) => {
    axios.get(`${WHATSAPP_ENDPOINT}?number=${NUMBER}&type=text&message=${msg}message&instance_id=${INSTANCE_ID}&access_token=${TOKEN}`, { httpsAgent: agent })
        .then(({ data }) => console.log(msg))
        .catch(error => console.error(error));
};
socket.onopen = async () => {
    const msg = "connected to live\n";
    // await sendWhatsappMessage(msg);
    console.log(msg);

};
socket.onerror = ({ message: msg }) => {
    // sendWhatsappMessage(msg);
    console.log(msg);
};

let last_record_number = 0;

socket.onmessage = async ({ data }) => {
    try {
        let { UserCode, SN: DeviceID, RecordDate, RecordNumber } = JSON.parse(
            data
        ).Data;


        if (UserCode > 0) {

            RecordDate = RecordDate.replace("T", " ").replace(/:\d+$/, '');

            const sanitizedValues = [
                UserCode,
                RecordDate,
                DeviceID,
                RecordNumber,
                ++last_record_number
            ];

            const query = format(
                'INSERT INTO visitor_logs("UserID", "LogTime", "DeviceID", "SerialNumber", "last_record_number") VALUES (%L)',
                sanitizedValues
            );

            await pool.query(query);

            let str = sanitizedValues.join(",");

            console.log("Data Inserted. " + str);

            fs.appendFileSync("visitor-logs.csv", str + "\n");
        }
    } catch ({ message }) {
        // sendWhatsappMessage(message);
        console.log(message);
    }
};
