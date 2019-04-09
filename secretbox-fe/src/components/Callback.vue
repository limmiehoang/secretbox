<template>
  <div>
    <p>Loading...</p>
  </div>
</template>

<script>
export default {
  methods: {
    handleLoginEvent(data) {
      console.log("handleLoginEvent in Callback");
      this.$router.push(data.state.target || "/");
    }
  },
  created() {
    this.$auth.handleAuthentication();
  }
};
</script>
