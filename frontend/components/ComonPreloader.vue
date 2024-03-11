<template>
  <div class="text-center" style="width: 25%; text-align: center">
    <v-img :src="`/preloaders/${icon}.gif`"></v-img>
  </div>
</template>
<script>
export default {
  props: ["icon", "height"],
};
</script>
