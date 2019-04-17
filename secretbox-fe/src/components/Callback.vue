<template>
  <div>
    <p>Loading...</p>
  </div>
</template>

<script>
export default {
  created() {
    this.$auth.handleAuthentication();
  }
};
</script>
