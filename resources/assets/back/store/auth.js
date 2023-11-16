import router from '../router'

// Начальное состояние
const state = {
	user: JSON.parse(localStorage.getItem('user')) || undefined,
	errors: {},
}

// Синхронные изменения состояний
const mutations = {
	SET_USER (state, user) {
		state.user = user
		if (!user) {
			localStorage.removeItem('user')
			delete window.axios.defaults.headers.common['Authorization'];
		} else {
			localStorage.setItem('user', JSON.stringify(user))
			// Сохраняем токен
			window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + user.token;
		}
	},

	SET_ERRORS (state, errors) {
		if (!errors) state.errors = {}
		else for (let key in errors) Vue.set(state.errors, key, errors[key])
	},

}

// Геттеры
const getters = {
	isAuth (state) {
		return !!(state.user && state.user.token)
	},

}

// Асинхронные изменения
const actions = {
	login ({ commit, dispatch }, { phone, password }) {
		axios({
			method: 'post',
			url: '/api/v1/admin/login',
			data: { phone, password },
		})
		.then(r => r.data.result).then(r => {
			commit('SET_USER', r)
			router.replace({name: 'Home'})
		})
		.catch(errors => {
			if (errors.response.data.errors)
			// commit('SET_ERRORS', errors.response.data.errors)
			dispatch('general/showErrors', {errors}, {root: true})
		})
	},

	logout ({ commit }) {
		commit('SET_USER', undefined)
		router.push({name: 'Login'})
	},

}

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations,
}
