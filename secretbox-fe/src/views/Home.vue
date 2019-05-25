<template>
  <div class="home">
    <Sidebar/>
    <div class="page-content">
      <Navbar :profile="profile"/>
      <GroupList/>
    </div>
  </div>
</template>

<script>
import Vue from "vue";
import Sidebar from "@/components/Sidebar.vue";
import Navbar from "@/components/Navbar.vue";
import GroupList from "@/components/GroupList.vue";

export default {
  name: "home",
  components: {
    Sidebar,
    Navbar,
    GroupList
  },
  data() {
    return {
      profile: this.$auth.profile
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