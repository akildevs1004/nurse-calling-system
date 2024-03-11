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
          vertical
        >
          <v-list-item-icon class="ma-2">
            <v-icon>{{ i.icon }}</v-icon>
          </v-list-item-icon>
          <v-list-item-title class="text-center">
            {{ i.title }}&nbsp;
          </v-list-item-title>
        </v-list-item>
        <v-list-item
          v-else
          :class="!miniVariant || 'pl-2'"
          @click="i.open_menu = !i.open_menu"
        >
          <v-list-item-icon class="mx-2">
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
          <div v-for="(j, jdx) in i.hasChildren" :key="jdx">
            <v-tooltip
              style="margin-left: 25px"
              v-if="miniVariant"
              right
              color="primary"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-list-item
                  v-bind="attrs"
                  v-on="on"
                  style="min-height: 0"
                  :to="j.to"
                  class="submenutitle"
                >
                  <v-list-item-title class="my-2">
                    {{ j.title }}
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
              </template>
              <span>{{ j.title }}</span>
            </v-tooltip>

            <v-list-item
              v-else
              style="min-height: 0; margin-left: 50px"
              :to="j.to"
              class="submenutitle"
            >
              <v-list-item-title v-if="can(j.menu)" class="my-2">
                {{ j.title }}
              </v-list-item-title>

              <!-- <v-list-item-icon
                :style="miniVariant ? 'margin-left: -54px;' : ''"
              >
                <v-icon
                  :to="j.to"
                  :style="miniVariant ? 'margin-left: 12px;' : ''"
                >
                  {{ j.icon }}
                </v-icon>
              </v-list-item-icon> -->
            </v-list-item>

            <!-- v-show="!miniVariant" -->
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
      <span class="text-overflow">{{ title }}</span>

      <v-spacer></v-spacer>
      <span style="100%">
        <v-bottom-navigation
          dense
          elevation="0"
          :value="0"
          background-color="primary"
        >
          <v-btn
            text
            class="btn-text-size"
            :elevation="menuProperties['dashboard'].elevation"
            :color="menuProperties['dashboard'].selected"
            @click="setTopMenuItems('dashboard', '/dashboard2')"
          >
            <span>Dashboard</span>

            <v-icon>mdi-view-dashboard</v-icon>
          </v-btn>
          <v-btn
            text
            class="btn-text-size"
            :elevation="menuProperties['settings'].elevation"
            :color="menuProperties['settings'].selected"
            @click="setTopMenuItems('settings', '/branches')"
          >
            <span>Settings</span>

            <v-icon>mdi-keyboard-settings-outline</v-icon>
          </v-btn>

          <v-btn
            text
            class="btn-text-size"
            :elevation="menuProperties['employees'].elevation"
            :color="menuProperties['employees'].selected"
            @click="setTopMenuItems('employees', '/employees')"
          >
            <span>Employees</span>

            <v-icon>mdi-account-tie</v-icon>
          </v-btn>

          <v-btn
            text
            class="btn-text-size"
            :elevation="menuProperties['attendance'].elevation"
            :color="menuProperties['attendance'].selected"
            @click="setTopMenuItems('attendance', '/dashboard/1')"
          >
            <span>Attendance</span>

            <v-icon>mdi-calendar-clock</v-icon>
          </v-btn>
          <v-btn
            text
            class="btn-text-size"
            :elevation="menuProperties['payroll'].elevation"
            :color="menuProperties['payroll'].selected"
            @click="setTopMenuItems('payroll', '/payroll/salary')"
          >
            <span>Payroll</span>

            <v-icon>mdi-cash-multiple</v-icon>
          </v-btn>
          <v-btn
            text
            class="btn-text-size"
            :elevation="menuProperties['access_control'].elevation"
            :color="menuProperties['access_control'].selected"
            @click="setTopMenuItems('access_control', '/timezone')"
          >
            <span>Access Control </span>

            <v-icon>mdi-security</v-icon>
          </v-btn>
          <v-btn
            text
            class="btn-text-size"
            :elevation="menuProperties['visitors'].elevation"
            :color="menuProperties['visitors'].selected"
            @click="setTopMenuItems('visitors', '/visitor-dashboard')"
          >
            <span>Visitor App</span>

            <v-icon>mdi-account-check-outline</v-icon>
          </v-btn>
        </v-bottom-navigation>
        <!-- <v-btn text color="#6946dd" class="text-white"> Primary </v-btn
        ><v-btn text color="#6946dd"> Primary </v-btn
        ><v-btn text color="#6946dd"> Primary </v-btn
        ><v-btn text color="#6946dd"> Primary </v-btn
        ><v-btn text color="#6946dd"> Primary </v-btn> -->
      </span>
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
          <label class="px-2 text-overflow" v-bind="attrs" v-on="on">
            <!-- {{ getUser }} -->
          </label>

          <v-btn icon color="yellow" v-bind="attrs" v-on="on">
            <!-- {{ getUser }} -->
            <v-avatar size="35">
              <v-img
                :src="`https://ui-avatars.com/api/?name=${getUser}`"
              ></v-img>
              <!-- <img :src="getLogo || '/no-image.PNG'" /> -->
            </v-avatar>
          </v-btn>
        </template>

        <v-list light nav dense>
          <v-list-item-group color="primary">
            <v-list-item
              v-if="this.$auth && this.$auth.user.user_type == 'company'"
              @click="goToCompany()"
            >
              <v-list-item-icon>
                <v-icon>mdi-account-multiple-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="black--text"
                  >Profile</v-list-item-title
                >
              </v-list-item-content>
            </v-list-item>

            <v-list-item
              v-else-if="getLoginType == 'manager' || hasDepartments"
              @click="setLoginType"
            >
              <v-list-item-icon>
                <v-icon>mdi-account-multiple-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="black--text"
                  >Login As
                  {{
                    caps(getLoginType == "manager" ? "employee" : "manager")
                  }}</v-list-item-title
                >
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
      <label class=" ">
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
      <v-container style="max-width: 100%">
        <nuxt />
      </v-container>
    </v-main>
    <!-- <v-btn
      height="50"
      width="20"
      dark
      :color="changeColor"
      class="fixed-setting"
      @click.stop="rightDrawer = !rightDrawer"
    >
      <v-icon class="spin" dark size="25">mdi-cog</v-icon>
    </v-btn> -->
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
import company_menus from "../menus/company.json";
import employee_menus from "../menus/employee.json";

