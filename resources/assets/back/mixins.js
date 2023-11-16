// Роли администраторов

export const adminRoles = {
	data() { return {
		adminRoles: (()=> {
			let roles = [
				{
					value: 'superadmin',
					color: 'red lighten-3',
				},
				{
					value: 'admin',
					color: 'orange lighten-3',
				},
				{
					value: 'operator',
					color: 'yellow lighten-3',
				},
				{
					value: 'purchaser',
					color: 'green lighten-3',
				},
				{
					value: 'packer',
					color: 'brown lighten-3',
				},
				{
					value: 'delivery',
					color: 'blue-grey lighten-3',
				},
				{
					value: 'storekeeper',
					color: 'purple lighten-3'
				}
			]

			roles.forEach(role => role.text = this.$tc('adminRoles.' + role.value))

			return roles
		})(),
		
	}},

	methods: {
		sortAdminRoles(roles) {
			let sorted = []

			if (Array.isArray(roles) && roles.length) this.adminRoles.forEach(item => {
				let i = roles.indexOf(item.value)
				if (i > -1) sorted.push(roles[i])
			})
			return sorted
		},

		sortAdminRolesArrays(a, b) {
			return this.adminRoles.findIndex(role => role.value === a[0]) - this.adminRoles.findIndex(role => role.value === b[0])
		},

		checkRole(route) {
			if (!route || !route.meta.permissions) return true
			let roles = route.meta.permissions.roles
			if (!roles || !roles.length) return true
			let user = this.$store.state.auth.user
			if (!user || !user.roles || !user.roles.length) return false
			let allow = false;
			user.roles.forEach((userRole) => {
				if (roles.indexOf(userRole) > -1) {
					allow = true
					return false
				}
			})
			return allow
		},

	},

}

// Состояния заказа
export const orderStatuses = {
	data() { return {
		orderStatuses: (()=> {
			let statuses = [
				{
					value: 'new',
				},
				{
					value: 'placed',
				},
				{
					value: 'canceled',
				},
				{
					value: 'packed',
				},
				{
					value: 'delivery',
				},
				{
					value: 'finished',
				},
			]

			statuses.forEach(status => status.text = this.$tc('orderStatuses.' + status.value))

			return statuses
		})(),

	}},

}

// Оплаты
export const paymentValues = {
	data() { return {
		paymentStatuses: (()=> {
			let statuses = [
				{
					value: 'waiting',
				},
				{
					value: 'done',
				},
				{
					value: 'cancel',
				},
			]

			statuses.forEach(status => status.text = this.$tc('paymentStatuses.' + status.value))

			return statuses
		})(),

		transactionTypes: (()=> {
			let types = [
				{
					value: 'pay',
				},
				{
					value: 'refund',
				},
			]

			types.forEach(type => type.text = this.$tc('transactionTypes.' + type.value))

			return types
		})(),

		paymentMethods: (()=> {
			let methods = [
				{
					value: 'cash',
				},
				{
					value: 'card',
				},
				{
					value: 'ccard',
				},
				{
					value: 'gpay',
				},
				{
					value: 'apay',
				},
				{
					value: 'bonus',
				},
	
			]

			methods.forEach(method => method.text = this.$tc('paymentMethods.' + method.value))

			return methods
		})(),

	}},

}


// Платформы
export const clientPlatforms = {
	data() { return {
		clientPlatforms: ['iOS', 'Android', 'Site', 'na'],

	}},

}


// Параметры фильтра запроса в формате для таблицы
export const filterOptions = {
	computed: {
		filterOptions: {
			get() {
				let f = this.filter
				return {
					page: f.page,
					itemsPerPage: f.perPage,
					sortBy: [f.sort],
					sortDesc: [f.sortDirect === 'desc' ? true : false],
				}
			},

			set(v) {
				let f = this.filter
				f.page = v.page
				f.perPage = v.itemsPerPage,
				f.sort = v.sortBy[0],
				f.sortDirect = v.sortDesc[0] ? 'desc' : 'asc'
			},
		},

	},

}


