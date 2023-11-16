import router from '@bo/router'
import i18n from '@bo/plugins/i18n'

function defaultClientsFilter() { return {
	// количество должно совпадать с элементом из списка в параметрах футера
	page: 1,
	perPage: 10,
	sort: 'lastLoginAt',
	sortDirect: 'desc',
}}

// Начальное состояние
const state = {
	alerts: {},
	alertsCounter: 0,

	settings: window.settings,

	slider: [],

	units: [],

	tagsFilter: {
		search: '',
		activeState: false,
	},
	tags: [],

	adminListFilter: {
		search: '',
	},
	adminList: [],
	adminAccount: {},

	clientsFilter: defaultClientsFilter(),
	clients: [],
	clientsPager: {},
	clientAccount: {},

	deliveryIntervalsFilter: {
		dayOfWeek: null,
		activeState: false,
	},
	deliveryIntervals: [],

	deliveryAreas: [],
	
}

// Синхронные изменения состояний
const mutations = {
	SET_ALERT(state, alert) {
		let i = ++state.alertsCounter
		Vue.set(state.alerts, i, alert)

		// Удаляем уведомление по таймауту
		if (!alert.time) return false
		setTimeout(() => Vue.set(state.alerts[i], 'show', false), alert.time)
	},

	SET_SETTINGS(state, settings) {
		state.settings = settings
	},

	SET_SLIDER(state, slider) {
		state.slider = slider
	},

	UPDATE_SLIDE(state, slide) {
		// Обновляем измененный слайд
		Vue.set(
			state.slider,
			state.slider.findIndex(v => v.id === slide.id),
			slide
		)
	},

	ADD_SLIDE(state, slide) {
		state.slider.push(slide)
	},

	DELETE_SLIDE(state, id) {
		state.slider.splice(state.slider.findIndex(v => v.id === id), 1)
	},

	SET_UNITS(state, units) {
		state.units = units
	},

	DELETE_UNIT(state, id) {
		state.units.splice(state.units.findIndex(v => v.id === id), 1)
	},

	ADD_UNIT(state, unit) {
		state.units.push(unit)
	},

	UPDATE_UNIT(state, unit) {
		// Обновляем измененную единицу
		Vue.set(
			state.units,
			state.units.findIndex(v => v.id === unit.id),
			unit
		)
	},

	SET_TAGS(state, tags) {
		state.tags = tags
	},

	DELETE_TAG(state, id) {
		state.tags.splice(state.tags.findIndex(v => v.id === id), 1)
	},

	ADD_TAG(state, tag) {
		state.tags.push(tag)
	},

	UPDATE_TAG(state, tag) {
		Vue.set(
			state.tags,
			state.tags.findIndex(v => v.id === tag.id),
			tag
		)
	},
	
	SET_ADMIN_LIST(state, adminList) {
		state.adminList = adminList
	},

	SET_ADMIN_ACCOUNT(state, adminAccount) {
		state.adminAccount = adminAccount
	},

	RESET_ADMIN_ACCOUNT(state) {
		state.adminAccount = {}
	},

	ADD_ADMIN_ACCOUNT(state, admin) {
		if (state.adminList.length) state.adminList.push(admin)
	},

	UPDATE_ADMIN_ACCOUNT(state, admin) {
		Vue.set(
			state.adminList,
			state.adminList.findIndex(v => v.id === admin.id),
			admin
		)
	},

	DELETE_ADMIN_ACCOUNT(state, id) {
		state.adminList.splice(state.adminList.findIndex(v => v.id === id), 1)
	},

	SET_CLIENTS(state, clients) {
		state.clients = clients.clients
		state.clientsPager = clients.pager
	},

	RESET_CLIENTS_FILTER(state) {
		state.clientsFilter = defaultClientsFilter()
	},

	SET_CLIENT_ACCOUNT(state, clientAccount) {
		state.clientAccount = clientAccount
	},
	
	SET_DELIVERY_INTERVALS(state, di) {
		state.deliveryIntervals = di
	},

	DELETE_DELIVERY_INTERVAL(state, id) {
		state.deliveryIntervals.splice(state.deliveryIntervals.findIndex(v => v.id === id), 1)
	},

	ADD_DELIVERY_INTERVAL(state, di) {
		if (state.deliveryIntervals.length) state.deliveryIntervals.push(di)
	},

	UPDATE_DELIVERY_INTERVAL(state, di) {
		Vue.set(
			state.deliveryIntervals,
			state.deliveryIntervals.findIndex(v => v.id === di.id),
			di
		)
	},
	
	SET_DELIVERY_AREAS(state, da) {
		state.deliveryAreas = da
	},

	UPDATE_DELIVERY_AREAS(state, da) {
		if (state.deliveryAreas.length) {
			state.deliveryAreas.forEach(area => {
				let found = da.find(e => {
					return area.name === e.name
				})

				if (found) {
					const newColor = found.color
					for (const key in area) {
						if (key !== 'id' && area.hasOwnProperty(key)) {
							found[key] = area[key]
						}
					}
					if (newColor !== '#000000') found.color = newColor
				}
			})
		}

		state.deliveryAreas = da
	},
	
	UPDATE_DELIVERY_AREA(state, da) {
		Vue.set(
			state.deliveryAreas,
			state.deliveryAreas.findIndex(v => v.id === da.id),
			da
		)
	},
	
}

