<template>
  <div>
    <v-dialog persistent v-model="announcementDialog" max-width="60%">
      <v-card>
        <v-card-title dense class="popup_background">
          Announcement
          <v-spacer></v-spacer>
          <v-icon @click="announcementDialog = false" outlined dark>
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <h3>{{ announcementViewObj.title }}</h3>

            <div v-html="announcementViewObj.description"></div>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-row>
      <v-col md="10">
        <h4 class="pl-2">Announcements</h4>
      </v-col>
      <v-col md="2" class="menu-icon-right">
        <v-menu bottom left>
          <template v-slot:activator="{ on, attrs }">
            <v-btn dark-2 icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list width="120" dense>
            <v-list-item @click="viewLogs()">
              <v-list-item-title style="cursor: pointer">
                View All
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-col>
    </v-row>
    <v-row v-if="loading">
      <ComonPreloader icon="face-scan" />
    </v-row>
    <v-row
      v-else
      v-for="(announcement, i) in data"
      :key="i"
      @click="viewDetails(announcement)"
    >
      <v-col
        md="2"
        sm="2"
        xs="2"
        class="text-right"
        style="text-align: right; float: right"
      >
        <!-- <v-avatar color="#FFB74D" size="40">
            <v-icon
              size="20"
              style="color: #fff"
              :color="i % 2 == 0 ? 'green' : 'red'"
              >mdi-email</v-icon
            >
          </v-avatar> -->

        <v-img
          style="
            border-radius: 50%;
            height: auto;
            width: 40px;
            max-width: 40px;
            float: right;
          "
          :src="getUserPic(announcement)"
        >
        </v-img>
      </v-col>

      <v-col md="10" sm="10" xs="10">
        <div style="font-weight: bold">{{ announcement.title }}</div>

        <div
          class="breakthewords"
          v-html="announcement.description.replace(/<[^>]*>/g, '')"
        ></div>

        <v-row>
          <v-col md="6" sm="6" xs="6">
            <div style="color: grey; font-size: 12px">
              {{ announcement.dateTime }}
            </div>
          </v-col>
          <v-col
            md="6"
            sm="6"
            xs="6"
            class="text-center"
            style="font-size: 12px"
          >
            <span :style="getPriorityColor(announcement.category)">{{
              announcement.category && announcement.category.name
            }}</span>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
    <v-col md="12" v-if="data.length == 0">
      <center>0 Announcements</center></v-col
    >
  </div>
</template>
<script>
export default {
  props: ["branch_id"],
  data: () => ({
    announcementDialog: false,
    announcementViewObj: {},
    options: {},
    Model: "Announcement",
    endpoint: "announcement_list",
    loading: false,

    headers: [
      {
        text: "Title",
        align: "left",
        sortable: true,
        key: "title",
        value: "title",
      },
      {
        text: "Start Date",
        align: "left",
        sortable: true,
        value: "start_date",
        key: "start_date",
      },
      {
        text: "End Date",
        align: "left",
        sortable: true,
        value: "end_date",
        key: "end_date",
      },
    ],

    data: [],
  }),
  watch: {
    branch_id() {
      this.$store.commit("dashboard/announcements", null);
      this.getDataFromApi();
    },
  },
  created() {
    this.getDataFromApi();
  },

  methods: {
    viewDetails(announcement) {
      this.announcementViewObj = announcement;
      this.announcementDialog = true;
    },
    getUserPic(announcement) {
      let name = "";
      if (announcement.user != null) {
        if (announcement.user && announcement.user.user_type == "company") {
          return announcement.user.company.logo;
        } else if (
          announcement.user &&
          announcement.user.user_type == "employee"
        ) {
          return announcement.user.employee.profile_picture;
        }
      } else {
        return "/no-profile-image.jpg";
      }
    },
    getPriorityColor(category) {
      if (category == null) return "";
      else {
        if (category.name == "Urgent") {
          return "color:#F44336";
        } else if (category.name == "Informational") {
          return "color:#3F51B5";
        } else if (category.name == "Meeting") {
          return "color:#FF5722";
        } else if (category.name == "Priority") {
          return "color:#4CAF50";
        } else if (category.name == "Low Priority") {
          return "color:#000000";
        }
      }
    },
    viewLogs() {
      this.$router.push("/employees/announcements");
      return false;
    },
    getDataFromApi() {
      let { announcements } = this.$store.state.dashboard;

      if (announcements) {
        this.data = announcements;
        return;
      }

      setTimeout(() => {
        this.loading = true;

        let { sortBy, sortDesc, page } = this.options;

        let sortedBy = sortBy ? sortBy[0] : "";
        let sortedDesc = sortDesc ? sortDesc[0] : "";

        let options = {
          params: {
            page,
            sortBy: sortedBy,
            sortDesc: sortedDesc,
            per_page: 4,
            company_id: this.$auth.user.company_id,
          },
        };

        this.$axios
          .get(
            "/employee-announcements/" + this.$auth.user.employee.id,
            options
          )
          .then(({ data }) => {
            this.loading = false;
            this.data = data.data;
            this.$store.commit("dashboard/announcements", data.data);
            if (!data.total) this.headers;
          });
      }, 1000 * 5);
    },
  },
};
</script>

<style scoped>
.center-both {
  height: 31vh;
  /* Adjust the height as needed */
  display: flex;
  align-items: center;
  justify-content: center;
}

.breakthewords {
  display: -webkit-box;
  font-size: 12px;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
