<template>
  <div>
    <v-row>
      <v-card
        class="ma-5 text-center"
        style="width: 100%; height: 800px"
        v-if="devices.length == 0"
      >
        <v-card-text class="pa-10"><h3>No Data is available</h3></v-card-text>
      </v-card>
      <v-col :key="index3" :cols="colsSize" v-for="(device, index3) in devices">
        <!-- <div
          style="
            height: 50px;
            background-color: red;
            border-radius: 30px;
            text-align: left;
            margin: 0 auto;
            display: flex;
            align-items: left;
          "
        >
          <div
            style="
              background-color: white;
              color: black;
              width: 40px;
              height: 40px;
              border-radius: 50%;
              margin: 4px 7px;
            "
          >
            <div>
              <img
                :src="getImage(device)"
                style="
                  max-height: 175px;
                  max-width: 100%;
                  width: 31px;
                  text-align: center;
                  vertical-align: text-top;
                  display: inline-flex;
                  margin-left: 2px;
                  margin-top: 2px;
                "
              />
            </div>
          </div>
          <div
            style="float: left; padding-top: 15px; color: #fff; width: 180px"
          >
            EMERGENCY
          </div>
          <div
            style="
              float: left;
              padding-top: 4px;
              color: #fff;
              font-weight: bold;
              font-size: 30px;
              width: 50px;
              text-align: center;
            "
          >
            {{ device.name }}
          </div>
          <div style="float: left; padding-left: 20px; padding-right: 20px">
            <v-divider vertical color="#FFF"></v-divider>
          </div>

          <div>
            <img
              src="../../static/icon-esclamation.png"
              style="width: 20px; padding-top: 5px"
            />
            
          </div>
          <div
            v-if="device.alarm_start_datetime"
            style="
              float: left;
              padding-top: 4px;
              color: #fff;
              font-weight: bold;
              font-size: 30px;
              width: 50px;
              padding-left: 10px;
            "
          >
            {{ getHourMinuteDifference(device.alarm_start_datetime) }}
          </div>
        </div> -->

        <div>
          <v-card
            elevation="2"
            class="rounded-lg"
            outlined
            :style="
              'height: 60px; width: 100%; background-color: ' +
              getHeadBgColor(device) +
              ''
            "
          >
            <v-card-text
              class="text-center"
              style="font-weight: bold; font-size: 40px; color: #fff"
              >{{ device.name }}
            </v-card-text></v-card
          >
          <v-card
            elevation="2"
            class="rounded-lg"
            :style="
              'height: 200px;  width: 100%; margin-top: -10px; background-color: ' +
              getBodyBgColor(device) +
              ''
            "
          >
            <v-card-text class="text-center">
              <div
                v-if="device.battery_level <= 50"
                style="left: 10px; position: absolute; top: 116px"
              >
                <img style="width: 50px" :src="getWarningImage(device)" />
                <br />
                Battery ({{ device.battery_level }}%)
                <!-- <v-icon>mdi mdi-battery-alert</v-icon> -->
              </div>

              <!-- <img
                :src="getImage(device)"
                :style="
                  'max-height:175px;max-width:100%;;width: ' +
                  boxImageWidth +
                  '%'
                "
              /> -->
              <img
                :src="getImage(device)"
                :style="'max-height:175px;max-width:100%;;width:  150px;height:150px'"
              />
            </v-card-text>
          </v-card>
        </div>
        <!-- <div v-else-if="device.alarm_status == 1">
          <v-card
            elevation="2"
            class="rounded-lg"
            outlined
            style="height: 60px; width: 100%; background-color: #fe0000"
          >
            <v-card-text
              class="text-center"
              style="font-weight: bold; font-size: 40px; color: #fff"
              >{{ device.name }}
            </v-card-text>
          </v-card>
          <v-card
            elevation="2"
            class="rounded-lg"
            style="
              height: 200px;
              width: 100%;
              margin-top: -10px;
              background-color: #11518d;
            "
          >
            <v-card-text class="text-center">
              <img
                :src="getWarningImage(device.category.id)"
                :style="
                  'max-height:175px;max-width:100%;;width: ' +
                  boxImageWidth +
                  '%'
                "
              />
            </v-card-text>
          </v-card>
        </div> -->
      </v-col>
    </v-row>
  </div>
</template>

