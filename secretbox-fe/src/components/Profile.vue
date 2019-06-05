<template>
  <div id="profile">
    <div class="main-content">
      <div class="presentation">
        <div class="presentation-breadcrumb">
          <h5>Profile</h5>
        </div>
        <div>
          <div>
            <img :src="profile.picture">
          </div>
        </div>
        <div>
          <h2>{{ profile.name }}</h2>
          <p>{{ profile.email }}</p>
        </div>
        <div>
          <pre>{{ JSON.stringify(profile, null, 2) }}</pre>
        </div>
      </div>
      <div class="info-banner">
        
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    profile: {
      type: Object
    }
  }
}
</script>

<style>

</style>
