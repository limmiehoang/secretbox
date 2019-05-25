<template>
  <div id="group-detail">
    <div class="main-content">
      <div class="presentation">
        <div class="presentation-breadcrumb">
          <h5>Dashboard</h5>
        </div>
        <p>Invited Groups</p>
        <ul>
          <li v-for="group in newGroups" :key="group.id">
            {{ group.name }}
          </li>
        </ul>
        <p>Own Groups</p>
        <ul>
          <li v-for="group in joinedGroups" :key="group.id">
            {{ group.name }}
          </li>
        </ul>
      </div>
      <div class="info-banner">
        
      </div>
    </div>
  </div>
</template>

<script>
export default {
  computed: {
    joinedGroups () {
      return this.$store.state.joinedGroups
    },
    newGroups () {
      return this.$store.state.newGroups
    }
  }
}
</script>

<style>

</style>
