<template>
  <v-app>
    <v-navigation-drawer
      v-model="drawer"
      dark
      :mini-variant="miniVariant"
      :clipped="clipped"
      fixed
      app
      :color="sideBarcolor"
      :style="miniVariant ? 'width: 60px' : ''"
      @transitionend="collapseSubItems"
    >
      <br />
      <v-list v-for="(i, idx) in items" :key="idx" style="padding: 5px 0 0 0px">
        <v-list-item
          :to="i.to"
          router
          v-if="!i.hasChildren"
          :class="!miniVariant || 'pl-2'"
        >
          <v-list-item-icon class="ma-2">
            <v-icon>{{ i.icon }}</v-icon>
          </v-list-item-icon>
          <v-list-item-title> {{ i.title }}&nbsp; </v-list-item-title>
        </v-list-item>
        <v-list-item
          v-else
          :class="!miniVariant || 'pl-2'"
          @click="i.open_menu = !i.open_menu"
        >
          <v-list-item-icon class="ma-2">
            <v-icon>{{ i.icon }}</v-icon>
            <v-icon v-if="miniVariant" small
              >{{ !i.open_menu ? "mdi-chevron-down" : "mdi-chevron-up" }}
            </v-icon>
          </v-list-item-icon>

          <v-list-item-title>{{ i.title }} </v-list-item-title>
          <v-icon small
            >{{ !i.open_menu ? "mdi-chevron-down" : "mdi-chevron-up" }}
          </v-icon>
        </v-list-item>
        <div v-if="i.open_menu">
          <div
            style="margin-left: 54px"
            v-for="(j, jdx) in i.hasChildren"
            :key="jdx"
          >
            <!-- v-show="!miniVariant" -->
            <v-list-item style="min-height: 0" :to="j.to" class="submenutitle">
              <v-list-item-title v-if="!miniVariant"
                >{{ j.title }}
              </v-list-item-title>

              <v-list-item-icon
                :style="miniVariant ? 'margin-left: -54px;' : ''"
              >
                <v-icon
                  :to="j.to"
                  :style="miniVariant ? 'margin-left: 12px;' : ''"
                >
                  {{ j.icon }}
                </v-icon>
              </v-list-item-icon>
            </v-list-item>
          </div>
        </div>
      </v-list>
    </v-navigation-drawer>

    <!-- style="
    margin-left: -49px;
