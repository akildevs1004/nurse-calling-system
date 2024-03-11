
const fs = require('fs');
const axios = require('axios');
const url = 'https://backend.mytime2cloud.com/api/employeesShortList?company_id=22';

const makeHttpRequest = async () => {
    try {
        const response = await axios.get(url);
        const data = response.data;

        // Splitting the data into chunks of 50 records
        const chunkSize = 50;
        for (let i = 0; i < data.length; i += chunkSize) {
            const chunk = data.slice(i, i + chunkSize);

            const jsonData = chunk.map(e => ({
                system_user_id: e.system_user_id,
                department_id: e.branch_test.departments[0].id
            }));

            const jsonString = JSON.stringify(jsonData, null, 2);
            const fileName = `data_${i / chunkSize + 1}.json`;

            fs.writeFileSync(fileName, jsonString, 'utf8');
            console.log(`Data has been written to ${fileName}`);
        }
    } catch (error) {
        console.error('Request error:', error.message);
    }
};

makeHttpRequest();
