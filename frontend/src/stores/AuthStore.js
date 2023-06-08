import { defineStore } from 'pinia';
import { router } from '@/router/index';

import ApiService from '@/services/api.service';
import { setCookie, getCookie, deleteCookie } from '@/services/storage.service';

export const useAuthStore = defineStore('authStore', {
    state: () => ({
        token: getCookie('token') || null,
        user: {},
        loading: false,
    }),
    getters: {
        isLoading: (state) => state.loading,
        isLogged: (state) => !!state.token,
        isAuthenticated: (state) => !!state.token && Object.keys(state.user).length != 0 && state.user.constructor === Object,
    },
    actions: {
        async register(credentials) {
            try {
                this.loading = true;
                const response = await ApiService.post('/auth/register', credentials);
                this.setAuthorization(response.data.token, response.data.user);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async login(credentials) {
            try {
                this.loading = true;
                const response = await ApiService.post('/auth/login', credentials);
                this.setAuthorization(response.data.token, response.data.user);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async passwordRecover(credentials) {
            try {
                this.loading = true;
                const response = await ApiService.post('/auth/recover', credentials);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async validatePasswordResetHash(hash) {
            try {
                this.loading = true;
                const response = await ApiService.get('/auth/recover', hash);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async passwordReset(credentials) {
            try {
                this.loading = true;
                const response = await ApiService.post('/auth/reset', credentials);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async fetchUser() {
            try {
                this.loading = true;
                const response = await ApiService.get('auth/user');
                this.user = response.data;
            } catch (error) {
               this.clearAuthorization();
            } finally {
                this.loading = false;
            }

        },
        setAuthorization(token, user) {
            this.user = user;
            this.token = token;
            setCookie('token', token);
            router.push({ name: 'Home' });
        },
        clearAuthorization() {
            this.user = {};
            this.token = null;
            deleteCookie('token');
            router.push({ name: 'Login' });
        },
    }
});