import Vue from 'vue'
import Vuetify from 'vuetify/lib'
import ru from 'vuetify/es5/locale/ru'
import 'material-design-icons-iconfont/dist/material-design-icons.css'

Vue.use(Vuetify)

const opts = {
	lang: {
		locales: {ru},
		current: window.settings.lang,
	},
	icons: {
		iconfont: 'md'
	},
	theme: {
		themes: {
			light: {
				primary: '#1976D2',
				secondary: '#424242',
				accent: '#82B1FF',
				error: '#FF5252',
				info: '#2196F3',
				success: '#4CAF50',
				warning: '#FFC107',
			}
		}
	}
}

export default new Vuetify(opts)
