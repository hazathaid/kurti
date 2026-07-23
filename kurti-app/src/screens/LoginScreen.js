import { useContext, useState } from "react";
import Toast from "react-native-toast-message";
import { getApiError } from "../api/errors";
import { AuthContext } from "../contexts/AuthContext";

import {
  Image,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
} from "react-native";
import { colors, minimumTouchSize, radius, spacing } from "../theme/tokens";

export default function LoginScreen() {
  const { login } = useContext(AuthContext);
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [loading, setLoading] = useState(false);

  const handleLogin = async () => {
    if (!email.trim() || !password) {
      Toast.show({ type: "error", text1: "Error", text2: "Email dan password wajib diisi" });
      return;
    }

    setLoading(true);
    try {
      await login(email, password);
      Toast.show({ type: "success", text1: "Login berhasil" });
    } catch (error) {
      const apiError = getApiError(error);
      const message = apiError.type === "unauthorized"
        ? "Email atau password salah."
        : apiError.message;
      const title = apiError.type === "network" ? "Koneksi bermasalah" : "Login gagal";
      Toast.show({ type: "error", text1: title, text2: message });
    } finally {
      setLoading(false);
    }
  };

  return (
    <KeyboardAvoidingView style={styles.container} behavior={Platform.OS === "ios" ? "padding" : undefined}>
      <ScrollView contentContainerStyle={styles.content} keyboardShouldPersistTaps="handled">
        <Image
          source={require("../assets/logo.png")}
          style={styles.logo}
          resizeMode="contain"
        />

      <Text style={styles.title}>Masuk</Text>
      <Text style={styles.subtitle}>Silakan login untuk melanjutkan</Text>

      <Text style={styles.label}>Email</Text>
      <TextInput
        style={styles.input}
        placeholder="Email"
        placeholderTextColor={colors.textMuted}
        value={email}
        onChangeText={setEmail}
        keyboardType="email-address"
        autoCapitalize="none"
        autoComplete="email"
        textContentType="emailAddress"
      />

      <Text style={styles.label}>Password</Text>
      <TextInput
        style={styles.input}
        placeholder="Password"
        placeholderTextColor={colors.textMuted}
        secureTextEntry
        value={password}
        onChangeText={setPassword}
        autoComplete="current-password"
        textContentType="password"
      />

        <TouchableOpacity
          style={[styles.loginBtn, loading && { opacity: 0.7 }]}
          onPress={handleLogin}
          disabled={loading}
          accessibilityRole="button"
          accessibilityLabel="Masuk ke aplikasi"
        >
          <Text style={styles.loginText}>
            {loading ? "Loading..." : "Login"}
          </Text>
        </TouchableOpacity>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.darkBackground,
  },
  content: {
    flexGrow: 1,
    alignItems: "center",
    justifyContent: "center",
    padding: spacing.xl,
  },
  logo: {
    width: 120,
    height: 120,
    marginBottom: 20,
  },
  title: {
    fontSize: 28,
    fontWeight: "bold",
    color: colors.onPrimary,
    marginBottom: 5,
  },
  subtitle: {
    fontSize: 14,
    color: colors.textMuted,
    marginBottom: 30,
  },
  label: {
    color: colors.onPrimary,
    alignSelf: "flex-start",
    marginLeft: 10,
    marginBottom: 5,
    fontSize: 14,
  },
  input: {
    width: "100%",
    minHeight: 50,
    borderWidth: 1,
    borderColor: colors.primary,
    borderRadius: radius.lg,
    paddingHorizontal: 15,
    color: colors.onPrimary,
    marginBottom: 15,
  },
  loginBtn: {
    backgroundColor: colors.primary,
    minHeight: minimumTouchSize,
    justifyContent: "center",
    width: "100%",
    borderRadius: radius.lg,
    alignItems: "center",
  },
  loginText: {
    color: colors.onPrimary,
    fontSize: 16,
    fontWeight: "bold",
  },
});