" -->

    <v-app-bar
      :color="changeColor"
      dark
      :clipped-left="clipped"
      fixed
      app
      :style="$nuxt.$route.name == 'index' ? 'z-index: 100000' : ''"
    >
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <v-btn icon @click.stop="miniVariant = !miniVariant">
        <v-icon>mdi-{{ `chevron-${miniVariant ? "right" : "left"}` }}</v-icon>
      </v-btn>
      <v-btn icon @click.stop="clipped = !clipped">
        <v-icon>mdi-application</v-icon>
      </v-btn>
      {{ title }}
      <v-spacer></v-spacer>

      <v-menu
        nudge-bottom="50"
        transition="scale-transition"
        origin="center center"
        bottom
        left
        min-width="200"
        nudge-left="20"
      >
        <template v-slot:activator="{ on, attrs }">
          <label class="px-2" v-bind="attrs" v-on="on">
            {{ getUser }}
          </label>

          <v-btn icon color="yellow" v-bind="attrs" v-on="on">
            <v-avatar>
              <img :src="getLogo || '/no-image.PNG'" />
            </v-avatar>
          </v-btn>
        </template>

        <v-list light nav dense>
          <v-list-item-group color="primary">
            <v-list-item @click="goToProfile()">
              <v-list-item-icon>
                <v-icon>mdi-account-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="black--text"
                  >Profile</v-list-item-title
                >
              </v-list-item-content>
            </v-list-item>

            <v-list-item @click="goToProfile()">
              <v-list-item-icon>
                <v-icon>mdi-account-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="black--text"
                  >Login As {{ loginType }}
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>

            <v-list-item @click="logout">
              <v-list-item-icon>
                <v-icon>mdi-logout</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="black--text"
                  >Logout</v-list-item-title
                >
              </v-list-item-content>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>
      <label class="px-2">
        <!-- <v-icon v-if="pendingLeavesCount == 0">mdi mdi-bell</v-icon>
        <span v-else>
          <v-icon title="0 Pending leaves" @click="snackNotificationText != '' && snackNotification == true"
            color="success">mdi mdi-bell-ring
          </v-icon>
          <v-chip title="Pending Count" color="white" style="color:black" to="/employees/leave"><strong>{{
            pendingLeavesCount }}</strong></v-chip>
        </span> -->
        <!-- <v-badge color="red" :content="pendingLeavesCount">
          <v-icon title="0 Pending leaves" color="success">mdi mdi-bell-ring
          </v-icon>
        </v-badge> -->
        <v-badge
          v-if="pendingLeavesCount > 0"
          @click="navigateToLeavePage()"
          :color="pendingLeavesCount > 0 ? 'red' : 'black'"
          :content="pendingLeavesCount"
        >
          <v-icon @click="navigateToLeavePage()">mdi mdi-bell-ring</v-icon>
        </v-badge>
        <v-badge
          v-else
          @click="navigateToLeavePage()"
          color="black"
          content="0"
        >
          <v-icon @click="navigateToLeavePage()">mdi mdi-bell-ring</v-icon>
        </v-badge>
      </label>
      <v-snackbar
        top="top"
        v-model="snackNotification"
        location="right"
        :timeout="5000"
        :color="snackNotificationColor"
      >
        {{ snackNotificationText }}

        <template v-slot:action="{ attrs }">
          <v-btn v-bind="attrs" text @click="snackNotification = false">
            Close
          </v-btn>
        </template>
      </v-snackbar>
    </v-app-bar>

    <v-main class="main_bg">
      <v-container>
        <nuxt />
      </v-container>
    </v-main>
    <v-btn
      height="50"
      width="20"
      dark
      :color="changeColor"
      class="fixed-setting"
      @click.stop="rightDrawer = !rightDrawer"
    >
      <v-icon class="spin" dark size="25">mdi-cog</v-icon>
    </v-btn>
    <!-- setting -->
    <v-navigation-drawer
      v-model="rightDrawer"
      :clipped="true"
      :right="right"
      fixed
      style="z-index: 1000"
    >
      <v-row style="margin-top: 50px">
        <v-col>
          <v-card class="pa-2" elevation="0">
            <v-col cols="12">
              <div class="mb-3">
                <Strong>Theme</Strong>
              </div>
              <div
                class="btn-group"
                role="group"
                aria-label="Basic radio toggle button group"
              >
                <input
                  type="radio"
                  class="btn-check"
                  name="theme"
                  id="light"
                  autocomplete="off"
                  @click="changeTheme('light')"
                />
                <label class="btn" :class="'btn-outline-dark'" for="light"
                  >Light</label
                >
                <input
                  type="radio"
                  class="btn-check"
                  name="theme"
                  id="dark"
                  autocomplete="off"
                  @click="changeTheme('dark')"
                />
                <label class="btn btn-outline-dark" for="dark">Dark</label>
              </div>
            </v-col>
            <v-divider></v-divider>
            <v-col cols="12">
              <div class="mb-3">
                <Strong>Top Bar</Strong>
              </div>
              <div class="d-flex">
                <v-btn
                  class="mx-2 stg-color-icon"
                  fab
                  dark
                  x-small
                  color="primary"
                  @click="changeTopBarColor('primary')"
                ></v-btn>
                <v-btn
                  class="mx-2 stg-color-icon"
                  fab
                  dark
                  x-small
                  color="error"
                  @click="changeTopBarColor('error')"
                ></v-btn>
                <v-btn
                  class="mx-2 stg-color-icon"
                  fab
                  dark
                  x-small
                  color="indigo"
                  @click="changeTopBarColor('indigo')"
                ></v-btn>
                <v-btn
                  class="mx-2 stg-color-icon"
                  fab
                  dark
                  x-small
                  color="background"
                  @click="changeTopBarColor('background')"
                ></v-btn>
              </div>
            </v-col>
            <v-divider></v-divider>
            <v-col cols="12">
              <div class="mb-3">
                <Strong>Side Bar</Strong>
              </div>
              <div class="d-flex">
                <v-btn
                  class="mx-2 stg-color-icon"
                  fab
                  dark
                  x-small
                  color="primary"
                  @click="changeSideBarColor('primary')"
                ></v-btn>
                <v-btn
                  class="mx-2 stg-color-icon"
                  fab
                  dark
                  x-small
                  color="error"
                  @click="changeSideBarColor('error')"
                ></v-btn>
                <v-btn
                  class="mx-2 stg-color-icon"
                  fab
                  dark
                  x-small
                  color="indigo"
                  @click="changeSideBarColor('indigo')"
                ></v-btn>
                <v-btn
                  class="mx-2 stg-color-icon"
                  fab
                  dark
                  x-small
                  color="background"
                  @click="changeSideBarColor('background')"
                >
                </v-btn>
              </div>
            </v-col>
          </v-card>
        </v-col>
      </v-row>
    </v-navigation-drawer>
  </v-app>
</template>

