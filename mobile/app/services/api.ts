import axios from 'axios';
import { storage } from '../utils/storage';
import { Platform } from 'react-native';

// Replace with your actual backend URL
// For Android Emulator, use 'http://10.0.2.2:8000/api'
// For iOS Simulator, use 'http://localhost:8000/api'
// For physical device, use your machine's LAN IP, e.g., 'http://192.168.1.4:8000/api'
const BASE_URL = Platform.select({
  android: 'http://192.168.1.4:8000/api', // Use LAN IP for physical device (fallback to 10.0.2.2 if emulator)
  ios: 'http://192.168.1.4:8000/api',     // Use LAN IP for physical device (fallback to localhost if simulator)
  default: 'http://localhost:8000/api',
});

const api = axios.create({
  baseURL: BASE_URL,
  timeout: 10000, // 10 seconds timeout
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
});

// Add a request interceptor to attach the token
api.interceptors.request.use(
  async (config) => {
    const token = await storage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    console.log(`[API] ${config.method?.toUpperCase()} ${config.url}`);
    return config;
  },
  (error) => {
    console.error('[API] Request Error:', error);
    return Promise.reject(error);
  }
);

// Add a response interceptor to handle errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    // console.error('[API] Response Error:', {
    //   message: error.message,
    //   status: error.response?.status,
    //   data: error.response?.data,
    //   url: error.config?.url,
    // });
    return Promise.reject(error);
  }
);

export default api;
