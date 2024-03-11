const { cpuUsage } = require('os-utils');

// Function to monitor CPU usage
function monitorCPU() {
    cpuUsage((usage) => {
        console.log(`CPU Usage: ${(usage * 100).toFixed(2)}%`);
    });
}

// Monitor CPU every 1 second
setInterval(monitorCPU, 5 * 1000);
