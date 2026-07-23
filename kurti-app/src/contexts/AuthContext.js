import AsyncStorage from "@react-native-async-storage/async-storage";
import { createContext, useCallback, useContext, useEffect, useRef, useState } from "react";
import Toast from "react-native-toast-message";
import api, { setUnauthorizedHandler } from "../api/client";

const TOKEN_KEY = "token";
const USER_KEY = "user";

const clearStoredSession = () => AsyncStorage.multiRemove([TOKEN_KEY, USER_KEY]);

const getSessionUser = (user) => ({
  id: user.id,
  name: user.name,
  email: user.email,
  type: user.type,
});

export const AuthContext = createContext();
export const useAuth = () => useContext(AuthContext);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const logoutInProgress = useRef(false);

  const clearSession = useCallback(async () => {
    try {
      await clearStoredSession();
    } finally {
      setUser(null);
    }
  }, []);

  useEffect(() => {
    const bootstrapSession = async () => {
      try {
        const values = await AsyncStorage.multiGet([TOKEN_KEY, USER_KEY]);
        const token = values[0][1];
        const storedUser = values[1][1];
        const parsedUser = storedUser ? JSON.parse(storedUser) : null;

        if (typeof token !== "string" || !token || !parsedUser?.id || !parsedUser?.type) {
          await clearStoredSession();
          return;
        }

        setUser(parsedUser);
      } catch {
        try {
          await clearStoredSession();
        } catch {
          // The in-memory session is already empty during bootstrap.
        }
      } finally {
        setLoading(false);
      }
    };

    bootstrapSession();
  }, []);

  useEffect(() => {
    setUnauthorizedHandler(clearSession);
    return () => setUnauthorizedHandler(undefined);
  }, [clearSession]);

  const login = async (email, password) => {
    const normalizedEmail = email.trim().toLowerCase();
    const response = await api.post("/login", { email: normalizedEmail, password });
    const json = response.data;

    if (json.status !== "success" || !json.token || !json.user) {
      throw new Error(json.message || "Periksa email dan password");
    }

    const sessionUser = getSessionUser(json.user);
    await AsyncStorage.multiSet([
      [TOKEN_KEY, json.token],
      [USER_KEY, JSON.stringify(sessionUser)],
    ]);
    setUser(sessionUser);
    return sessionUser;
  };

  const logout = useCallback(async () => {
    if (logoutInProgress.current) return;
    logoutInProgress.current = true;

    try {
      await api.post("/logout");
    } catch {
      // Local logout must still succeed when the server is unavailable.
    } finally {
      try {
        await clearSession();
      } finally {
        logoutInProgress.current = false;
        Toast.show({
          type: "info",
          text1: "Anda telah logout",
        });
      }
    }
  }, [clearSession]);

  return (
    <AuthContext.Provider value={{ user, loading, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
};
