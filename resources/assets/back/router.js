import Vue from 'vue'
import Router from 'vue-router'
import NProgress from 'nprogress'
import store from './store'

import Login from './views/Login'
import Settings from './views/Settings'
import Catalog from './views/Catalog'
import Gifts from './views/Gifts'
import CatalogSection from './views/CatalogSection'
import CatalogProduct from './views/CatalogProduct'
import Slider from './views/Slider'
import Tags from './views/Tags'
import Units from './views/Units'
import AdminList from './views/AdminList'
import AdminAccount from './views/AdminAccount'
import Clients from './views/Clients'
import ClientAccount from './views/ClientAccount'
import DeliveryIntervals from './views/DeliveryIntervals'
import DeliveryAreas from './views/DeliveryAreas'
import Orders from './views/orders/Orders'
import OrderDetails from './views/orders/OrderDetails'
import PurchaseOrders from './views/orders/PurchaseOrders'
import PackOrders from './views/orders/PackOrders'
import PackOrderDetails from './views/orders/PackOrderDetails'
import DeliveryOrders from './views/orders/DeliveryOrders'
import DeliveryOrderDetails from './views/orders/DeliveryOrderDetails'
import OrderItem from './views/orders/OrderItem'
import Stores from './views/Stores'
import StoreDetails from './views/StoreDetails'
import StoreOperation from './views/StoreOperation'

Vue.use(Router)
// Определяем роуты
const router = new Router({
	mode: 'history',
	base: 'admin',
	routes: [
		{
			path: '/login//',
			name: 'Login',
			component: Login,
		},
		{
			path: '/',
			name: 'Home',
			redirect: {name: 'Catalog'},
		},
		{
			path: '/catalog//',
			name: 'Catalog',
			component: Catalog,
		},
		{
			path: '/gifts//',
			name: 'Gifts',
			component: Gifts,
			props: true,
		},
		{
			path: '/catalog/section/new//',
			name: 'CatalogSectionCreate',
			component: CatalogSection,
		},
		{
			path: '/catalog/section/:id//',
			name: 'CatalogSectionEdit',
			component: CatalogSection,
			props: true,
		},
		{
			path: '/catalog/product/new//',
			name: 'CatalogProductCreate',
			component: CatalogProduct,
		},
		{
			path: '/catalog/product/:id//',
			name: 'CatalogProductEdit',
			component: CatalogProduct,
			props: true,
		},
		{
			path: '/settings//',
			name: 'Settings',
			component: Settings,
		},
		{
			path: '/slider//',
			name: 'Slider',
			component: Slider,
		},
		{
			path: '/tags//',
			name: 'Tags',
			component: Tags,
		},
		{
			path: '/units//',
			name: 'Units',
			component: Units,
		},
		{
			path: '/administrators//',
			name: 'AdminList',
			component: AdminList,
		},
		{
			path: '/administrators/new//',
			name: 'AdminAccountNew',
			component: AdminAccount,
		},
		{
			path: '/administrators/:id//',
			name: 'AdminAccountEdit',
			component: AdminAccount,
			props: true,
		},
		{
			path: '/clients//',
			name: 'Clients',
			component: Clients,
		},
		{
			path: '/clients/:id//',
			name: 'ClientAccount',
			component: ClientAccount,
			props: true,
		},
		{
			path: '/delivery_intervals//',
			name: 'DeliveryIntervals',
			component: DeliveryIntervals,
		},
		{
			path: '/delivery_areas//',
			name: 'DeliveryAreas',
			component: DeliveryAreas,
		},
		{
			path: '/orders//',
			name: 'Orders',
			component: Orders,
		},
		{
			path: '/orders/items/:id//',
			name: 'OrderItemProduct',
			component: OrderItem,
			props(r) { return {...r.params, isProduct: true, }}
		},
		{
			path: '/orders/gifts/:id//',
			name: 'OrderItemGift',
			component: OrderItem,
			props(r) { return {...r.params, isProduct: false, }}
		},
		{
			path: '/orders/:id//',
			name: 'OrderDetails',
			component: OrderDetails,
			props: true,
		},
		{
			path: '/purchase_orders//',
			name: 'PurchaseOrders',
			component: PurchaseOrders,
		},
		{
			path: '/pack_orders//',
			name: 'PackOrders',
			component: PackOrders,
		},
		{
			path: '/pack_orders/:id//',
			name: 'PackOrderDetails',
			component: PackOrderDetails,
			props: true,
		},
		{
			path: '/delivery_orders//',
			name: 'DeliveryOrders',
			component: DeliveryOrders,
		},
		{
			path: '/delivery_orders/:id//',
			name: 'DeliveryOrderDetails',
			component: DeliveryOrderDetails,
			props: true,
		},
		{
			path: '/stores//',
			name: 'Stores',
			component: Stores,
			meta: {
				permissions: {
					roles: ['superadmin', 'admin', 'storekeeper']
				}
			}
		},
		{
			path: '/stores/:id//',
			name: 'StoreDetails',
			component: StoreDetails,
			props: true,
			meta: {
				permissions: {
					roles: ['superadmin', 'admin', 'storekeeper']
				}
			}
		},
		{
			path: '/stores/:storeId/operation/new-put/',
			name: 'StoreOperationNewPut',
			component: StoreOperation,
			props: true,
			meta: {
				permissions: {
					roles: ['superadmin', 'admin', 'storekeeper']
				}
			}
		},
		{
			path: '/stores/:storeId/operation/new-take/',
			name: 'StoreOperationNewTake',
			component: StoreOperation,
			props: true,
			meta: {
				permissions: {
					roles: ['superadmin', 'admin', 'storekeeper']
				}
			}
		},
		{
			path: '/stores/:storeId/operation/new-correction/',
			name: 'StoreOperationNewCorrection',
			component: StoreOperation,
			props: true,
			meta: {
				permissions: {
					roles: ['superadmin', 'admin', 'storekeeper']
				}
			}
		},
		{
			path: '/stores/:storeId/operation/:id//',
			name: 'StoreOperationDetails',
			component: StoreOperation,
			props: true,
			meta: {
				permissions: {
					roles: ['superadmin', 'admin', 'storekeeper']
				}
			}
		},

		{
			path: '*',
			redirect: {name: 'Home'},
		}
	],
})

// Конфигурируем полосу загрузки
NProgress.configure({
	showSpinner: false,
	trickleSpeed: 100
})

router.beforeEach((to, from, next) => {
	// Проверка на авторизацию
	let isAuth = store.getters['auth/isAuth']

	if (to.name !== 'Login' && !isAuth) {
		if (from.name !== 'Login') {
			NProgress.start()
			next({name: 'Login', replace: true})
		} else {
			next(false)
		}
	} else if (to.name === 'Login' && isAuth) {
		next(false)
	} else {

		// Проверка роли пользователей:
		if (to.meta.permissions && to.meta.permissions.roles) {
			let user = store.state.auth.user
			if (!user || !user.roles || !user.roles.length) next(false)
			else {
				let allow = false
				user.roles.forEach((userRole) => {
					if (to.meta.permissions.roles.indexOf(userRole) > -1) allow = true
				})
				if (!allow) {
					if (to.meta.permissions.redirect) next(to.meta.permissions.redirect)
					else next(false)
					return
				}
			}
		}

		NProgress.start()
		next()
	}
})

router.afterEach((to, from) => {
	NProgress.done()
})

export default router
