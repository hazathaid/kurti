import AsyncStorage from "@react-native-async-storage/async-storage";
import axios from "axios";

const DEFAULT_API_URL = "https://kurti.saisukabumi.sch.id/api";

const normalizeApiUrl = (url) => {
  const baseUrl = (url || DEFAULT_API_URL).trim().replace(/\/+$/, "");
  return baseUrl.endsWith("/api") ? baseUrl : `${baseUrl}/api`;
};

const api = axios.create({
  baseURL: normalizeApiUrl(process.env.EXPO_PUBLIC_API_URL),
});

let unauthorizedHandler;
let handlingUnauthorized = false;

export const setUnauthorizedHandler = (handler) => {
  unauthorizedHandler = handler;
};

api.interceptors.request.use(async (config) => {
  const token = await AsyncStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 401 && unauthorizedHandler && !handlingUnauthorized) {
      handlingUnauthorized = true;
      try {
        await unauthorizedHandler();
      } finally {
        handlingUnauthorized = false;
      }
    }

    return Promise.reject(error);
  },
);

export default api;
