<template></template>
<script>
export default {
  layout: "login",
  created() {
    this.$axios.get(`/logout`).then(({ res }) => {
      this.$auth.logout();
    });

    this.$router.push(`/login`);
  },
};
</script>
