<template>
  <div>
    <v-select
      v-if="header.key == column || header.type == 'dropdown'"
      :hide-details="true"
      v-model="header.search_value"
      @input="handleChange"
      outlined
      dense
      item-text="name"
      item-value="id"
      :items="[{ id: ``, name: `All` }, ...items]"
      :placeholder="header.text"
      solo
      flat
      :disabled="disabled"
    ></v-select>
  </div>
</template>

<script>
export default {
  props: ["header", "column", "items", "disabled"],
  methods: {
    handleChange() {
      this.$emit("entered-value", this.header);
    },
  },
};
</script>
