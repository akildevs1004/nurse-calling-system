const axios = require('axios');


const fs = require("fs");

// Define the URL you want to hit
const url = 'https://backend.mytime2cloud.com/api/employeesShortList?company_id=22';

// const url = 'https://backend.eztime.online/api/departments?company_id=1&per_page=51';


// Function to make the HTTP request
const makeHttpRequest = () => {
    axios
        .get(url)
        .then(({ data }) => {
            const jsonData = data.map(e => ({
                system_user_id: e.system_user_id,
                department_id: e.branch_test.departments[0].id
            }));

            // Convert the JSON data to a string
            const jsonString = JSON.stringify(jsonData, null, 2); // 2 spaces for indentation

            // Write the data to a JSON file
            fs.writeFile('data.json', jsonString, 'utf8', (err) => {
                if (err) {
                    console.error('Error writing file:', err);
                } else {
                    console.log('Data has been successfully written to data.json');
                }
            });
        })
        .catch((error) => {
            // Handle any errors
            console.error('Request error:', error.message);
        });
};

makeHttpRequest()

// const interval = 2000;
// setInterval(makeHttpRequest, interval);

// // Log a message to indicate that the script is running
// console.log(`Requesting ${url} every ${interval / 1000} seconds...`);
