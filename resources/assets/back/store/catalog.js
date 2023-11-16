import router from '@bo/router'
import i18n from '@bo/plugins/i18n'

// Начальное состояние
const state = {
	sectionsFilter: {
		selected: undefined,
		search: '',
		activeState: false,
	},
	sections: [],

	productsFilter: {
		sort: 'order',
		sortDirect: 'asc',
		onlyActive: false,
		perPage: 100,
		page: 1,
	},
	products: [],
	productsPager: {},

	section: {},
	product: {},
	certs: [],

	giftsFilter: {
		selected: -1,
		search: '',
		activeState: false,
	},
	gifts: [],
	searchProductsResults: undefined,

	productPriceHistoty: undefined,
	productRemains: undefined,
}

// Синхронные изменения состояний
const mutations = {
	SET_SECTIONS(state, sections) {
		state.sections = sections
	},
	
	RESET_SECTION(state) {
		state.section = {}
	},
	
	SET_SECTION(state, section) {
		state.section = section
	},
	
	UPDATE_SECTION(state, section) {
		state.section = section
		Vue.set(
			state.sections,
			state.sections.findIndex(v => v.id === section.id),
			section
		)
	},
	
	ADD_SECTION(state, section) {
		if (state.sections.length) state.sections.push(section)
	},
	
	DELETE_SECTION(state, id) {
		state.sections.splice(state.sections.findIndex(v => v.id === id), 1)
	},
	
	SET_PRODUCTS(state, products) {
		state.productsPager = products.pager
		state.products = products.products
	},

	SET_SEARCH_PRODUCTS_RESULTS(state, products) {
		state.searchProductsResults = products
	},

	ADD_PRODUCTS(state, products) {
		state.productsPager = products.pager
		state.products = [...state.products, ...products.products]
	},
	
	RESET_PRODUCT(state) {
		state.product = {}
	},
	
	SET_PRODUCT(state, product) {
		state.product = product
	},
	
	ADD_PRODUCT(state, product) {
		state.product = product
		if (state.products.length) state.products.push(product)
	},
	
	UPDATE_PRODUCT(state, product) {
		state.product = product
		Vue.set(
			state.products,
			state.products.findIndex(v => v.id === product.id),
			product
		)
	},
	
	DELETE_PRODUCT(state, id) {
		state.products.splice(state.products.findIndex(v => v.id === id), 1)
	},
	
	SET_CERTS(state, certs) {
		state.certs = certs
	},
	
	RESET_CERTS(state) {
		state.certs = []
	},

	SET_GIFTS(state, gifts) {
		state.gifts = gifts
	},
	
	ADD_GIFT(state, gift) {
		if (state.gifts.length) state.gifts.push(gift)
	},
	
	UPDATE_GIFT(state, gift) {
		Vue.set(
			state.gifts,
			state.gifts.findIndex(v => v.id === gift.id),
			gift
		)
	},
	
	DELETE_GIFT(state, id) {
		state.gifts.splice(state.gifts.findIndex(v => v.id === id), 1)
	},

	SET_PRODUCT_PRICE_HISTORY(state, history) {
		state.productPriceHistoty = history
	},

	SET_PRODUCT_REMAINS(state, remains) {
		state.productRemains = remains
	}
	
}