// Форматы времени
export const timeFormats = {
	data() { return {
		daysOfWeek: (()=> {
			let days = []

			for (let i = 1; i <= 7; i++) {
				days.push({
					value: i,
					text: this.$t('daysOfWeek[' + (i - 1).toString() + ']'),
				})
			}

			return days
		})(),

	}},

	methods: {
		timeToString(v) {
			if (v == null) return ''
			return (v < 10 ? '0' : '') + v.toString()
		},
		
		timeToInt(v) {
			let time = v.split(':')
			return {
				hour: parseInt(time[0]),
				minute: parseInt(time[1]),
			}
		},

		deliveryIntervalToString(di) {
			return (di.dayOfWeek != null ? this.daysOfWeek[di.dayOfWeek - 1].text : '') + ' — '
				+ this.$tc('dateStarts') + ' ' + this.timeToString(di.startHour)
				+ ':' + this.timeToString(di.startMinute)
				+ ' ' + this.$tc('dateEnds') + ' ' + this.timeToString(di.endHour)
				+ ':' + this.timeToString(di.endMinute)
		},

		deliveryIntervalPropertiesToString(item) {
			if (item.deliveryIntervalId) {
				return this.deliveryIntervalToString({
					dayOfWeek: item.diDayOfWeek,
					startHour: item.diStartHour,
					startMinute: item.diStartMinute,
					endHour: item.diEndHour,
					endMinute: item.diEndMinute,
				}) 
			} else return ''
		},

	},

}


// Интервалы доставки текстом и для селекта
export const deliveryIntervals = {
	mixins: [timeFormats],

	computed: {
		deliveryIntervals() {
			let intervals = []
			
			this.$store.state.general.deliveryIntervals.forEach(di => {
				if (di.active) intervals.push({
					value: di.id,
					text: this.deliveryIntervalToString(di)
				})
			})
			return intervals
		},

	},

	created() {
		this.$store.dispatch('general/getDeliveryIntervals')
	},

}



// Формат порядков числа
export const numberByThousands = {
	methods: {
		numberByThousands(v) {
			if (v == null) return ''
			return v.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '\u00A0')
		}
		
	},

}


// Подсчет итоговой суммы
export const totalSum = {
	mixins: [numberByThousands],

	methods: {
		totalSum(list, sum, factor = 1) {
			if (!list || !list.length) return 0
			return list.map(item => item[sum]).reduce((a, b) => a + Number(b * factor), 0).toString()
		},


		totalSumText() {
			let sum = Math.round(parseFloat(this.totalSum(...arguments)) * 100) / 100
			return this.numberByThousands(sum)
		},
		
	},

}


// Подсчет итоговой суммы
export const autoRefresh = {
	data() { return {
		autoRefreshInterval: null,
		
	}},

	methods: {
		autoRefresh(f) {
			this.autoRefreshInterval = setInterval(() => {
				f()
			}, 60000)
		},

	},

	beforeDestroy() {
		clearInterval(this.autoRefreshInterval)

	},

}


// Конвертация ед-ц измерения
export const formatUnits = {
	methods: {
		unitsToBase(amount, factor) {
			return Math.round(parseFloat(amount * factor) * 100) / 100
		},

		unitsToDer(amount, factor) {
			return Math.round(parseFloat(amount / factor) * 100) / 100
		},

		autoFormatUnits({ item, q, qField = 'realUnits' }) {
			let units = q != undefined ? q : item[qField]
			if (!item || isNaN(units) || item.unitShortName == undefined || item.unitShortDerName == undefined) return 'ERROR'
			if (Math.abs(units) >= item.unitFactor) {
				return this.numberByThousands(this.unitsToDer(units, item.unitFactor)) + ' ' + item.unitShortDerName
			} else {
				return this.numberByThousands(units) + ' ' + item.unitShortName
			}
		}
	}
}