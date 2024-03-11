<template>
  <div>
    <v-text-field
      v-if="header.key == column || header.type == 'text'"
      clearable
      :hide-details="true"
      v-model="header.search_value"
      @input="handleChange"
      outlined
      dense
      autocomplete="off"
    ></v-text-field>
  </div>
</template>

<script>
export default {
  props: ["header", "column"],
  date: () => ({
    text: "",
  }),
  methods: {
    handleChange() {
      this.$emit("entered-value", this.header);
    },
  },
};
</script>
