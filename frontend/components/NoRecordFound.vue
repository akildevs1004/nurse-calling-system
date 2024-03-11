<template>
  <div v-if="data && !data.length" class="error--text text-center mt-15">
    No record found
  </div>
</template>
<script>
export default {
  props: ["data"],
};
</script>
