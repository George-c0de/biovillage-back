import Vue from 'vue'
import Vuex from 'vuex'

import auth from './store/auth'
import general from './store/general'
import catalog from './store/catalog'
import orders from './store/orders'
import stores from './store/stores'

Vue.use(Vuex)

export default new Vuex.Store({
	modules: {
		auth,
		general,
		catalog,
		orders,
		stores,
	}
})