// Асинхронные изменения
const actions = {
	getSections({ commit, state, dispatch }, force) {
		if (!force && state.sections.length) return false
		axios({
			method: 'get',
			url: '/api/v1/admin/groups',
		})
		.then(r => r.data.result).then(r => {
			commit('SET_SECTIONS', r)
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
	},
	
	getSection({ commit, state, dispatch }, {force, id}) {
		if (id === undefined) {
			commit('RESET_SECTION')
			return false
		}
		if (!force && Number(state.section.id) === Number(id)) {
			return false
		}
		
		commit('RESET_SECTION')
		axios({
			method: 'get',
			url: '/api/v1/admin/groups/' + id.toString(),
		})
		.then(r => r.data.result).then(r => {
			commit('SET_SECTION', r)
		})
		.catch(errors => {
			router.currentRoute.name === 'Catalog' || router.replace({name: 'Catalog'})
			dispatch('general/showErrors', {errors}, {root: true})
		})
	},
	
	createSection({ commit, dispatch }, data) {
		axios({
			method: 'post',
			url: '/api/v1/admin/groups',
			data: data,
		})
		.then(r => r.data.result).then(r => {
			commit('ADD_SECTION', r)
			router.currentRoute.name === 'Catalog' || router.push({name: 'Catalog'})
			dispatch('general/setAlert', {text: i18n.tc('alerts.sectionCreated')}, {root: true})
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
	},
	
	updateSection({ commit, dispatch }, {id, data}) {
		axios({
			method: 'post',
			url: '/api/v1/admin/groups/' + id.toString(),
			data: data,
		})
		.then(r => r.data.result).then(r => {
			commit('UPDATE_SECTION', r)
			router.currentRoute.name === 'Catalog' || router.push({name: 'Catalog'})
			dispatch('general/setAlert', {text: i18n.tc('alerts.sectionUpdated')}, {root: true})
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
	},
	
	deleteSection({ commit, dispatch }, id) {
		axios({
			method: 'delete',
			url: '/api/v1/admin/groups/' + id.toString(),
		})
		.then(r => r.data.result).then(r => {
			commit('DELETE_SECTION', id)
			router.currentRoute.name === 'Catalog' || router.push({name: 'Catalog'})
			dispatch('general/setAlert', {text: i18n.tc('alerts.sectionDeleted')}, {root: true})
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
	},
	
	getProducts({ commit, state, dispatch }, {force = false, add = false, then = ()=>{}, } = {}) {
		if (!force && !add && state.products.length) return false
		let filter = JSON.parse(JSON.stringify(state.productsFilter))
		if (filter.onlyActive === false) filter.onlyActive = undefined
		axios({
			method: 'get',
			url: '/api/v1/admin/products',
			params: {
				...filter,
				name: state.productsFilter.name || undefined,
				groupId: state.sectionsFilter.selected,
			},
		})
		.then(r => r.data.result).then(r => {
			add ? commit('ADD_PRODUCTS', r) : commit('SET_PRODUCTS', r)
			then()
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
	},

	searchProducts({ commit, dispatch }, {q, add = false, then = ()=>{}, } = {}) {
		commit('SET_SEARCH_PRODUCTS_RESULTS', undefined)
		if (!q || !q.length) return
		axios({
			method: 'get',
			url: '/api/v1/admin/products',
			params: {
				sort: 'order',
				sortDirect: 'asc',
				perPage: 100,
				page: 1,
				name: q,
			},
		})
		.then(r => r.data.result).then(r => {
			commit('SET_SEARCH_PRODUCTS_RESULTS', r.products)
			then()
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
	},
	
	getProduct({ commit, state, dispatch }, {force, id}) {
		if (id === undefined) {
			commit('RESET_PRODUCT')
			return false
		}
		if (!force && state.product.id && Number(state.product.id) === Number(id)) {
			return false
		}
		
		commit('RESET_PRODUCT')
		axios({
			method: 'get',
			url: '/api/v1/admin/products/' + id.toString(),
		})
		.then(r => r.data.result).then(r => {
			commit('SET_PRODUCT', r)
		})
		.catch(errors => {
			router.currentRoute.name === 'Catalog' || router.replace({name: 'Catalog'})
			dispatch('general/showErrors', {errors}, {root: true})
		})
	},
	
	createProduct({ commit, dispatch }, {data, newCerts}) {
		axios({
			method: 'post',
			url: '/api/v1/admin/products',
			data: data,
		})
		.then(r => r.data.result).then(r => {
			const id = r.id
			commit('ADD_PRODUCT', r)

			if (newCerts.length) {
				dispatch('addCerts', {id, newCerts})
				.then(()=> {
					router.currentRoute.name === 'Catalog' || router.push({name: 'Catalog'})
				})
				.catch(()=> {
					router.replace({name: 'CatalogProductEdit', params: {id}})
				})
				.finally(()=> {
					dispatch('general/setAlert', {text: i18n.tc('alerts.productCreated')}, {root: true})
				})

			} else {
				router.currentRoute.name === 'Catalog' || router.push({name: 'Catalog'})
				dispatch('general/setAlert', {text: i18n.tc('alerts.productCreated')}, {root: true})
			}
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
	},
	
	updateProduct({ commit, dispatch }, {id, data, oldCerts, newCerts}) {
		function updateFinal() {
			commit('RESET_CERTS')
			router.currentRoute.name === 'Catalog' || router.push({name: 'Catalog'})
		}
		function addCerts() {
			dispatch('addCerts', {id, newCerts})
			.then(updateFinal)
			.catch(()=>{})
		}
		
		axios({
			method: 'post',
			url: '/api/v1/admin/products/' + id.toString(),
			data: data,
		})
		.then(r => r.data.result).then(r => {
			commit('UPDATE_PRODUCT', r)
			if (oldCerts.length) {
				dispatch('deleteCerts', {id, oldCerts,
					then: newCerts.length ? addCerts() : updateFinal(),
				})
			} else if (newCerts.length) {
				addCerts()
			} else {
				updateFinal()
			}
			dispatch('general/setAlert', {text: i18n.tc('alerts.productUpdated')}, {root: true})
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
	},
	
	deleteProduct({ commit, dispatch }, {id, oldCerts}) {
		function deleteProduct() {
			axios({
				method: 'delete',
				url: '/api/v1/admin/products/' + id.toString(),
			})
			.then(r => {
				commit('DELETE_PRODUCT', id)
				commit('RESET_PRODUCT')
				commit('RESET_CERTS')
				router.currentRoute.name === 'Catalog' || router.push({name: 'Catalog'})
				dispatch('general/setAlert', {text: i18n.tc('alerts.productUpdated')}, {root: true})
			})
			.catch(errors => {
				dispatch('general/showErrors', {errors}, {root: true})
			})
		}

		function deleteProductWithCerts(oldCerts) {
			if (oldCerts.length) {
				dispatch('deleteCerts', {id, oldCerts, then: deleteProduct})
			} else {
				deleteProduct()
			}
		}

		if (oldCerts === undefined) {
			dispatch('getCerts', {id, force: true, then: deleteProductWithCerts})
		} else deleteProductWithCerts(oldCerts)
		
	},
	
	getCerts({ commit, state, dispatch }, {id, force, then}) {
		if (id === undefined) {
			commit('RESET_CERTS')
			return false
		}
		if (!force && state.certs.length && Number(state.product.id) === Number(id)) {
			return false
		}

		commit('RESET_CERTS')
		axios({
			method: 'get',
			url: '/api/v1/admin/images/prodCertificates/' + id.toString(),
		})
		.then(r => r.data.result).then(r => {
			commit('SET_CERTS', r)
			if (then !== undefined) then(r)
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
	},
	
	addCerts({ commit, dispatch }, {id, newCerts}) {
		return new Promise((resolve, reject) => {
			let data = new FormData()
			newCerts.forEach(e => {
				data.append('images[]', e.file)
			})
			axios({
				method: 'post',
				url: '/api/v1/admin/images/prodCertificates/' + id.toString() + '/upload',
				data,
			})
			.then(r => r.data.result).then(r => {
				commit('SET_CERTS', r)
				resolve()
			})
			.catch(errors => {
				dispatch('general/setAlert', {
					type: 'error',
					text: i18n.tc('alerts.certsUpdateError'),
				}, {root: true})
				dispatch('general/showErrors', {errors}, {root: true})
				reject()
			})
		})
	},
	
	deleteCerts({ dispatch, commit }, {id, oldCerts, then}) {
		let data = new URLSearchParams()
			oldCerts.forEach(e => {
				data.append('ids[]', e.id)
			})
			axios({
				method: 'delete',
				url: '/api/v1/admin/images/prodCertificates/' + id.toString() + '/destroy',
				data,
			})
			.then(then)
			.catch(errors => {
				dispatch('general/setAlert', {
					type: 'error',
					text: i18n.tc('alerts.certsUpdateError'),
				}, {root: true})
				dispatch('general/showErrors', {errors}, {root: true})
			})
	},
	
	updateCerts({ commit, dispatch }, {id, data}) {
		axios({
			method: 'post',
			url: '/api/v1/admin/images/prodCertificates/' + id.toString() + '/update',
			data,
		})
		.then(r => r.data.result).then(r => {
			commit('SET_CERTS', r)
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
	},

	getGifts({ commit, state, dispatch }, force) {
		if (!force && state.gifts.length) return false
		axios({
			method: 'get',
			url: '/api/v1/admin/gifts',
		})
		.then(r => r.data.result).then(r => {
			commit('SET_GIFTS', r)
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
	},

	addGift({ commit, dispatch }, {data, then = ()=>{}, }) {
		axios({
			method: 'post',
			url: '/api/v1/admin/gifts',
			data: data,
		})
		.then(r => r.data.result).then(r => {
			commit('ADD_GIFT', r)
			dispatch('general/setAlert', {text: i18n.tc('alerts.giftAdded')}, {root: true})
			then()
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
	},

	updateGift({ commit, dispatch }, {id, data}) {
		axios({
			method: 'post',
			url: '/api/v1/admin/gifts/' + id.toString(),
			data: data,
		})
		.then(r => r.data.result).then(r => {
			commit('UPDATE_GIFT', r)
			dispatch('general/setAlert', {text: i18n.tc('alerts.giftUpdated')}, {root: true})
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
	},

	deleteGift({ commit, dispatch }, id) {
		axios({
			method: 'delete',
			url: '/api/v1/admin/gifts/' + id.toString(),
		})
		.then(r => r.data.result).then(r => {
			commit('DELETE_GIFT', id)
			dispatch('general/setAlert', {text: i18n.tc('alerts.giftDeleted')}, {root: true})
		})
		.catch(errors => {
			dispatch('general/showErrors', {errors}, {root: true})
		})
	},

	getProductPriceHistory({ commit, dispatch }, id) {
		commit('SET_PRODUCT_PRICE_HISTORY')
		axios({
			method: 'get',
			url: '/api/v1/store-operations/product/' + id,
		})
		.then(r => r.data.result).then(r => {
			commit('SET_PRODUCT_PRICE_HISTORY', r)
		})
		.catch(errors => {
			dispatch('general/showErrors', { errors }, { root: true })
		})
	},

	getProductRemains({ commit, dispatch }, id) {
		commit('SET_PRODUCT_REMAINS')
		axios({
			method: 'get',
			url: '/api/v1/store-operations/product-residue/' + id,
		})
		.then(r => r.data.result).then(r => {
			commit('SET_PRODUCT_REMAINS', r)
		})
		.catch(errors => {
			dispatch('general/showErrors', { errors }, { root: true })
		})
	}
	
}

export default {
	namespaced: true,
	state,
	actions,
	mutations,
}
