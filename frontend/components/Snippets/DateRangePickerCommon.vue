<template>
  <v-menu
    ref="menu"
    v-model="menu"
    :close-on-content-click="false"
    transition="scale-transition"
    offset-y
    min-width="auto"
  >
    <template v-slot:activator="{ on }">
      <v-text-field
        :hide-details="true"
        outlined
        dense
        v-on="on"
        v-model="datesLabel"
        readonly
        placeholder="Select Date Range"
      ></v-text-field>
    </template>

    <div class="text-center" style="padding: 0">
      <v-date-picker
        @input="firstPicker"
        persistant
        no-title
        color="primary"
        v-model="startDates"
        range
        scrollable
      >
      </v-date-picker>

      <v-date-picker
        @input="secondPicker"
        no-title
        color="primary"
        v-model="endDates"
        range
        :min="min"
      >
      </v-date-picker>
      <v-card class="text-right pa-2">
        <v-btn dark color="background" x-small @click="clearDates">
          clear
        </v-btn>

        <v-btn dark color="background" x-small @click="menu = false">
          close
        </v-btn>

        <v-btn
          dark
          color="background"
          x-small
          @click="$refs.menu.save(secondPicker)"
        >
          OK
        </v-btn>
      </v-card>
    </div>
  </v-menu>
</template>

<script>
let currentFullDate = new Date(
  Date.now() - new Date().getTimezoneOffset() * 60000
);

let nextFullDate = new Date(currentFullDate);
nextFullDate.setMonth(nextFullDate.getMonth() + 1);
let currentDate = currentFullDate.toISOString().slice(0, 10);
let nextDate = nextFullDate.toISOString().slice(0, 10);
export default {
  props: ["column", "disabled"],

  data: () => ({
    menu: false,
    menu2: false,

    showPicker: true,

    startDates: [currentDate],
    endDates: [nextDate],
    finalDates: [],
    min: nextDate,
  }),
  computed: {
    datesLabel() {
      return this.finalDates.join(" ~ ");
    },
  },
  methods: {
    firstPicker() {
      this.startDates.sort((a, b) => a.localeCompare(b));

      this.endDates = [];
      this.finalDates = this.startDates;
      console.log("this.finalDates", this.finalDates);
      this.$emit("selected-dates", this.finalDates);
    },
    secondPicker() {
      this.endDates.sort((a, b) => a.localeCompare(b));

      let endDate = this.endDates.length
        ? this.endDates[this.endDates.length - 1]
        : this.startDates[this.startDates.length - 1];

      this.finalDates = [this.startDates[0], endDate];

      this.startDates = this.finalDates;
      this.endDates = this.finalDates;

      this.$emit("selected-dates", this.finalDates);

      this.min = this.startDates[0];
    },
    clearDates() {
      this.clearStartDates();
      this.clearEndDates();
    },
    clearStartDates() {
      this.finalDates = [];
      this.startDates = [];
    },
    clearEndDates() {
      this.finalDates = [];
      this.endDates = [];
    },
  },
};
</script>
