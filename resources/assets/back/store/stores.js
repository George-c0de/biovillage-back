import i18n from '@bo/plugins/i18n'
import router from '@bo/router'
import Vue from 'vue'

// Начальное состояние
const state = {
	stores: [],
	storesForce: false,
	storeDetails: undefined,
	storesPlaces: {},
	storesPlacesForce: false,
	idStoreHistory: undefined,
	idStoreProducts: undefined,
	storeHistory: undefined,
	storeHistoryPager: {},
	storeHistoryFilter: {
		page: 1,
		perPage: 10,
		sort: 'id',
		sortDirect: 'desc',
	},
	storeOperationDetails: undefined,
	storeProducts: [],
	storeProductsPager: {},
	storeProductsFilter: {
		page: 1,
		perPage: 10,
	}
}

// Синхронные изменения состояний
const mutations = {
	SET_STORES(state, stores) {
		state.stores = stores.stores
	},

	SET_FALSE(state, payload) {
		state[payload] = false
	},

	ADD_STORE(state, store) {
		state.stores.push(store)
	},

	UPDATE_STORE(state, store) {
		Vue.set(
			state.stores,
			state.stores.findIndex(v => v.id === store.id),
			store
		)
	},

	DELETE_STORE(state, id) {
		state.stores.splice(state.stores.findIndex(v => v.id === id), 1)
	},

	SET_STORE_DETAILS(state, store) {
		state.storeDetails = store
	},

	SET_STORES_PLACES(state, { storeId, places }) {
		Vue.set(state.storesPlaces, storeId, places)
	},

	ADD_STORE_PLACE(state, storePlace) {
		if (state.storesPlaces[storePlace.storeId]) state.storesPlaces[storePlace.storeId].push(storePlace)
	},

	UPDATE_STORE_PLACE(state, storePlace) {
		Vue.set(state.storesPlaces, storePlace.storeId, storePlace)
	},

	DELETE_STORE_PLACE(state, storeId) {
		Vue.delete(state.storesPlaces, storeId)
	},

	SET_STORE_HISTORY(state, { response, storeId }) {
		state.idStoreHistory = storeId
		state.storeHistory = response.operations
		state.storeHistoryPager = response.pager
	},

	ADD_STORE_HISTORY(state, historyItem) {
		let operation = historyItem.operation ? historyItem.operation : historyItem
		if (state.storeHistory) {
			state.storeHistory.unshift(operation)
			if (state.storeHistoryPager.total) state.storeHistoryPager.total++
			else state.storeHistoryPager.total = 1
		}
	},

	SET_STORE_OPERATION_DETAILS(state, details) {
		state.storeOperationDetails = details
	},

	SET_STORE_PRODUCTS(state, { response, storeId }) {
		state.idStoreProducts = storeId
		state.storeProducts = response.contents
		state.storeProductsPager = response.pager
	}

}

// Геттеры
const getters = {
	getStorePlacesByStoreId: state => id => {
		return state.storesPlaces[id]
	},
}