// Геттеры
const getters = {
	getUnitById: state => id => {
		if (!state.units || !id) return
		return state.units.find((item) => item.id == id);
	},
}

// Асинхронные изменения
const actions = {
	setAlert ({ commit }, { type = 'success', text, time = 5000, dismissible = true}) {
		if (!text) return false
		commit('SET_ALERT', { type, text, time, dismissible, show: true })
	},
	
	showErrors({ dispatch }, { errors }) {
		if (errors.response === undefined) {
			console.log(errors)
			return undefined
		}

		const status = errors.response.status

		// Ошибки авторизации:
		if (status === 401) {
			dispatch('setAlert', {
				type: 'error',
				text: i18n.tc('alerts.authError'),
			})
			dispatch('auth/logout', null, {root: true})
			return false
		} else if (status === 500) {
			dispatch('setAlert', {
				type: 'error',
				// разработка || продакшн || нет сообщения
				text: errors.response.data?.errors?.message || errors.response.data?.errors?.error || i18n.tc('alerts.someError'),
			})
			return false
		} else if (status !== 400) {
			dispatch('setAlert', {
				type: 'error',
				text: i18n.tc('alerts.errorWithStatus') + status.toString(),
			})
			return false
		}
		
		// Рекурсивный обход ошибок
		function searchErrors(error) {
			let errors = []
			if (typeof error === 'string') {
				// dispatch('setAlert', {
				// 	type: 'error',
				// 	text: error,
				// })
				errors.push(error)
			} else if (error instanceof Object) {
				for (let key in error) {
					errors = errors.concat(searchErrors(error[key]))
				}
			}
			return errors
		}
		
		const messages = errors.response.data.errors
		if (messages.trance) {		
			if (messages.message) dispatch('setAlert', {
				type: 'error',
				text: messages.message ? messages.message : i18n.tc('alerts.someError'),
			})
		} else {
			dispatch('setAlert', {
				type: 'error',
				text: searchErrors(messages)[0],
			})
		}
		return true
	},

	getAdminList({ commit, state, dispatch }, { force = false, start = ()=>{}, finish = ()=>{}, } = {}) {
		if (!force && state.adminList.length) return false
		
		start()
		axios({
			method: 'get',
			url: '/api/v1/admin/admins',
		})
		.then(r => r.data.result).then(r => {
			commit('SET_ADMIN_LIST', r)
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
		.finally(finish)
	},

	getAdminAccount({ commit, state, dispatch }, { force, id }) {
		if (id === undefined) {
			commit('RESET_ADMIN_ACCOUNT')
			return false
		}

		if (!force && Number(state.adminAccount.id) === Number(id)) return false

		axios({
			method: 'get',
			url: '/api/v1/admin/admins/' + id.toString(),
		})
		.then(r => r.data.result).then(r => {
			commit('SET_ADMIN_ACCOUNT', r)
		})
		.catch(errors => {
			router.currentRoute.name === 'AdminList' || router.replace({name: 'AdminList'})
			dispatch('showErrors', {errors})
		})
	},

	createAdmin({ commit, dispatch }, { data, then = ()=>{}, finish = ()=>{}, }) {
		axios({
			method: 'post',
			url: '/api/v1/admin/admins',
			data: data,
		})
		.then(r => r.data.result).then(r => {
			router.currentRoute.name === 'AdminList' || router.push({name: 'AdminList'})
			commit('ADD_ADMIN_ACCOUNT', r)
			dispatch('setAlert', {text: i18n.tc('alerts.adminCreated')})
			then()
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
		.finally(finish)
	},

	editAdmin({ commit, dispatch }, { id, data, then = ()=>{}, finish = ()=>{}, }) {
		axios({
			method: 'post',
			url: '/api/v1/admin/admins/' + id.toString(),
			data: data,
		})
		.then(r => r.data.result).then(r => {
			router.currentRoute.name === 'AdminList' || router.push({name: 'AdminList'})
			commit('UPDATE_ADMIN_ACCOUNT', r)
			dispatch('setAlert', {text: i18n.tc('alerts.adminEdited')})
			then()
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
		.finally(finish)
	},

	deleteAdmin({ commit, dispatch }, { id, then = ()=>{}, }) {
		axios({
			method: 'delete',
			url: '/api/v1/admin/admins/' + id.toString(),
		})
		.then(r => r.data.result).then(r => {
			router.currentRoute.name === 'AdminList' || router.push({name: 'AdminList'})
			commit('DELETE_ADMIN_ACCOUNT', id)
			dispatch('setAlert', {text: i18n.tc('alerts.adminDeleted')})
			then()
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},

	getSettings({ commit, state, dispatch }, force) {
		if (!force) return false
		axios({
			method: 'get',
			url: '/api/v1/admin/settings',
		})
		.then(r => r.data.result).then(r => {
			commit('SET_SETTINGS', r)
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},

	updateSettings({ commit, dispatch }, settings) {
		axios({
			method: 'post',
			url: '/api/v1/admin/settings',
			data: settings,
		})
		.then(r => r.data.result).then(r => {
			commit('SET_SETTINGS', r)
			dispatch('setAlert', {text: i18n.tc('alerts.settingsSaved')})
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},

	getSlider({ commit, dispatch }, force) {
		if (!force && state.slider.length) return false
		axios({
			method: 'get',
			url: '/api/v1/admin/slider',
		})
		.then(r => r.data.result).then(r => {
			commit('SET_SLIDER', r)
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},

	addSlide({ commit, dispatch }, { data, then = ()=>{}, }) {
		axios({
			method: 'post',
			url: '/api/v1/admin/slider',
			data: data,
		})
		.then(r => r.data.result).then(r => {
			commit('ADD_SLIDE', r)
			dispatch('setAlert', {text: i18n.tc('alerts.slideAdded')})
			then()
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},

	updateSlide({ commit, dispatch }, { id, data }) {
		axios({
			method: 'post',
			url: '/api/v1/admin/slider/' + id.toString(),
			data: data,
		})
		.then(r => r.data.result).then(r => {
			commit('UPDATE_SLIDE', r)
			dispatch('setAlert', {text: i18n.tc('alerts.slideUpdated')})
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},

	deleteSlide({ commit, dispatch }, id) {
		axios({
			method: 'delete',
			url: '/api/v1/admin/slider/' + id.toString(),
		})
		.then(r => r.data.result).then(r => {
			commit('DELETE_SLIDE', id)
			dispatch('setAlert', {text: i18n.tc('alerts.slideDeleted')})
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},

	getUnits({ commit, dispatch }, { force = false, start = ()=>{}, finish = ()=>{}, } = {}) {
		if (!force && state.units.length) return false

		start()
		axios({
			method: 'get',
			url: '/api/v1/admin/units',
		})
		.then(r => r.data.result).then(r => {
			commit('SET_UNITS', r)
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
		.finally(finish)
	},

	addUnit({ commit, dispatch }, { data, then = ()=>{}, }) {
		axios({
			method: 'post',
			url: '/api/v1/admin/units',
			data: data,
		})
		.then(r => r.data.result).then(r => {
			commit('ADD_UNIT', r)
			dispatch('setAlert', {text: i18n.tc('alerts.unitAdded')})
			then()
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},

	updateUnit({ commit, dispatch }, { data, then = ()=>{}, }) {
		axios({
			method: 'post',
			url: '/api/v1/admin/units/' + data.id.toString(),
			data: data,
		})
		.then(r => r.data.result).then(r => {
			commit('UPDATE_UNIT', r)
			dispatch('setAlert', {text: i18n.tc('alerts.unitEdited')})
			then()
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},

	deleteUnit({ commit, dispatch }, { id, then = ()=>{}, }) {
		axios({
			method: 'delete',
			url: '/api/v1/admin/units/' + id.toString(),
		})
		.then(r => r.data.result).then(r => {
			commit('DELETE_UNIT', id)
			dispatch('setAlert', {text: i18n.tc('alerts.unitDeleted')})
			then()
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},

	getTags({ commit, dispatch }, { force = false, start = ()=>{}, finish = ()=>{}, } = {}) {
		if (!force && state.tags.length) return false

		start()
		axios({
			method: 'get',
			url: '/api/v1/admin/tags',
		})
		.then(r => r.data.result).then(r => {
			commit('SET_TAGS', r)
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
		.finally(finish)
	},

	addTag({ commit, dispatch }, { data, then = ()=>{}, }) {
		axios({
			method: 'post',
			url: '/api/v1/admin/tags',
			data: data,
		})
		.then(r => r.data.result).then(r => {
			commit('ADD_TAG', r)
			dispatch('setAlert', {text: i18n.tc('alerts.tagAdded')})
			then()
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},

	updateTag({ commit, dispatch }, {data, id}) {
		axios({
			method: 'post',
			url: '/api/v1/admin/tags/' + id.toString(),
			data: data,
		})
		.then(r => r.data.result).then(r => {
			commit('UPDATE_TAG', r)
			dispatch('setAlert', {text: i18n.tc('alerts.tagEdited')})
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},

	deleteTag({ commit, dispatch }, id) {
		axios({
			method: 'delete',
			url: '/api/v1/admin/tags/' + id.toString(),
		})
		.then(r => r.data.result).then(r => {
			commit('DELETE_TAG', id)
			dispatch('setAlert', {text: i18n.tc('alerts.tagDeleted')})
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},
	
	getClients({ commit, dispatch }, { force = false, params, start = ()=>{}, finish = ()=>{}, }) {
		if (!force && state.clients.length) return false
		
		start()
		axios({
			method: 'get',
			url: '/api/v1/admin/clients',
			params,
		})
		.then(r => r.data.result).then(r => {
			commit('SET_CLIENTS', r)
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
		.finally(finish)
	},

	getClientAccount({ commit, state, dispatch }, { force, id }) {
		if (!force && Number(state.clientAccount.id) === Number(id)) return false

		axios({
			method: 'get',
			url: '/api/v1/admin/clients/' + id.toString(),
		})
		.then(r => r.data.result).then(r => {
			commit('SET_CLIENT_ACCOUNT', r)
		})
		.catch(errors => {
			router.currentRoute.name === 'Clients' || router.replace({name: 'Clients'})
			dispatch('showErrors', {errors})
		})
	},

	updateClientInfo({ commit, state, dispatch }, {id, data, }) {
		axios({
			method: 'post',
			url: '/api/v1/admin/clients/' + id.toString(),
			data,
		})
		.then(r => r.data.result).then(r => {
			commit('SET_CLIENT_ACCOUNT', r)
			dispatch('setAlert', {text: i18n.tc('alerts.clientInfoUpdated')})
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},

	updateClientBonuses({ commit, state, dispatch }, {id, data, }) {
		axios({
			method: 'post',
			url: '/api/v1/admin/clients/' + id.toString() + '/bonuses',
			data,
		})
		.then(r => r.data.result).then(r => {
			commit('SET_CLIENT_ACCOUNT', r)
			dispatch('setAlert', {text: i18n.tc('alerts.clientBonusesUpdated')})
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},
	
	getDeliveryIntervals({ commit, dispatch }, { force = false, start = ()=>{}, finish = ()=>{}, } = {}) {
		if (!force && state.deliveryIntervals.length) return false
		
		start()
		axios({
			method: 'get',
			url: '/api/v1/admin/di',
		})
		.then(r => r.data.result).then(r => {
			commit('SET_DELIVERY_INTERVALS', r)
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
		.finally(finish)
	},

	addDeliveryInterval({ commit, dispatch }, { data, then = ()=>{}, }) {
		axios({
			method: 'post',
			url: '/api/v1/admin/di',
			data: data,
		})
		.then(r => r.data.result).then(r => {
			commit('ADD_DELIVERY_INTERVAL', r)
			dispatch('setAlert', {text: i18n.tc('alerts.deliveryIntervalAdded')})
			then()
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},

	updateDeliveryInterval({ commit, dispatch }, { data, then = ()=>{}, }) {
		axios({
			method: 'post',
			url: '/api/v1/admin/di/' + data.id.toString(),
			data: data,
		})
		.then(r => r.data.result).then(r => {
			commit('UPDATE_DELIVERY_INTERVAL', r)
			dispatch('setAlert', {text: i18n.tc('alerts.deliveryIntervalUpdated')})
			then()
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},

	deleteDeliveryInterval({ commit, dispatch }, {id, then}) {
		axios({
			method: 'delete',
			url: '/api/v1/admin/di/' + id.toString(),
		})
		.then(r => r.data.result).then(r => {
			commit('DELETE_DELIVERY_INTERVAL', id)
			dispatch('setAlert', {text: i18n.tc('alerts.deliveryIntervalDeleted')})
			then()
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},
	
	parseKml({ commit, dispatch }, data) {
		return new Promise((reslove, reject) => {
			axios({
				method: 'post',
				url: '/api/v1/admin/da/loadKml',
				data: data,
			})
			.then(r => r.data.result).then(r => {
				commit('UPDATE_DELIVERY_AREAS', r)
				dispatch('setAlert', {text: i18n.tc('alerts.mapUploaded')})
				reslove()
			})
			.catch(errors => {
				dispatch('showErrors', {errors})
			})
		})
	},
	
	getDeliveryAreas({ commit, dispatch }, force) {
		if (!force && state.deliveryAreas.length) return false
		
		axios({
			method: 'get',
			url: '/api/v1/admin/da',
		})
		.then(r => r.data.result).then(r => {
			commit('SET_DELIVERY_AREAS', r)
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},
	
	saveDeliveryAreas({ commit, dispatch }, { data, then = ()=>{}, }) {
		axios({
			method: 'post',
			url: '/api/v1/admin/da',
			data: data,
		})
		.then(r => r.data.result).then(r => {
			commit('SET_DELIVERY_AREAS', r)
			dispatch('setAlert', {text: i18n.tc('alerts.deliveryAreasUpdated')})
			then()
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},
	
	updateDeliveryArea({ commit, dispatch }, {data, id}) {
		axios({
			method: 'post',
			url: '/api/v1/admin/da/' + id.toString(),
			data: data,
		})
		.then(r => r.data.result).then(r => {
			commit('UPDATE_DELIVERY_AREA', r)
			dispatch('setAlert', {text: i18n.tc('alerts.deliveryAreaUpdated')})
		})
		.catch(errors => {
			dispatch('showErrors', {errors})
		})
	},
	
}

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations
}
