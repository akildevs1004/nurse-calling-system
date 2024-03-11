<template>
  <div style="width:150px;">
    <v-text-field
      @input="serachAll($event)"
      style="height: 30px; margin-top: 5px"
      clearable
      :hide-details="true"
      outlined
      dense
      autocomplete="off"
      label="Search"
    ></v-text-field>
  </div>
</template>

<script>
export default {
  props: {
    endpoint: {
      type: String,
    },
  },
  data() {
    return {};
  },
  methods: {
    serachAll(e) {
      if ((e && e.length == 0) || e == null) {
        this.$emit("default");
        return;
      } else if (e.length <= 3) {
        return false;
      }

      this.$axios
        .get(`${this.endpoint}/search/${e}`, {
          params: {
            company_id: this.$auth.user.company_id,
          },
        })
        .then(({ data }) => {
          this.$emit("search", data);
        })
        .catch(({ err }) => {
          this.$emit("search", []);
        });
    },
  },
};
</script>

<style scoped>
/* Add any custom styling here */
</style>
