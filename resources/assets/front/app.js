window.Vue = require('vue')

// Axious
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
let token = document.head.querySelector('meta[name="csrf-token"]')
if (token) window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content

import store from './store.js'
import router from './router.js'
import App from './App.vue'

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
