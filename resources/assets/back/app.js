window.Vue = require('vue')

// Axious
window.axios = require('axios')
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
let token = document.head.querySelector('meta[name="csrf-token"]')
if (token) window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content

import store from './store.js'
import router from './router.js'
import vuetify from './plugins/vuetify'
import App from './App.vue'
import i18n from './plugins/i18n'
import * as Sentry from '@sentry/browser'
import {Vue as SentryVue, ExtraErrorData as SentryExtraErrorData} from '@sentry/integrations'

let sentryOpt = {
	Vue,
	attachProps: true,
	logErrors: process.env.NODE_ENV === 'development',
}

Sentry.init({
	dsn: process.env.MIX_SENTRY_DSN,
	integrations: [
		new SentryExtraErrorData(),
		new SentryVue(sentryOpt)
	],
	environment: process.env.NODE_ENV,
})

Vue.config.devtools = false
Vue.config.productionTip = false

new Vue({
	router,
	store,
	vuetify,
	i18n,
	render: h => h(App)
}).$mount('#app')
