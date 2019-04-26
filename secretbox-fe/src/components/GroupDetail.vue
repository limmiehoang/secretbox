<template>
  <div id="group-detail" class="main-content">
    <div class="presentation">
      <div class="presentation-breadcrumb">
      </div>
      <table class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Owner</th>
            <th>Group</th>
            <th>Created at</th>
            <th>Options</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="file in files" :key="file.id">
            <td>{{ `${file.name}.${file.extension}` }}</td>
            <td>{{ file.user_id }}</td>
            <td>{{ file.group_id }}</td>
            <td>{{ file.created_at }}</td>
            <td>
              <button class="btn btn-outline-dark btn-sm">...</button>
            </td>
          </tr>
          <tr>
            <td>Auth0.png</td>
            <td>me</td>
            <td>Con en</td>
            <td>5 mins ago</td>
            <td>
              <button class="btn btn-outline-dark btn-sm">...</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-show="msg">{{ msg }}</p>
    </div>
    <div class="info-banner">
      <button class="btn btn-primary btn-sm">Upload</button>
    </div>
  </div>
</template>

<script>
export default {

}
</script>

<style>

</style>
