import axios from 'axios';
import { useAuthStore } from '@/stores/AuthStore';

const ApiService = {
	init() {
		axios.defaults.baseURL = import.meta.env.VITE_APP_API_URL;
		this.setInterceptors();
	},

	setInterceptors() {
		axios.interceptors.request.use(
			(request) => {
				const token = useAuthStore().token;
				if (token) request.headers['Authorization'] = 'Bearer ' + token;
				return request;
			},
			(error) => Promise.reject(error)
		);

		axios.interceptors.response.use(
			(response) => response,
			(error) => {
				if (error.response.status === 401) useAuthStore().clearAuthorization();
				return Promise.reject(error);
			}
		);
	},

    handleServerError(error) {
        if (error.response.status === 500) {
            const customError = {
                details: error,
                status: 500,
                data: {
                    status: 'Error',
                    message: 'Unexpected error. Please try again later.'
                }
            };
            throw customError;
        }
        throw error.response;
    },

	query(resource, params) {
		return axios
            .get(resource, { params: params })
            .then((response) => response.data)
            .catch((error) => this.handleServerError(error));
	},

	get(resource, slug = '') {
		const path = slug ? `${resource}/${slug}` : resource;
		return axios
            .get(path)
            .then((response) => response.data)
            .catch((error) => this.handleServerError(error));
	},

	post(resource, params) {
        return axios
            .post(resource, params)
            .then((response) => response.data)
            .catch((error) => this.handleServerError(error));
    },

	update(resource, slug, params) {
		return axios
            .put(`${resource}/${slug}`, params)
            .then((response) => response.data)
            .catch((error) => this.handleServerError(error));
	},

	put(resource, params) {
		return axios
            .put(resource, params)
            .then((response) => response.data)
            .catch((error) => this.handleServerError(error));
	},

	delete(resource) {
		return axios
            .delete(resource)
            .then((response) => response.data)
            .catch((error) => this.handleServerError(error));
    }
};

export default ApiService;
