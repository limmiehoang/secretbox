<template>
  <div class="home">
    <Sidebar/>
    <div class="page-content">
      <Navbar :profile="profile"/>
      <GroupDetail/>
    </div>
  </div>
</template>

<script>
import Vue from "vue";
import Sidebar from "@/components/Sidebar.vue";
import Navbar from "@/components/Navbar.vue";
import GroupDetail from "@/components/GroupDetail.vue";

export default {
  name: "home",
  components: {
    Sidebar,
    Navbar,
    GroupDetail
  },
  data() {
    return {
      profile: this.$auth.profile,
      files: [],
      messages: []
    };
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