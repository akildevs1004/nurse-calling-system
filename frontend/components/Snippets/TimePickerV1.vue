<template>
  <v-menu v-model="menu" offset-y :close-on-content-click="false">
    <template v-slot:activator="{ on, attrs }">
      <v-text-field
        v-model="finalTime"
        append-icon="mdi-clock-time-four-outline"
        outlined
        dense
        readonly
        hide-details
        v-bind="attrs"
        v-on="on"
        :label="label"
      ></v-text-field>
    </template>

    <v-row
      witdh
      align="center"
      no-gutters
      class="pa-2 mx-auto"
      justify="center"
    >
      <v-col>
        <v-list
          class="text-center"
          outlined
          dense
          height="200"
          style="overflow: scroll"
        >
          <div class="mx-auto">
            <b>Hours </b>
          </div>

          <v-list-item-group color="primary">
            <v-list-item v-for="(hour, i) in hours" :key="i">
              <v-list-item-content>
                <v-list-item-title @click="setHour(hour)">{{
                  hour
                }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-col>

      <v-col>
        <v-list
          class="text-center"
          outlined
          dense
          height="200"
          style="overflow: scroll"
        >
          <div class="mx-auto">
            <b>Minutes </b>
          </div>

          <v-list-item-group color="primary">
            <v-list-item v-for="(minute, i) in minutes" :key="i">
              <v-list-item-content>
                <v-list-item-title @click="setMinute(minute)">{{
                  minute
                }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-col>

      <v-col>
        <v-list
          class="text-center"
          outlined
          dense
          height="200"
          style="overflow: scroll"
        >
          <v-list-item-group color="primary">
            <v-list-item v-for="(period, index) in periods" :key="index">
              <v-list-item-content>
                <v-list-item-title @click="setPeriod(index)"
                  ><b>
                    {{ period }}
                  </b></v-list-item-title
                >
              </v-list-item-content>
            </v-list-item>

           

            <v-list-item>
              <v-list-item-content>
                <v-list-item-title><b>  </b></v-list-item-title>

              </v-list-item-content>
            </v-list-item>

            <v-btn large class="primary" @click="menu = false">Ok</v-btn>
          </v-list-item-group>
        </v-list>
      </v-col>
    </v-row>
  </v-menu>
</template>

<script>
export default {
  props: ["label", "default_value"],
  data() {
    return {
      menu:false,
      selectedHour: 1,
      selectedMinute: 0,
      selectedPeriod: null,
      hours: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], // Hours from 1 to 12
      minutes: Array.from({ length: 60 }, (_, i) => i), // Minutes from 0 to 59
      periods: ["AM", "PM"],
      finalTime: null,
    };
  },
  created() {
    this.finalTime = this.default_value;
    this.$emit("getTime", this.default_value);
  },
  methods: {
    setPeriod(v) {
      this.selectedHour =
        this.periods[v] == "PM"
          ? 12 + this.selectedHour
          : this.selectedHour - 12;

      this.setFinalTime();
    },

    setHour(v) {
      if (this.selectedPeriod && this.periods[v] == "PM") {
        this.selectedHour = 12 + v;
      } else {
        this.selectedHour = v;
      }

      this.setFinalTime();
    },

    setMinute(v) {
      this.selectedMinute = v;

      this.setFinalTime();
    },

    setFinalTime() {
      if (this.selectedHour > 23) {
        this.selectedHour = this.selectedHour - 12;
      } else if (this.selectedHour < 0) {
        this.selectedHour = this.selectedHour + 12;
      }
      let h = this.selectedHour;
      let m = this.selectedMinute;
      this.finalTime = `${h < 10 ? `0${h}` : h}:${m < 10 ? `0${m}` : m}`;
      this.$emit("getTime", this.finalTime);
    },
  },
};
</script>
