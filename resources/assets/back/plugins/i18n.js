import Vue from 'vue'
import VueI18n from 'vue-i18n'

import en from '@bo/lang/en/general.js'
import ru from '@bo/lang/ru/general.js'

Vue.use(VueI18n)

export default new VueI18n({
	locale: window.settings.lang,
	fallbackLocale: 'en',
	messages: {
		en,
		ru,
	},
	missing(locale, value) {
		return value.replace(/.+\./, '')
	},
})