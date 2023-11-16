<template>
  <div class="page">
    <div class="page__header">
      header here...
    </div>
    <div class="page__main-content">
      <router-view></router-view>
    </div>
    <div class="page__footer">
      footer here...
    </div>
  </div>
</template>

<script>
  export default {}
</script>

<style lang="scss" scoped>
  //@import "../scss/_variables";

  .page{
    display: flex;
    flex-direction: column;
    height: 100%;
  }
  .page__header{
    flex: 0 0 auto;
  }
  .page__main-content{
    flex: 1 0 auto;
  }
  .page__footer{
    flex: 0 0 auto;
  }
</style>
