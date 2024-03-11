let { Client } = require("pg");

const client = new Client({
  host: "127.0.0.1",
  user: "postgres",
  port: 5432,
  password: "tZqsls7URjNC0aF",
  database: "ideahrms"
});

client.connect();

client.query("select count(*) from attendance_logs", (err, result) => {
  if (err) {
    console.log(err);
    return;
  }
  console.log(result.rows[0].count);
});
