const fs = require('fs');
const readline = require('readline');

const fileStream = fs.createReadStream('logs.csv');

const rl = readline.createInterface({
    input: fileStream,
    crlfDelay: Infinity // To handle Windows line endings (CRLF)
});

const arr = [];

// Event listener for each line read from the file
rl.on('line', async (line) => {
    // Process each line here\
    let row = await line;
    arr.push(row);
});

// Event listener when the file reading is complete
rl.on('close', () => {
    console.log('File reading complete.');
});

console.log(arr);
