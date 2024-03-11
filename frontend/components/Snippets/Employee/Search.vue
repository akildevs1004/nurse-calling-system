<template>
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
</template>

<script>
export default {
  props: {
    endpoint: {
      type: String,
    },
    options: {
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

      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      this.$axios
        .get(`${this.endpoint}/search/${e}`, {
          params: {
            page,
            sortBy: sortBy ? sortBy[0] : "",
            sortDesc: sortDesc ? sortDesc[0] : "",
            per_page: itemsPerPage,
            company_id: this.$auth.user.company_id,
          },
        })
        .then(({ data }) => {
          this.data = data;
        })
        .catch(({ err }) => {
          this.data = [];
        });
    },
  },
};
</script>

<style scoped>
/* Add any custom styling here */
</style>
