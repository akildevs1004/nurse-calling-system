const axios = require('axios');

const fs = require("fs");

// Define the URL you want to hit
const url = 'http://127.0.0.1:8080/api/employeesDepartmentMapping/22';

// Function to make the HTTP request
const makeHttpRequest = () => {



    for (let i = 5; i < 10; i++) {
        let fileName = `./data_${i + 1}.json`;
        const json = require(fileName);
        console.log(json);


        axios
        .get(`http://127.0.0.1:8080/api/employeesDepartmentMapping/22`,{params:{
            
        }})
        .then(({ data }) => {
            // Handle the response if needed
            console.log('Request successful:', data);

            fs.appendFileSync("./logs.csv", data + "\n");
        })
        .catch((error) => {
            // Handle any errors
            console.error('Request error:', error.message);
        });

    }
    
};
makeHttpRequest();
// const interval = 2000;
// setInterval(makeHttpRequest, interval);

// // Log a message to indicate that the script is running
// console.log(`Requesting ${url} every ${interval / 1000} seconds...`);