<script>
// import TimePickerCommon from "../../../components/Snippets/TimePickerCommon.vue";
export default {
  // components: {
  //   TimePickerCommon,
  // },
  data() {
    return {
      colsSize: 2,
      currentTime: "",
      boxImageWidth: "50",
      devices: [],
    };
  },
  computed: {
    currentDate() {
      const months = [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
      ];

      const now = new Date();
      const day = now.getDate();
      const monthIndex = now.getMonth();
      const year = now.getFullYear();
      const formattedDate = `${day} ${months[monthIndex]} ${year}`;
      return formattedDate;
    },
  },
  mounted() {
    let reloadCount = 0;
    // setInterval(() => {
    //   const now = new Date();
    //   this.currentTime = now.toLocaleTimeString([], { hour12: false });
    // }, 1000);

    setInterval(() => {
      this.getDataFromApi();
    }, 1000 * 30);
    const now = new Date();
    console.log("reloadCount", now.toLocaleTimeString([], { hour12: false }));
    setTimeout(() => {
      console.log("reloadCount", reloadCount++);
      window.location.reload();
    }, 1000 * 60 * 15);

    let boxesPerRow = 4;

    if (boxesPerRow == 2) {
      this.boxImageWidth = 22; //%
      this.colsSize = 6;
    } else if (boxesPerRow == 3) {
      this.boxImageWidth = 35; //%
      this.colsSize = 4;
    } else if (boxesPerRow == 4) {
      this.colsSize = 3;
      this.boxImageWidth = 50; //%
    } else if (boxesPerRow == 6) {
      this.boxImageWidth = 70; //%
      this.colsSize = 2;
    }
  },
  created() {
    this.getDataFromApi();
  },
  methods: {
    // getHeadBgColor(device) {
    //   if (device.alarm_status == 0)
    //     return this.$auth.user.company.device_normal_top_color ?? "#11b393";
    //   else if (device.alarm_status == 1)
    //     return this.$auth.user.company.device_normal_body_color ?? "#fe0000";
    // },
    // getBodyBgColor(device) {
    //   if (device.alarm_status == 0)
    //     return this.$auth.user.company.device_alarm_top_color ?? "#11518d";
    //   else if (device.alarm_status == 1)
    //     return this.$auth.user.company.device_alarm_body_color ?? "#ffde00";
    // },
    getHeadBgColor(device) {
      if (this.$auth.user.company)
        if (device.alarm_status == 0) return "#005947";
        else if (device.alarm_status == 1) return "#fe0000";
      // if (this.$auth.user.company)
      //   if (device.alarm_status == 0)
      //     return this.$auth.user.company.device_normal_top_color ?? "#005947";
      //   else if (device.alarm_status == 1)
      //     return this.$auth.user.company.device_alarm_top_color ?? "#fe0000";
    },
    getBodyBgColor(device) {
      console.log(
        " this.$auth.user.company.device_normal_body_color",
        this.$auth.user.company.device_normal_body_color
      );
      if (this.$auth.user.company)
        return this.$auth.user.company.device_normal_body_color ?? "#eba50f";

      // if (this.$auth.user.company)
      //   if (device.alarm_status == 0)
      //     return (
      //       this.$auth.user.company.device_alarm_top_color ?? "#eba50f" //"#d7d7d7"
      //     );
      //   // "#eba50f";
      //   else if (device.alarm_status == 1)
      //     return this.$auth.user.company.device_alarm_body_color ?? "#eba50f";
    },
    getImage(device) {
      //let imagename = "normal";
      let imagename = "warning";

      if (device.alarm_status == 1) imagename = "warning";
      return (
        process.env.BACKEND_URL2 +
        "monitor_icons/" +
        device.category.id +
        "_" +
        imagename +
        ".png" +
        "?t=" +
        Math.random(1, 2)
      );
    },
    getWarningImage(device) {
      let imagename = "warning_yellow";

      if (device.battery_level > 30 && device.battery_level <= 50) {
        imagename = "warning_yellow";
      } else if (device.battery_level <= 20) {
        imagename = "warning_red";
      }

      return (
        process.env.BACKEND_URL2 +
        "monitor_icons/" +
        imagename +
        ".png" +
        "?t=" +
        Math.random(1, 2)
      );
    },
    getHourMinuteDifference(date1) {
      date1 = new Date(date1);

      let date2 = new Date();
      // Calculate the time difference in milliseconds
      let timeDifference = Math.abs(date2.getTime() - date1.getTime());

      // Calculate hours and minutes
      let hours = Math.floor(timeDifference / (1000 * 60 * 60));
      let minutes = Math.floor(
        (timeDifference % (1000 * 60 * 60)) / (1000 * 60)
      );

      // Format the result as "hours:minutes"
      let formattedResult = `${hours}:${minutes < 10 ? "0" : ""}${minutes}`;

      return formattedResult;
    },

    getDataFromApi() {
      let category_id = this.$route.params.id;

      this.$axios
        .get(`devices-list-monitor`, {
          params: {
            company_id: this.$auth.user.company_id,
            category_id: category_id,
          },
        })
        .then(({ data }) => {
          this.devices = data;
        });
    },
  },
};
</script>
