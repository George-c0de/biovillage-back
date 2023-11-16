import router from '@bo/router'
import i18n from '@bo/plugins/i18n'
import Vue from 'vue'

function today() {
	return new Date().toISOString().substr(0, 10).split('-').reverse().join('.')
}

function emptyOrder() { return {
	info: {},
	items: [],
	gifts: [],
	payments: [],
}}

function defaultOrdersFilter() { return {
	// количество должно совпадать с элементом из списка в параметрах футера
	perPage: 10,
	page: 1,
	sort: 'updatedAt',
	sortDirect: 'desc',
}}

function defaultPackOrdersFilter() { return {
	dtDelivery: today(),
	status: 'placed',
}}

function defaultDeliveryOrdersFilter() { return {
	dtDelivery: today(),
	status: 'packed',
}}

function defaultPurchaseOrdersFilter() { return {
	date: today(),
	orderStatus: 'placed',
}}

// Начальное состояние
const state = {
	ordersFilter: defaultOrdersFilter(),
	orders: [],
	ordersPager: {},
	ordersForce: false,

	packOrdersFilter: defaultPackOrdersFilter(),
	packOrders: [],
	packOrdersPager: {},
	packOrdersForce: false,

	deliveryOrdersFilter: defaultDeliveryOrdersFilter(),
	deliveryOrders: [],
	deliveryOrdersPager: {},
	deliveryOrdersDisplayType: 0,
	deliveryOrdersForce: false,

	orderDetails: emptyOrder(),
	orderDetailsLoaded: false,
	orderDetailsTab: 0,
	orderItem: {},
	
	purchaseOrdersFilter: defaultPurchaseOrdersFilter(),
	purchaseOrders: {
		products: [],
		gifts: [],
	},
	purchaseOrdersForce: false,
	
}

// Синхронные изменения состояний
const mutations = {
	FORCE_ALL_ORDERS(state) {
		state.ordersForce = true
		state.packOrdersForce = true
		state.deliveryOrdersForce = true
	},

	SET_FALSE(state, payload) {
		state[payload] = false
	},

	RESET_ORDER_DETAILS(state) {
		state.orderDetailsLoaded = false
		state.orderDetails = emptyOrder()
	},
	
	SET_ORDER_DETAILS(state, orderDetails) {
		state.orderDetails = orderDetails
		state.orderDetailsLoaded = true
	},

	SET_ORDERS(state, orders) {
		state.orders = orders.orders
		state.ordersPager = orders.pager
	},

	RESET_ORDERS_FILTER(state) {
		state.ordersFilter = defaultOrdersFilter()
	},

	SET_PACK_ORDERS(state, orders) {
		state.packOrders = orders.orders
		state.packOrdersPager = orders.pager
	},

	RESET_PACK_ORDERS_FILTER(state) {
		state.packOrdersFilter = defaultPackOrdersFilter()
	},

	SET_DELIVERY_ORDERS(state, orders) {
		state.deliveryOrders = orders.orders
		state.deliveryOrdersPager = orders.pager
	},

	RESET_DELIVERY_ORDERS_FILTER(state) {
		state.deliveryOrdersFilter = defaultDeliveryOrdersFilter()
	},

	SET_PURCHASE_ORDERS(state, orders) {
		state.purchaseOrders = orders
	},

	RESET_PURCHASE_ORDERS_FILTER(state) {
		state.purchaseOrdersFilter = defaultPurchaseOrdersFilter()
	},

	UPDATE_ORDER_INFO(state, info) {
		Vue.set(
			state.orderDetails,
			'info',
			info
		)
	},

	RESET_ORDER_ITEM(state) {
		state.orderItem = {}
	},

	SET_ORDER_ITEM(state, {item, isProduct}) {
		item.isProduct = isProduct
		state.orderItem = item

		if (state.orderDetails.info.id === item.orderId) {
			const type = isProduct ? 'items' : 'gifts'
			Vue.set(
				state.orderDetails[type],
				state.orderDetails[type].findIndex(el => el.id === item.id),
				item
			)
		}
	},

	SET_ORDER_ITEMS(state, {items, gifts}) {
		Vue.set(
			state.orderDetails,
			'items',
			items
		)
		Vue.set(
			state.orderDetails,
			'gifts',
			gifts
		)
	},
	
}

// Геттеры
const getters = {}

