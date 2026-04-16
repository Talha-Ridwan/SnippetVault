import axios from 'axios';

const api = axios.create({
    baseURL: "/api",
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    }
});

api.interceptors.request.use(
    (config) => {
        // 1. Check if we have a token saved in localStorage
        const token = localStorage.getItem('auth_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }

        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

api.interceptors.response.use(
    (response) => {
        // If the response is good, just pass it through
        return response;
    },
    (error) => {
        if (error.response && error.response.status === 401) {
            localStorage.removeItem('auth_token');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export default api;