<template>
  <v-text-field
    v-model="finalTime"
    outlined
    dense
    :label="label"
    type="time"
    hide-details
    @input="setInput"
  ></v-text-field>
</template>

<script>
export default {
  props: ["label", "default_value"],
  data() {
    return {
      finalTime: null,
    };
  },
  created() {
    this.finalTime = this.default_value;
    this.$emit("getTime", this.default_value);
  },
  methods: {
    setInput() {
      this.$emit("getTime", this.finalTime);
    },
  },
};
</script>