<script>
export default {
  data() {
    return {
      pendingLeavesCount: 0,
      snackNotificationText: "",
      snackNotification: false,
      snackNotificationColor: "black",

      socketConnectionStatus: 0,
      miniVariant: false,
      right: true,
      rightDrawer: false,
      color: "",
      sideBarcolor: "background",
      year: new Date().getFullYear(),
      dropdown_menus: [{ title: "setting" }, { title: "logout" }],
      clipped: false,
      open_menu: [],
      drawer: true,
      fixed: false,
      order_count: "",
      menus: [],
      // -------
      // leave request
      // holiday
      items: [],
      modules: {
        module_ids: [],
        module_names: [],
      },
      clipped: true,

      miniVariant: false,
      title: "ideaHRMS",
      logout_btn: {
        icon: "mdi-logout",
        label: "Logout",
      },
    };
  },
  created() {
    this.$router.push(`/login`);

    return false;
    let { permissions } = this.$auth.user;

    this.menus.forEach((ele) => {
      if (permissions.includes(ele.menu)) {
        this.items.push(ele);
      }
    });

    this.getCompanyDetails();

    // setInterval(() => {
    //   this.verifyLeaveNotifications();
    // }, 1000 * 60 * 60);
  },

  mounted() {
    // this.verifyLeaveNotificationsApi();
    // setInterval(() => {
    //   if (this.socketConnectionStatus != 1) {
    //     //socket connection is closed
    //     this.verifyLeaveNotificationsSocket();
    //   }
    // }, 1000 * 60 * 1);
  },

  computed: {
    changeColor() {
      return this.$store.state.color;
    },

    getUser() {
      return this.$auth.user &&
        this.$auth.user.employee &&
        this.$auth.user.company
        ? this.$auth.user.employee.first_name +
            " " +
            this.$auth.user.employee.last_name
        : this.$auth.user.company.name;

      // $user->assigned_department
    },

    loginType() {
      return this.$auth.user && this.$auth.user.login_type;
      // $user->assigned_department
    },

    getLogo() {
      return this.$auth.user && this.$auth.user.company.logo;
    },
  },
  methods: {
    navigateToLeavePage() {
      this.$router.push("/employees/leaves");
    },

    verifyLeaveNotificationsSocket() {
      let company_id = this.$auth.user.company_id;
      let employee_id = this.$auth.user.employee.id;

      if (!process.env.EMP_LEAVE_NOTIFICATION_SOCKET_ENDPOINT) return false;
      let ws = new WebSocket(
        process.env.EMP_LEAVE_NOTIFICATION_SOCKET_ENDPOINT
      );

      ws.onopen = function () {
        this.socketConnectionStatus = ws.readyState;
        const data = {
          company_id: company_id,
          employee_id: employee_id,
        };
        ws.send(JSON.stringify(data)); // this works
      };
      ws.onclose = function () {
        this.socketConnectionStatus = 0;
      };
      ws.onmessage = ({ data }) => {
        data = JSON.parse(data);
        if (data.status && data.new_leaves_data[0]) {
          let element = data.new_leaves_data[0];
          //data.new_leaves_data.data.forEach(element => {

          if (element.status == 1) {
            this.snackNotification = true;
            this.snackNotificationColor = "background";
            this.snackNotificationText = "Your Leave Application is Approved";
          } else if (element.status == 2) {
            this.snackNotification = true;
            this.snackNotificationColor = "error";
            this.snackNotificationText = "Your Leave Application is Rejected";
          }
        }
        this.pendingLeavesCount = data.total_pending_count;
      };
    },
    verifyLeaveNotificationsApi() {
      if (!this.$auth.user?.company?.id) return false;
      let options = {
        params: {
          company_id: this.$auth.user?.company?.id || 0,
          employee_id: this.$auth.user.employee.id,
        },
      };

      this.$axios
        .get(`employee_leaves_new_by_employee`, options)
        .then(({ data }) => {
          if (data.status && data.new_leaves_data.data[0]) {
            let element = data.new_leaves_data.data[0];
            //data.new_leaves_data.data.forEach(element => {

            this.snackNotification = true;

            if (element.status == 1) {
              this.snackNotificationColor = "primary";
              this.snackNotificationText = "Your Leave Application is Approved";
            } else if (element.status == 2) {
              this.snackNotificationColor = "error";
              this.snackNotificationText = "Your Leave Application is Rejected";
            }

            //});
          }
          this.pendingLeavesCount = data.total_pending_count;
        });
    },
    collapseSubItems() {
      this.menus.map((item) => (item.active = false));
    },
    changeTopBarColor(color) {
      this.color = color;
      this.$store.commit("change_color", color);
    },

    changeTheme(color) {
      // alert(color);
    },

    changeSideBarColor(color) {
      this.sideBarcolor = color;
    },

    caps(str) {
      return str.replace(/\b\w/g, (c) => c.toUpperCase());
    },
    goToSetting() {
      this.$router.push("/setting");
    },
    goToProfile() {
      //let u = this.$auth.user.user_type;
      // if(u){
      // this.$router.push(`/empl/${this.$auth.user?.company?.id}`);
      // }
      this.$router.push(`/employees/profile`);
    },
    getCompanyDetails() {
      let user = this.$auth.user;

      this.$axios.get(`company/${user?.company?.id}`).then(({ data }) => {
        let { modules } = data.record;

        if (modules !== null) {
          this.modules = {
            module_ids: modules.module_ids || [],
            module_names: modules.module_names.map((e) => ({
              icon: "mdi-chart-bubble",
              title: this.caps(e),
              to: "/" + e + "_modules",
              permission: true,
            })),
          };
        }
      });
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    logout() {
      this.$axios.get(`/logout`).then(({ res }) => {
        this.$auth.logout();
      });
    },
  },
};
</script>