export default {
  data() {
    return {
      menuProperties: {
        dashboard: {
          elevation: 0,
          selected: "",
        },
        settings: {
          elevation: 0,
          selected: "",
        },
        employees: {
          elevation: 0,
          selected: "",
        },
        attendance: {
          elevation: 0,
          selected: "",
        },
        payroll: {
          elevation: 0,
          selected: "",
        },
        access_control: {
          elevation: 0,
          selected: "",
        },
        visitors: {
          elevation: 0,
          selected: "",
        },
      },

      topMenu_Selected: "dashboard",
      company_menus,
      employee_menus,
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

      items: [],
      modules: {
        module_ids: [],
        module_names: [],
      },
      clipped: true,

      miniVariant: false,
      title: "MyTime2Cloud",
      socket: null,
      logout_btn: {
        icon: "mdi-logout",
        label: "Logout",
      },
    };
  },
  created() {
    this.$store.commit("loginType", this.$auth.user.user_type);
    this.getCompanyDetails();
    this.setMenus();
    this.deviceList();
    this.getEmployeeList();

    this.setTopMenuItems("dashboard", "/dashboard2");
  },

  mounted() {
    // this.verifyLeaveNotificationsApi();
    // setInterval(() => {
    //   if (this.socketConnectionStatus != 1) {
    //     //socket connection is closed
    //     this.verifyLeaveNotificationsSocket();
    //   }
    // }, 1000 * 60);
  },
  watch: {
    // socketConnectionStatus(val) {
    //   console.log('watch ', val);
    // },
  },
  computed: {
    changeColor() {
      return this.$store.state.color;
    },

    getUser() {
      const user = this.$auth.user;
      const userType = user.user_type;

      if (userType === "master") {
        return user.name;
      } else if (userType === "company") {
        return user.company.name;
      }

      const employee = user.employee;
      if (employee) {
        return employee.display_name || employee.first_name;
      }

      return null; // Or some default value indicating no user found
    },

    getLogo() {
      if (this.$auth.user && this.$auth.user.user_type == "master") {
        return "/no-image.PNG";
      }
      return this.$auth.user && this.$auth.user.company.logo;
    },
    getLoginType() {
      return this.$store.state.loginType;
    },
    hasDepartments() {
      return this.$auth.user && this.$auth.user.assignedDepartments.length > 0;
    },
  },
  methods: {
    setTopMenuItems(menu_name, page) {
      this.topMenu_Selected = menu_name;

      let bgColor = "#6946dd";
      this.setMenus();

      // Check if menu_name exists in menuProperties
      if (this.menuProperties.hasOwnProperty(menu_name)) {
        for (const key in this.menuProperties) {
          this.menuProperties[key].elevation = 0;
          this.menuProperties[key].selected = "";
        }

        this.menuProperties[menu_name].elevation = 0;
        this.menuProperties[menu_name].selected = bgColor;
      }

      this.$router.push(page);
    },
    deviceList() {
      this.$axios
        .get(`device_list_not_manual`, {
          params: { company_id: this.$auth.user.company_id },
        })
        .then(({ data }) => {
          this.$store.commit("devices", data);

          console.log(
            "  console.log(this.$store.state.devices);",
            this.$store.state.devices
          );
        });
    },
    getEmployeeList() {
      this.$axios
        .get(`employee`, {
          params: { per_page: 1000, company_id: this.$auth.user.company_id },
        })
        .then(async ({ data }) => {
          this.$store.commit("employees", data.data);
        });
    },
    setMenus() {
      if (this.getLoginType === "company") {
        // this.items = this.company_menus;
        this.items = this.company_menus.filter(
          (item) => item.module === this.topMenu_Selected
        );

        return;
      }

      let { permissions: perms } = this.$auth.user;

      let menus = {
        manager: this.company_menus,
        employee: this.employee_menus,
      };

      this.items = menus[this.getLoginType].filter(({ menu }) =>
        perms.includes(menu)
      );
    },
    setLoginType() {
      this.$store.commit(
        "loginType",
        this.getLoginType == "manager" ? "employee" : "manager"
      );
      this.setMenus();
    },
    navigateToLeavePage() {
      this.$router.push("/leaves");
    },
    verifyLeaveNotificationsSocket() {
      // 0	CONNECTING	Socket has been created.The connection is not yet open.
      // 1	OPEN	The connection is open and ready to communicate.
      // 2	CLOSING	The connection is in the process of closing.
      // 3	CLOSED

      let company_id = this.$auth.user.company_id;

      if (!process.env.ADMIN_LEAVE_NOTIFICATION_SOCKET_ENDPOINT) return false;
      this.socket = new WebSocket(
        process.env.ADMIN_LEAVE_NOTIFICATION_SOCKET_ENDPOINT
      );

      this.socket.onopen = function () {
        this.socketConnectionStatus = this.socket.readyState;

        const data = {
          company_id: company_id,
        };
        this.socket.send(JSON.stringify(data)); // this works
      };
      this.socket.onclose = function () {
        this.socketConnectionStatus = 0;
      };
      this.socket.onmessage = ({ data }) => {
        data = JSON.parse(data);
        if (data.status && data.new_leaves_data[0]) {
          let element = data.new_leaves_data[0];
          //data.new_leaves_data.data.forEach(element => {

          this.snackNotification = true;
          this.snackNotificationText =
            "New Leave Notification - From : " +
            element.first_name +
            " " +
            element.last_name;
        }
        this.pendingLeavesCount = data.total_pending_count;
      };
    },
    verifyLeaveNotificationsApi() {
      if (!this.$auth.user.company_id) return false;
      let options = {
        params: {
          company_id: this.$auth.user.company_id || 0,
        },
      };

      this.$axios.get(`employee_leaves_new`, options).then(({ data }) => {
        if (data.status) {
          data.new_leaves_data.data.forEach((element) => {
            this.snackNotification = true;

            this.snackNotificationText =
              "New Leave Notification : From : " +
              element.employee.first_name +
              " " +
              element.employee.last_name;
          });

          this.pendingLeavesCount = data.total_pending_count;
        }
      });
    },
    collapseSubItems() {
      this.company_menus.map((item) => (item.active = false));
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
    goToLeaves() {
      this.$router.push("/leaves");
    },
    goToCompany() {
      this.$router.push(`/companies/${this.$auth.user?.company_id}`);
    },
    getCompanyDetails() {
      this.$axios
        .get(`company/${this.$auth.user?.company_id}`)
        .then(({ data }) => {
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
<style scoped>
.btn-text-size {
  font-size: 17px !important;
}
</style>
