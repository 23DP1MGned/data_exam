import { createRouter, createWebHistory } from 'vue-router'

import Login from '../pages/Login.vue'
import Home from '../pages/Dashboard.vue'
import Register from '../pages/Register.vue'
import Groups from '../pages/Groups.vue'
import Schedule from '../pages/Schedule.vue'
import Payments from '../pages/Payments.vue'
import Attendance from '../pages/Attendance.vue'

const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    component: Login
  },
  {
    path: '/dashboard',
    redirect: '/home'
  },
  {
    path: '/home',
    component: Home
  },
  {
    path: '/register',
    component: Register
  },
    {
    path: '/groups',
    component: Groups
  },
      {
    path: '/schedule',
    component: Schedule
  },
      {
    path: '/payments',
    component: Payments
  },
      {
    path: '/attendance',
    component: Attendance
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})



export default router