// Асинхронные изменения
const actions = {
	getStores({ commit, state, dispatch }, { force = false, start = ()=>{}, finish = ()=>{}, } = {}) {
		if (!force && state.stores.length && !state.storesForce) return false
		start()
		axios({
			method: 'get',
			url: '/api/v1/stores/'
		})
		.then(r => r.data.result).then(r => {
			commit('SET_STORES', r)
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
		.finally(()=> {
			commit('SET_FALSE', 'storesForce')
			finish()
		})
	},

	createStore({ commit, dispatch }, { newStore, then = () => { }, }) {
		return axios({
			method: 'post',
			url: '/api/v1/stores',
			data: newStore,
		})
		.then(r => r.data.result).then((r) => {
			commit('ADD_STORE', r)
			dispatch('general/setAlert', { text: i18n.tc('alerts.storeCreated') }, { root: true })
			then()
		})
		.catch((errors) => {
			dispatch('general/showErrors', { errors }, { root: true })
		})
	},

	updateStore({ commit, dispatch }, { store, then = () => { }, }) {
		return axios({
			method: 'post',
			url: '/api/v1/stores/' + store.id,
			data: store,
		})
		.then(r => r.data.result).then((r) => {
			commit('UPDATE_STORE', r)
			dispatch('general/setAlert', { text: i18n.tc('alerts.storeUpdated') }, { root: true })
			then()
		})
		.catch((errors) => {
			dispatch('general/showErrors', { errors }, { root: true })
		})
	},

	deleteStore({ commit, dispatch }, { id, then = ()=>{}, }) {
		axios({
			method: 'delete',
			url: '/api/v1/stores/' + id.toString(),
		})
		.then(r => r.data.result).then(r => {
			commit('DELETE_STORE', id)
			dispatch('general/setAlert', { text: i18n.tc('alerts.storeDeleted') }, { root: true })
			router.currentRoute.name === 'Stores' || router.push({ name: 'Stores' })
			then()
		})
		.catch(errors => {
			dispatch('general/showErrors', { errors }, { root: true })
		})
	},

	getStoreDetails({ commit, state, dispatch }, { force, id }) {
		if (!id) {
			commit('SET_STORE_DETAILS', undefined)
			return false
		}
		if (!force && state.storeDetails && Number(state.storeDetails.id) === Number(id)) return false
		commit('SET_STORE_DETAILS', undefined)
		axios({
			method: 'get',
			url: '/api/v1/stores/' + id.toString(),
		})
		.then(r => r.data.result).then(r => {
			commit('SET_STORE_DETAILS', r)
		})
		.catch(errors => {
			router.currentRoute.name === 'Stores' || router.replace({name: 'Stores'})
			dispatch('general/showErrors', { errors }, { root: true })
		})
	},

	getStoresPlaces({ commit, state, dispatch }, { storeId, force = false, start = ()=>{}, finish = ()=>{}, } = {}) {
		if (!force && state.storesPlaces[storeId]) return false
		start()
		axios({
			method: 'get',
			url: '/api/v1/store-places/' + storeId
		})
		.then(r => r.data.result).then(r => {
			commit('SET_STORES_PLACES', { storeId, places: r })
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
		.finally(()=> {
			finish()
		})
	},

	createStorePlace({ commit, dispatch }, { storePlace, storeId, then = () => { }, }) {
		storePlace.storeId = storeId
		return axios({
			method: 'post',
			url: '/api/v1/store-places',
			data: storePlace,
		})
		.then(r => r.data.result).then((r) => {
			commit('ADD_STORE_PLACE', r)
			dispatch('general/setAlert', { text: i18n.tc('alerts.storePlaceCreated') }, { root: true })
			then()
		})
		.catch((errors) => {
			dispatch('general/showErrors', { errors }, { root: true })
		})
	},

	updateStorePlace({ commit, dispatch }, { storePlace, then = () => { }, }) {
		return axios({
			method: 'post',
			url: '/api/v1/store-places/' + storePlace.id,
			data: storePlace,
		})
		.then(r => r.data.result).then((r) => {
			commit('UPDATE_STORE_PLACE', r)
			dispatch('general/setAlert', { text: i18n.tc('alerts.storePlaceUpdated') }, { root: true })
			then()
		})
		.catch((errors) => {
			dispatch('general/showErrors', { errors }, { root: true })
		})
	},

	deleteStorePlace({ commit, dispatch }, { id, then = ()=>{}, }) {
		axios({
			method: 'delete',
			url: '/api/v1/store-places/' + id.toString(),
		})
		.then(r => r.data.result).then(r => {
			commit('DELETE_STORE_PLACE', id)
			dispatch('general/setAlert', { text: i18n.tc('alerts.storePlaceDeleted') }, { root: true })
			then()
		})
		.catch(errors => {
			dispatch('general/showErrors', { errors }, { root: true })
		})
	},

	storePutOperation({ commit, dispatch }, { isGifts = false, formData, then = ()=>{}, }) {
		axios({
			method: 'post',
			url: isGifts ? '/api/v1/store-gift-operation/put' : '/api/v1/store-operation/put',
			data: formData,
		})
		.then(r => r.data.result).then(r => {
			commit('ADD_STORE_HISTORY', r)
			dispatch('general/setAlert', { text: i18n.tc('alerts.storePutCreated') }, { root: true })
			then()
		})
		.catch(errors => {
			dispatch('general/showErrors', { errors }, { root: true })
		})
	},

	storeTakeOperation({ commit, dispatch }, { isGifts = false, formData, then = ()=>{}, error = ()=>{} }) {
		axios({
			method: 'post',
			url: isGifts ? '/api/v1/store-gift-operation/take' : '/api/v1/store-operation/take',
			data: formData,
		})
		.then(r => r.data.result).then(r => {
			commit('ADD_STORE_HISTORY', r)
			dispatch('general/setAlert', { text: i18n.tc('alerts.storeTakeCreated') }, { root: true })
			then()
		})
		.catch(errors => {
			if (errors.response.data && errors.response.data.errors) error(errors.response.data.errors)
			dispatch('general/showErrors', { errors }, { root: true })
		})
	},

	storeCorrectionOperation({ commit, dispatch }, { isGifts = false, formData, then = ()=>{}, }) {
		axios({
			method: 'post',
			url: isGifts ? '/api/v1/store-gift-operation/correction' : '/api/v1/store-operation/correction',
			data: formData,
		})
		.then(r => r.data.result).then(r => {
			commit('SET_STORE_PRODUCTS', { response: { contents: undefined, pager: {} } })
			commit('ADD_STORE_HISTORY', r)
			dispatch('general/setAlert', { text: i18n.tc('alerts.storeCorrectionCreated') }, { root: true })
			then()
		})
		.catch(errors => {
			dispatch('general/showErrors', { errors }, { root: true })
		})
	},

	getStoreHistory({ commit, dispatch }, { storeId, force = false, params, start = ()=>{}, finish = ()=>{}, }) {
		if (!force && state.idStoreHistory == storeId) return false
		start()
		params.storeId = storeId
		axios({
			method: 'get',
			url: '/api/v1/store-operations',
			params,
		})
		.then(r => r.data.result).then(r => {
			commit('SET_STORE_HISTORY', { response: r, storeId })
		})
		.catch(errors => {
			dispatch('general/showErrors', { errors }, { root: true })
		})
		.finally(finish)
	},

	getStoreOperationDetails({ commit, dispatch }, { id, start = ()=>{}, finish = ()=>{}, }) {
		start()
		commit('SET_STORE_OPERATION_DETAILS', undefined)
		axios({
			method: 'get',
			url: '/api/v1/store-operations/' + id,
		})
		.then(r => r.data.result).then(r => {
			commit('SET_STORE_OPERATION_DETAILS', r)
		})
		.catch(errors => {
			dispatch('general/showErrors', { errors }, { root: true })
		})
		.finally(finish)
	},

	getStoreProducts({ commit, dispatch }, { id, force = false, params, start = ()=>{}, finish = ()=>{}, }) {
		if (!force && state.idStoreProducts == id) return false
		start()
		axios({
			method: 'get',
			url: '/api/v1/stores/' + id + '/contents',
			params,
		})
		.then(r => r.data.result).then(r => {
			commit('SET_STORE_PRODUCTS', { response: r, storeId: id })
		})
		.catch(errors => {
			dispatch('general/showErrors', { errors }, { root: true })
		})
		.finally(finish)
	},

}

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations
}
