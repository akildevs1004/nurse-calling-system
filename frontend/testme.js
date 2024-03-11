const axios = require("axios");
let data = JSON.stringify({
  email: "francisgill1002@gmail.com",
  password: "secret",
});

let config = {
  method: "get",
  maxBodyLength: Infinity,
  url: "https://backend.mytime2cloud.com/api/me",
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
    Authorization: "Bearer 781|C7TbSyJWbW2m3qolr1OdUCols0aHxMeaBQ44DO05",
  },
  data: data,
};

axios
  .request(config)
  .then((response) => {
    console.log(JSON.stringify(response.data));
  })
  .catch((error) => {
    console.log(error);
  });
