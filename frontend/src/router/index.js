import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'Home',
            component: HomeView,
            meta: { breadcrumb: '' }
        },
        {
            path: '/workout',
            name: 'Workout',
            component: () => import('../views/WorkoutView.vue'),
            meta: { breadcrumb: 'Home/Workout' }
        },
        {
            path: '/workout/monday',
            name: 'WorkoutDayMonday',
            component: () => import('../views/WorkoutDayView.vue'),
            meta: { breadcrumb: 'Home/Workout/Monday' }
        },
        {
            path: '/workout/tuesday',
            name: 'WorkoutDayTuesday',
            component: () => import('../views/WorkoutDayView.vue'),
            meta: { breadcrumb: 'Home/Workout/Tuesday' }
        },
        {
            path: '/workout/wednesday',
            name: 'WorkoutDayWednesday',
            component: () => import('../views/WorkoutDayView.vue'),
            meta: { breadcrumb: 'Home/Workout/Wednesday' }
        },
        {
            path: '/workout/tuesday',
            name: 'WorkoutDayTuesday',
            component: () => import('../views/WorkoutDayView.vue'),
            meta: { breadcrumb: 'Home/Workout/Tuesday' }
        },
        {
            path: '/exercises',
            name: 'Exercises',
            component: () => import('../views/WorkoutView.vue'),
            meta: { breadcrumb: 'Home/Exercises' }
        },
        {
            path: '/progress',
            name: 'Progress',
            component: () => import('../views/WorkoutView.vue'),
            meta: { breadcrumb: 'Home/Progress' }
        }
    ]
})

export default router
