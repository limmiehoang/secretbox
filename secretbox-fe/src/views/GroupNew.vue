<template>
  <div class="home">
    <Sidebar/>
    <div class="page-content">
      <Navbar :profile="profile"/>
      <GroupCreate/>
    </div>
  </div>
</template>

<script>
import Vue from "vue";
import Sidebar from "@/components/Sidebar.vue";
import Navbar from "@/components/Navbar.vue";
import GroupCreate from "@/components/GroupCreate.vue";

export default {
  name: "group-new",
  components: {
    Sidebar,
    Navbar,
    GroupCreate
  },
  data() {
    return {
      profile: this.$auth.profile
    };
  },
  async created() {
    this.msg = null;
    const accessToken = await this.$auth.getAccessToken();
    Vue.http.headers.common["Authorization"] = `Bearer ${accessToken}`;
  },
  methods: {
    handleLoginEvent(data) {
      this.profile = data.profile;
    },
    logout() {
      this.$auth.logOut();
    }
  }
};
</script>

<style>
</style>