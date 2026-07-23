import { ActivityIndicator, StyleSheet, Text, TouchableOpacity, View } from "react-native";
import { colors, minimumTouchSize, radius, spacing } from "../theme/tokens";

export function LoadingState({ message = "Memuat...", fullScreen = false, light = false, color }) {
  return (
    <View
      style={[styles.state, fullScreen && styles.fullScreen, light && styles.darkBackground]}
      accessibilityRole="progressbar"
      accessibilityLabel={message}
    >
      <ActivityIndicator size={fullScreen ? "large" : "small"} color={color || (light ? colors.onPrimary : colors.primary)} />
      {message ? <Text style={[styles.message, light && styles.lightText]}>{message}</Text> : null}
    </View>
  );
}

export function EmptyState({ title, description }) {
  return (
    <View style={[styles.state, styles.fullScreen]} accessibilityRole="summary">
      <Text style={styles.title}>{title}</Text>
      <Text style={styles.message}>{description}</Text>
    </View>
  );
}

export function ErrorState({ title = "Data belum dapat dimuat", message, onRetry, retrying = false }) {
  return (
    <View style={[styles.state, styles.fullScreen]} accessibilityRole="alert">
      <Text style={[styles.title, styles.errorTitle]}>{title}</Text>
      <Text style={styles.message}>{message}</Text>
      <TouchableOpacity
        style={styles.retryButton}
        onPress={onRetry}
        disabled={retrying}
        accessibilityRole="button"
        accessibilityLabel="Coba muat data lagi"
      >
        <Text style={styles.retryText}>{retrying ? "Memuat..." : "Coba Lagi"}</Text>
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  state: { alignItems: "center", justifyContent: "center", padding: spacing.xl },
  fullScreen: { flex: 1, backgroundColor: colors.background },
  darkBackground: { backgroundColor: colors.darkBackground },
  title: { color: colors.text, fontSize: 18, fontWeight: "700", textAlign: "center" },
  errorTitle: { color: colors.danger },
  message: { color: colors.textMuted, marginTop: spacing.sm, textAlign: "center" },
  lightText: { color: colors.onPrimary },
  retryButton: { minHeight: minimumTouchSize, marginTop: spacing.lg, borderRadius: radius.md, backgroundColor: colors.primary, paddingHorizontal: spacing.xl, justifyContent: "center" },
  retryText: { color: colors.onPrimary, fontWeight: "700" },
});
