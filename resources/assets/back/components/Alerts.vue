<template>
  <div class="alerts" :class="{'no-app-bar': !isAuth}">
    <v-alert
      v-for="(alert, i) in alerts"
      :key="i"
      v-show="alert.show"
      :type="alert.type"
      :dismissible="alert.dismissible"
      transition="scale-transition"
    >
      {{ alert.text }}
    </v-alert>
  </div>
</template>

<script>
export default {

  computed: {
    alerts() {
      return this.$store.state.general.alerts
    },

    isAuth() {
      return this.$store.getters['auth/isAuth']
    },

  },

}
</script>

<style lang="scss" scoped>

.alerts {
  position: fixed;
  z-index: 10000;
  right: 20px;
  top: 80px;
  width: calc(100% - 40px);
  max-width: 500px;
  pointer-events: none;

  .v-alert {
    pointer-events: auto;
  }

  &.no-app-bar {
    top: 20px;
  }
}

</style>
