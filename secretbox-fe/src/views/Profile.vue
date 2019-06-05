<template>
  <div class="home">
    <Sidebar/>
    <div class="page-content">
      <Navbar :profile="profile"/>
      <Profile :profile="profile"/>
    </div>
  </div>
</template>


<script>
import Sidebar from "@/components/Sidebar.vue";
import Navbar from "@/components/Navbar.vue";
import Profile from "@/components/Profile.vue";

export default {
  components: {
    Sidebar,
    Navbar,
    Profile
  },
  data() {
    return {
      profile: this.$auth.profile
    };
  },
  methods: {
    handleLoginEvent(data) {
      this.profile = data.profile;
    }
  }
}
</script>
