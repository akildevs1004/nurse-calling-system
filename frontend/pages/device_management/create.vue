<template>
  <div v-if="can('device_management')">
    <div v-if="!preloader">
      <v-row class="mt-5 mb-5">
        <v-col cols="10">
          <h3>{{ model }}</h3>
          <div>Dashboard / {{ model }}</div>
        </v-col>
      </v-row>
      <v-card elevation="0" class="pa-3">
        <v-card-title>
          <label class="col-form-label"
            ><b>Upload User List to devices </b></label
          >
          <v-spacer></v-spacer>
          <v-btn small fab color="background" dark to="/device_management">
            <v-icon>mdi-arrow-left</v-icon>
          </v-btn>
        </v-card-title>
        <v-container>
          <v-row>
            <v-col cols="3">
              <v-autocomplete
                :hide-details="!payload.users"
                multiple
                outlined
                dense
                item-text="name"
                item-value="userCode"
                placeholder="Users"
                v-model="payload.users"
                :items="employees"
              >
              </v-autocomplete>

              <span v-if="errors && errors.users" class="error--text">{{
                errors.users[0]
              }}</span>
            </v-col>

            <v-col cols="3">
              <v-autocomplete
                @change="setDevices"
                :hide-details="!payload.devices"
                v-model="payload.devices"
                multiple
                outlined
                dense
                item-text="name"
                item-value="device_id"
                placeholder="Devices"
                :items="devices"
              >
              </v-autocomplete>
              <span v-if="errors && errors.devices" class="error--text">{{
                errors.devices[0]
              }}</span>
            </v-col>

            <v-col cols="12">
              <v-btn small color="primary" @click="store"> Submit </v-btn>
            </v-col>
            <v-col cols="12">
              <table>
                <thead>
                  <tr>
                    <th class="text-left">User Code</th>
                    <th class="text-left">Name</th>
                    <!-- <th class="text-left">
                      Picture
                    </th> -->
                    <th class="text-left">Device ID</th>
                    <th class="text-left">Message</th>
                    <th class="text-left">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(
                      {
                        userCode,
                        name,
                        // profile_picture,
                        device_id,
                        message,
                        status,
                      },
                      index
                    ) in results"
                    :key="index"
                  >
                    <td>{{ userCode }}</td>
                    <td>{{ name }}</td>
                    <!-- <td>
                      <v-img :src="profile_picture"></v-img>
                    </td> -->
                    <td>{{ device_id }}</td>
                    <td>{{ message }}</td>
                    <td>
                      <v-icon :color="!status ? `red` : `green`"
                        >mdi-{{ !status ? `close` : `check` }}</v-icon
                      >
                    </td>
                  </tr>
                </tbody>
              </table>
            </v-col>
          </v-row>
        </v-container>
      </v-card>
    </div>
    <Preloader v-else />
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  data: () => ({
    model: "Upload Users", //Device Management
    menu: false,
    color: "primary",

    preloader: false,
    loading: false,
    response: false,

    payload: {
      users: [],
      devices: [],
      company_id: 0,
    },
    employees: [],
    devices: [],
    errors: [],
    results: [],
    columns: ["id", "display_name", "system_user_id", "profile_picture"],
  }),

  mounted() {},

  created() {
    let options = {
      params: {
        per_page: 1000,
        company_id: this.$auth?.user?.company?.id,
        columns: this.columns,
      },
    };
    this.$axios.get(`employeesList`, options).then(({ data: { data } }) => {
      this.employees = data.map((e) => ({
        userCode: e.system_user_id,
        name: e.display_name,
        profile_picture: e.profile_picture,
      }));
      // this.employees.unshift({ display_name: "All", system_user_id: "-1" });
    });
    // this.$axios.get(`employee`, options).then(({ data: { data } }) => {
    //   this.employees = data.map(e => ({
    //     userCode: e.system_user_id,
    //     name: e.display_name,
    //     device_id: "FC-8300T20094123",
    //     expiry: "2089-12-31 23:59:59",
    //     faceImage:
    //       "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"
    //   }));
    //   // this.employees.unshift({ display_name: "All", system_user_id: "-1" });
    // });

    this.$axios.get(`device`, options).then(({ data: { data } }) => {
      this.devices = data.filter((e) => e.status_id == 1);
      // this.devices.unshift({ name: "All", device_id: "-1" });
    });

    this.preloader = false;
    this.payload.company_id = this.$auth?.user?.company?.id;
  },
  methods: {
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    
    setUsers() {
      this.payload.users = this.payload.users.includes("-1")
        ? this.employees
        : this.employees.filter((e) =>
            this.payload.users.includes(e.system_user_id)
          );
    },
    setDevices() {
      this.payload.devices = this.payload.devices.includes("-1")
        ? this.devices
        : this.devices.filter((e) =>
            this.payload.devices.includes(e.device_id)
          );
    },
    store() {
      this.results = [];

      const { users, devices } = this.payload;
      const employees = this.employees;
      const dIds = devices.map(({ device_id, name: device_name }) => ({
        device_id,
        device_name,
      }));

      const filterUsers = employees.filter((e) => users.includes(e.userCode));

      dIds.forEach((d) => {
        filterUsers.forEach((e) => {
          e.device_id = d.device_id;
          e.device_name = d.device_name;
          e.expiry = "2089-12-31 23:59:59";
          // e.faceImage = e.profile_picture;
          e.faceImage = `https://backend.ideahrms.com/media/employee/profile_picture/WhatsApp%20Image%202022-09-16%20at%202.11.34%20PM%20(1).jpeg`;
          // "https://sdk.ideahrms.com/{$device_id}/AddPerson
          // https://sdk.ideahrms.com/CheckDeviceHealth/$device_id

          // this.$axios
          //   .post("check-device-health", e)
          //   .then(({ data }) => {
          //     this.results.push(e);
          //   })
          //   .catch(e => this.results.push(e.message));

          this.$axios
            .post("upload-users", e)
            .then(({ data }) => {
              this.results.push(data);
            })
            .catch((e) => this.results.push(e.message));
        });
      });
    },
  },
};
</script>
<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
<style scoped>
.tiptap-vuetify-editor__content {
  min-height: 400px !important;
}

.ProseMirror .ProseMirror-focused {
  height: 400px !important;
}

.tiptap-icon .v-icon {
  color: white !important;
}
.tiptap-icon .v-btn--icon {
  color: white !important;
}
</style>
