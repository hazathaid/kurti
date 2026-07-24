import { useFocusEffect } from "@react-navigation/native";
import { useCallback, useContext, useLayoutEffect, useRef, useState } from "react";
import {
  RefreshControl,
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from "react-native";
import api from "../api/client";
import { getApiErrorMessage } from "../api/errors";
import { EmptyState, ErrorState, LoadingState } from "../components/StateViews";
import { AuthContext } from "../contexts/AuthContext";
import { colors, minimumTouchSize, radius, spacing } from "../theme/tokens";

const STATUS_LABELS = {
  "belum ada data": "Belum ada aktivitas",
  "belum diisi": "Belum diisi",
  onprogress: "Sebagian sudah diisi",
  done: "Sudah lengkap",
};

const STATUS_SYMBOLS = {
  "belum ada data": "—",
  "belum diisi": "○",
  onprogress: "◐",
  done: "✓",
};

const DashboardOrtu = ({ navigation }) => {
  const { logout } = useContext(AuthContext);
  const [muridData, setMuridData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [errorMessage, setErrorMessage] = useState("");
  const hasLoaded = useRef(false);
  const requestInFlight = useRef(false);

  useLayoutEffect(() => {
    navigation.setOptions({
      title: "Dashboard Orang Tua",
      headerRight: () => (
        <TouchableOpacity onPress={logout} style={styles.logoutButton} accessibilityRole="button" accessibilityLabel="Keluar dari akun">
          <Text style={styles.logoutText}>Logout</Text>
        </TouchableOpacity>
      ),
    });
  }, [navigation, logout]);

  const fetchDashboardData = useCallback(async (initial = false) => {
    if (requestInFlight.current) return;

    requestInFlight.current = true;
    setErrorMessage("");
    if (initial) {
      setLoading(true);
    } else {
      setRefreshing(true);
    }

    try {
      const { data } = await api.get("/dashboard");
      if (data.status !== "success") {
        throw new Error("Unexpected dashboard response");
      }

      setMuridData(Array.isArray(data.data) ? data.data : []);
    } catch (error) {
      setErrorMessage(
        error.response
          ? getApiErrorMessage(error)
          : error.message === "Unexpected dashboard response"
            ? "Data dashboard tidak dapat dibaca. Silakan coba lagi."
            : getApiErrorMessage(error),
      );
    } finally {
      requestInFlight.current = false;
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useFocusEffect(
    useCallback(() => {
      fetchDashboardData(!hasLoaded.current);
      hasLoaded.current = true;
    }, [fetchDashboardData]),
  );

  if (loading) {
    return <LoadingState message="Memuat dashboard..." fullScreen />;
  }

  if (errorMessage && muridData.length === 0) {
    return <ErrorState title="Dashboard belum dapat dimuat" message={errorMessage} onRetry={() => fetchDashboardData(true)} retrying={refreshing} />;
  }

  return (
    <ScrollView
      style={styles.container}
      contentContainerStyle={styles.content}
      refreshControl={
        <RefreshControl
          refreshing={refreshing}
          onRefresh={() => fetchDashboardData(false)}
          colors={[colors.primary]}
          tintColor={colors.primary}
        />
      }
    >
      {errorMessage ? <Text style={styles.inlineError}>{errorMessage}</Text> : null}
      <TouchableOpacity style={styles.reportButton} onPress={() => navigation.navigate("WeeklyReports")}>
        <Text style={styles.reportButtonText}>Lihat Weekly Report</Text>
      </TouchableOpacity>

      {muridData.length === 0 ? (
        <EmptyState title="Belum ada data Kurti" description="Data aktivitas anak akan tampil di sini setelah tersedia." />
      ) : (
        muridData.map((murid) => {
          const groupsByMonth = (murid.groups || []).reduce((months, group) => {
            const month = group.bulan || "Tanpa bulan";
            if (!months[month]) months[month] = [];
            months[month].push(group);
            return months;
          }, {});

          return (
            <View key={murid.murid_id} style={styles.card}>
              <Text style={styles.childLabel}>Nama anak</Text>
              <Text style={styles.muridName}>{murid.nama}</Text>

              {Object.entries(groupsByMonth).map(([month, groups]) => (
                <View key={`${murid.murid_id}-${month}`} style={styles.monthSection}>
                  <Text style={styles.monthTitle}>Bulan {month}</Text>

                  {groups.map((group) => {
                    const statusLabel = STATUS_LABELS[group.status] || "Status belum diketahui";
                    const statusSymbol = STATUS_SYMBOLS[group.status] || "?";

                    return (
                      <TouchableOpacity
                        key={group.group_id}
                        style={styles.groupButton}
                        accessibilityRole="button"
                        accessibilityLabel={`Pekan ${group.pekan}, ${statusLabel}, ${group.sudah_diisi} dari ${group.total_aktivitas} aktivitas terisi`}
                        onPress={() =>
                          navigation.navigate("KurtiDetail", {
                            muridId: murid.murid_id,
                            groupId: group.group_id,
                            groupName: `Pekan ${group.pekan}`,
                          })
                        }
                      >
                        <View style={styles.groupHeader}>
                          <Text style={styles.weekText}>Pekan {group.pekan}</Text>
                          <Text style={styles.chevron}>›</Text>
                        </View>
                        <Text style={styles.activityText}>
                          {group.sudah_diisi} dari {group.total_aktivitas} aktivitas terisi
                        </Text>
                        <Text style={styles.statusText}>
                          {statusSymbol} {statusLabel}
                        </Text>
                      </TouchableOpacity>
                    );
                  })}
                </View>
              ))}
            </View>
          );
        })
      )}
    </ScrollView>
  );
};

export default DashboardOrtu;

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  content: { flexGrow: 1, padding: spacing.lg },
  logoutButton: { marginRight: spacing.lg, minHeight: minimumTouchSize, justifyContent: "center" },
  logoutText: { color: colors.danger, fontWeight: "bold" },
  inlineError: { marginBottom: spacing.md, borderRadius: radius.md, backgroundColor: colors.dangerSoft, color: colors.danger, padding: spacing.md },
  card: { backgroundColor: colors.surface, borderRadius: radius.lg, padding: spacing.lg, marginBottom: spacing.lg, elevation: 2, shadowColor: colors.text, shadowOpacity: 0.08, shadowOffset: { width: 0, height: 2 }, shadowRadius: 5 },
  childLabel: { color: colors.textMuted, fontSize: 12, textTransform: "uppercase", letterSpacing: 0.6 },
  muridName: { fontSize: 20, fontWeight: "700", marginTop: 2, color: colors.text },
  monthSection: { marginTop: 18 },
  monthTitle: { fontSize: 16, fontWeight: "700", color: colors.text, marginBottom: spacing.sm },
  groupButton: { borderWidth: 1, borderColor: colors.border, backgroundColor: colors.surface, padding: spacing.md, minHeight: minimumTouchSize, borderRadius: radius.md, marginBottom: spacing.sm },
  groupHeader: { flexDirection: "row", justifyContent: "space-between", alignItems: "center" },
  weekText: { fontSize: 16, fontWeight: "700", color: colors.text },
  chevron: { fontSize: 24, color: colors.primary, lineHeight: 24 },
  activityText: { color: colors.textMuted, marginTop: spacing.xs },
  statusText: { color: colors.primary, fontWeight: "600", marginTop: 6 },
  reportButton: { minHeight: minimumTouchSize, borderRadius: radius.md, backgroundColor: colors.primary, alignItems: "center", justifyContent: "center", marginBottom: spacing.lg },
  reportButtonText: { color: colors.onPrimary, fontWeight: "700" },
});
