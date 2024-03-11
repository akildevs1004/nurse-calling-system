<template>
  <v-dialog persistent v-model="dialog" width="1250">
    <template v-slot:activator="{ on, attrs }">
      <v-icon color="secondary" small v-bind="attrs" v-on="on">
        mdi-pencil
      </v-icon>
    </template>
    <v-card>
      <v-tabs
        v-model="tab"
        class="popup_background"
        centered
        icons-and-text
        color="violet"
      >
        <v-tabs-slider></v-tabs-slider>

        <v-tab v-for="(item, index) in tabMenu" :key="index" :href="item.value">
          {{ item.text }}
          <v-icon>{{ item.icon }}</v-icon>
        </v-tab>
        <v-icon
          @click="dialog = false"
          style="margin-right: 4px"
          text-right
          outlined
          dark
          color="black"
        >
          mdi mdi-close-circle
        </v-icon>
      </v-tabs>

      <v-card-text>
        <v-tabs-items v-model="tab">
          <v-tab-item
            v-for="(tb, index) in tabMenu"
            :key="index"
            :value="`${index}`"
          >
            <component
              :is="getComponent(tab)"
              :employeeId="employeeId"
              @close-popup="dialog = false"
              @eventFromchild="getDataFromApi()"
            />
          </v-tab-item>
        </v-tabs-items>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
import Update from "./Update.vue";
import Contact from "./Contact.vue";
import Passport from "./Passport.vue";
import Emirates from "./Emirates.vue";
import Visa from "./Visa.vue";
import Bank from "./Bank.vue";
import Document from "./Document.vue";
import Qualification from "./Qualification.vue";
import Setting from "./Setting.vue";
import Payroll from "./Payroll.vue";
import Login from "./Login.vue";

const compList = [
  Update,
  Contact,
  Passport,
  Emirates,
  Visa,
  Bank,
  Document,
  Qualification,
  Setting,
  Payroll,
  Login,
];
export default {
  props: {
    // item: {
    //   type: String,
    // },
    employeeId: {
      type: String,
    },
  },
  data() {
    return {
      dialog: false,
      compList,
      tabMenu: [
        {
          text: "Profile",
          icon: "mdi-account-box",
          value: "#0",
        },
        {
          text: "Contact",
          icon: "mdi-phone",
          value: "#1",
        },
        {
          text: "Passport",
          icon: "mdi-file-powerpoint-outline",
          value: "#2",
        },
        {
          text: "Emirates",
          icon: "mdi-city-variant",
          value: "#3",
        },
        {
          text: "Visa",
          icon: "mdi-file-document-multiple",
          value: "#4",
        },
        {
          text: "Bank",
          icon: "mdi-bank",
          value: "#5",
        },
        {
          text: "Documents",
          icon: "mdi-file",
          value: "#6",
        },
        {
          text: "Qualification",
          icon: "mdi-account-box",
          value: "#7",
        },
        {
          text: "Setting",
          icon: "mdi-phone",
          value: "#8",
        },
        {
          text: "Payroll",
          icon: "mdi-briefcase",
          value: "#9",
        },
        {
          text: "Login",
          icon: "mdi-lock",
          value: "#10",
        },
      ],
      tab: "0",
    };
  },
  methods: {
    getComponent() {
      return this.compList[this.tab];
    },
  },
};
</script>

<style scoped>
/* Add any custom styling here */
</style>