// Асинхронные изменения
const actions = {
	getOrders({ commit, state, dispatch }, { force = false, params, start = ()=>{}, finish = ()=>{}, } = {}) {
		if (!force && state.orders.length && !state.ordersForce) return false
		
		start()
		axios({
			method: 'get',
			url: '/api/v1/admin/orders/',
			params,
		})
		.then(r => r.data.result).then(r => {
			commit('SET_ORDERS', r)
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
		.finally(()=> {
			commit('SET_FALSE', 'ordersForce')
			finish()
		})
	},

	getPackOrders({ commit, state, dispatch }, { force = false, params, start = ()=>{}, finish = ()=>{}, } = {}) {
		if (!force && state.packOrders.length && !state.packOrdersForce) return false
		start()

		axios({
			method: 'get',
			url: '/api/v1/admin/orders/',
			params,
		})
		.then(r => r.data.result).then(r => {
			commit('SET_PACK_ORDERS', r)
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
		.finally(()=> {
			commit('SET_FALSE', 'packOrdersForce')
			finish()
		})
	},

	getDeliveryOrders({ commit, state, dispatch }, { force = false, params, start = ()=>{}, finish = ()=>{}, } = {}) {
		if (!force && state.deliveryOrders.length && !state.deliveryOrdersForce) return false
		start()

		axios({
			method: 'get',
			url: '/api/v1/admin/orders/',
			params,
		})
		.then(r => r.data.result).then(r => {
			commit('SET_DELIVERY_ORDERS', r)
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
		.finally(()=> {
			commit('SET_FALSE', 'deliveryOrdersForce')
			finish()
		})
	},

	getOrderDetails({ commit, state, dispatch }, {force = false, id}) {
		if (id === undefined) {
			commit('RESET_ORDER_DETAILS')
			return false
		}
		if (!force && state.orderDetails.info && Number(state.orderDetails.info.id) === id) {
			return false
		}
		
		commit('RESET_ORDER_DETAILS')
		axios({
			method: 'get',
			url: '/api/v1/admin/orders/' + id.toString(),
		})
		.then(r => r.data.result).then(r => {
			commit('SET_ORDER_DETAILS', r)
		})
		.catch(errors => {
			router.currentRoute.name === 'Orders' || router.replace({name: 'Orders'})
			dispatch('general/showErrors', {errors}, {root: true})
		})
	},

	getPurchaseOrders({ commit, state, dispatch }, { force = false, params, start = ()=>{}, finish = ()=>{}, } = {}) {
		if (!force && state.purchaseOrders.length && !state.purchaseOrdersForce) return false
		
		start()
		axios({
			method: 'get',
			url: '/api/v1/admin/purchaser-boy/',
			params,
		})
		.then(r => r.data.result).then(r => {
			commit('SET_PURCHASE_ORDERS', r)
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
		.finally(()=> {
			commit('SET_FALSE', 'purchaseOrdersForce')
			finish()
		})
	},

	getPurchaseShortageProducts({ commit, dispatch }, { force = false, params, start = ()=>{}, finish = ()=>{}, } = {}) {
		if (!force && state.purchaseOrders.length && !state.purchaseOrdersForce) return false

		start()
		axios({
			method: 'get',
			url: '/api/v1/store-operations/purchaser-shortage',
			params,
		})
		.then(r => r.data.result).then(r => {
			commit('SET_PURCHASE_ORDERS', r)
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
		.finally(()=> {
			commit('SET_FALSE', 'purchaseOrdersForce')
			finish()
		})
	},

	updateOrderInfo({ commit, dispatch }, { data, id, then = ()=>{}, finish = ()=>{}, }) {
		axios({
			method: 'post',
			url: '/api/v1/admin/orders/' + id.toString(),
			data,
		})
		.then(r => r.data.result).then(r => {
			commit('FORCE_ALL_ORDERS')
			commit('UPDATE_ORDER_INFO', r)
			dispatch('general/setAlert', {text: i18n.tc('alerts.orderUpdated')}, {root: true})
			then()
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
		.finally(()=> {
			finish()
		})
	},

	cancelOrder({ commit, dispatch }, {id, then = ()=>{}, finish = ()=>{}, }) {
		axios({
			method: 'post',
			url: '/api/v1/admin/orders/' + id.toString() + '/cancel',
		})
		.then(r => r.data.result).then(r => {
			commit('FORCE_ALL_ORDERS')
			commit('SET_ORDER_DETAILS', r)
			dispatch('general/setAlert', {text: i18n.tc('alerts.orderCanceled')}, {root: true})
			then()
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
		.finally(()=> {
			finish()
		})
	},

	packOrder({ commit, dispatch }, { data, id, then = () => { }, finish = () => { }, error = () => {}, }) {
		axios({
			method: 'post',
			url: '/api/v1/admin/packer-boy/pack/' + id.toString(),
			data,
		})
		.then(r => r.data.result).then(r => {
			dispatch('general/setAlert', {text: i18n.tc('alerts.orderPacked')}, {root: true})
			commit('RESET_ORDER_ITEM', r)
			commit('RESET_ORDER_DETAILS', r)
			then()
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
			if (errors.response.data && errors.response.data.errors) error(errors.response.data.errors)
			finish()
		})
	},

	unpackOrder({ commit, dispatch }, {id, then = ()=>{}, finish = ()=>{}, }) {
		axios({
			method: 'post',
			url: '/api/v1/admin/packer-boy/unpack/' + id.toString(),
		})
		.then(r => r.data.result).then(r => {
			dispatch('general/setAlert', {text: i18n.tc('alerts.orderUnpacked')}, {root: true})
			commit('RESET_ORDER_ITEM', r)
			commit('RESET_ORDER_DETAILS', r)
			then()
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
			finish()
		})
	},

	completeOrder({ commit, dispatch }, {id, then = ()=>{}, finish = ()=>{}, }) {
		axios({
			method: 'post',
			url: '/api/v1/admin/orders/' + id.toString() + '/complete',
		})
		.then(r => r.data.result).then(r => {
			commit('FORCE_ALL_ORDERS')
			commit('SET_ORDER_DETAILS', r)
			dispatch('general/setAlert', {text: i18n.tc('alerts.orderCompleted')}, {root: true})
			then()
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
		.finally(()=> {
			finish()
		})
	},

	refundOrder({ commit, dispatch }, {id, then = ()=>{}, finish = ()=>{}, }) {
		axios({
			method: 'delete',
			url: '/api/v1/admin/orders/' + id.toString(),
		})
		.then(r => r.data.result).then(r => {
			commit('FORCE_ALL_ORDERS')
			commit('SET_ORDER_DETAILS', r)
			dispatch('general/setAlert', {text: i18n.tc('alerts.orderRefunded')}, {root: true})
			then()
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
		.finally(()=> {
			finish()
		})
	},

	getOrderItem({ commit, state, dispatch }, {id, isProduct, force = false, }) {
		if (id === undefined) {
			commit('RESET_ORDER_ITEM')
			return false
		}
		if (!force && Number(state.orderItem.id) === id && state.orderItem.isProduct === isProduct) {
			return false
		}
		
		commit('RESET_ORDER_ITEM')
		axios({
			method: 'get',
			url: '/api/v1/admin/orders/' + (isProduct ? 'items' : 'gifts') + '/' + id.toString(),
		})
		.then(r => r.data.result).then(r => {
			commit('SET_ORDER_ITEM', {item: r, isProduct})
		})
		.catch(errors => {
			router.currentRoute.name === 'Orders' || router.replace({name: 'Orders'})
			dispatch('general/showErrors', {errors}, {root: true})
		})
	},

	updateOrderItem({ commit, dispatch }, {id, isProduct, data, then = ()=>{}, }) {
		axios({
			method: 'post',
			url: '/api/v1/admin/orders/' + (isProduct ? 'items' : 'gifts') + '/' + id.toString(),
			data,
		})
		.then(r => r.data.result).then(r => {
			commit('SET_ORDER_ITEM', {item: r, isProduct})
			dispatch('general/setAlert', {text: i18n.tc('alerts.orderItemUpdated')}, {root: true})
			then()
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
	},

	cancelOrderItem({ dispatch }, { id, isProduct, then = ()=>{}, }) {
		axios({
			method: 'delete',
			url: '/api/v1/admin/orders/' + (isProduct ? 'items' : 'gifts') + '/' + id.toString(),
		})
		.then(r => r.data.result).then(r => {
			dispatch('general/setAlert', {text: i18n.tc('alerts.orderItemCanceled')}, {root: true})
			then()
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
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
