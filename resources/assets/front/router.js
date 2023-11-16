import Vue from 'vue'
import Router from 'vue-router'
import NProgress from 'nprogress'

import Home from './views/Home.vue'

Vue.use(Router)

// Определяем роуты
const router = new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/',
      name: 'Home',
      component: Home
    },
    // { // ALWAYS AT THE END
    //   path: '/404',
    //   name: '404',
    //   component: PageNotFound
    // },
    // { path: '*', redirect: '/404' }
  ],
  scrollBehavior (to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else if (to.hash) {
      return { selector: to.hash }
    } else {
      return { x: 0, y: 0 }
    }
  },
})


// Конфигурируем полосу загрузки
NProgress.configure({
  showSpinner: false,
  trickleSpeed: 100
})

router.beforeEach((to, from, next) => {
  if (!to.matched.length) {
  	next('/404')
  } else {
    if (to.name) NProgress.start()
    next()
  }
})

router.afterEach((to, from) => {
  NProgress.done()
})

export default router
