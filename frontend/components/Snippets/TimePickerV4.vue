<template>
    <v-dialog
      ref="menu"
      v-model="menu2"
      :return-value.sync="time"
      max-width="290px"
      min-width="290px"
    >
      <template v-slot:activator="{ on, attrs }">
        <v-text-field
        hide-details
          outlined
          dense
          v-model="time"
          :label="label"
          append-icon="mdi-clock-time-four-outline"
          readonly
          v-bind="attrs"
          v-on="on"
        ></v-text-field>
      </template>
      <v-time-picker
        v-if="menu2"
        v-model="time"
        full-width
        @click:minute="setInput"
      ></v-time-picker>
    </v-dialog>
  </template>
  <script>
  export default {
    props: ["label", "default_value"],
  
    data() {
      return {
        time: null,
        menu2: false,
      };
    },
    created() {
      this.time = this.default_value;
      this.$emit("getTime", this.default_value);
    },
    methods: {
      setInput() {
        this.$refs.menu.save(this.time);
        this.$emit("getTime", this.time);
      },
    },
  };
  </script>
  