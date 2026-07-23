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

const DashboardFasil = ({ navigation, route }) => {
  const { logout } = useContext(AuthContext);
  const [muridData, setMuridData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [errorMessage, setErrorMessage] = useState("");
  const hasLoaded = useRef(false);
  const requestInFlight = useRef(false);
  const lastCreatedAt = useRef(null);

  useLayoutEffect(() => {
    navigation.setOptions({
      title: "Dashboard Fasilitator",
      headerRight: () => (
        <TouchableOpacity onPress={logout} style={styles.logoutButton} accessibilityRole="button" accessibilityLabel="Keluar dari akun">
          <Text style={styles.logoutText}>Logout</Text>
        </TouchableOpacity>
      ),
    });
  }, [navigation, logout]);

  const fetchDashboard = useCallback(async (initial = false) => {
    if (requestInFlight.current) return;

    requestInFlight.current = true;
    setErrorMessage("");
    if (initial) setLoading(true);
    else setRefreshing(true);

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
      const createdAt = route.params?.kurtiCreatedAt;
      const created = createdAt && createdAt !== lastCreatedAt.current;

      if (!hasLoaded.current || created) {
        fetchDashboard(!hasLoaded.current);
      }

      hasLoaded.current = true;
      if (createdAt) lastCreatedAt.current = createdAt;
    }, [fetchDashboard, route.params?.kurtiCreatedAt]),
  );

  if (loading) {
    return <LoadingState message="Memuat dashboard..." fullScreen />;
  }

  if (errorMessage && muridData.length === 0) {
    return <ErrorState title="Dashboard belum dapat dimuat" message={errorMessage} onRetry={() => fetchDashboard(true)} />;
  }

  const classroomName = muridData.find((murid) => murid.classroom)?.classroom;

  return (
    <ScrollView
      style={styles.container}
      contentContainerStyle={styles.content}
      refreshControl={
        <RefreshControl
          refreshing={refreshing}
          onRefresh={() => fetchDashboard(false)}
          colors={[colors.primary]}
          tintColor={colors.primary}
        />
      }
    >
      {errorMessage ? <Text style={styles.inlineError}>{errorMessage}</Text> : null}

      {muridData.length === 0 ? (
        <EmptyState title="Belum ada murid" description="Belum ada murid pada kelas aktif Anda." />
      ) : (
        <>
          <Text style={styles.classLabel}>Kelas aktif</Text>
          <Text style={styles.className}>{classroomName || "Kelas belum diketahui"}</Text>

          {muridData.map((murid) => (
            <View key={murid.murid_id} style={styles.studentCard}>
              <View style={styles.studentHeader}>
                <Text style={styles.studentName}>{murid.murid_name}</Text>
                <TouchableOpacity
                  style={styles.addButton}
                  accessibilityRole="button"
                  accessibilityLabel={`Tambah Kurti untuk ${murid.murid_name}`}
                  onPress={() =>
                    navigation.navigate("CreateMultipleKurti", {
                      muridId: murid.murid_id,
                      classroomId: murid.current_classroom_id,
                    })
                  }
                >
                  <Text style={styles.addButtonText}>+ Tambah Kurti</Text>
                </TouchableOpacity>
              </View>

              {(murid.groups || []).length === 0 ? (
                <Text style={styles.noKurtiText}>Belum ada data Kurti untuk murid ini.</Text>
              ) : (
                murid.groups.map((monthGroup) => (
                  <View
                    key={`${murid.murid_id}-${monthGroup.bulan || "bulan"}`}
                    style={styles.monthSection}
                  >
                    <Text style={styles.monthTitle}>
                      Bulan {monthGroup.bulan || "belum ditentukan"}
                    </Text>

                    {(monthGroup.pekans || []).map((weekGroup) => (
                      <TouchableOpacity
                        key={weekGroup.group_id}
                        style={styles.weekButton}
                        accessibilityRole="button"
                        accessibilityLabel={`Buka detail pekan ${weekGroup.pekan}, ${weekGroup.jumlah} aktivitas`}
                        onPress={() =>
                          navigation.navigate("KurtiDetail", {
                            muridId: murid.murid_id,
                            groupId: weekGroup.group_id,
                            groupName: `Pekan ${weekGroup.pekan}`,
                          })
                        }
                      >
                        <View>
                          <Text style={styles.weekTitle}>Pekan {weekGroup.pekan}</Text>
                          <Text style={styles.activityCount}>
                            {weekGroup.jumlah} aktivitas
                          </Text>
                        </View>
                        <Text style={styles.chevron}>›</Text>
                      </TouchableOpacity>
                    ))}
                  </View>
                ))
              )}
            </View>
          ))}
        </>
      )}
    </ScrollView>
  );
};

export default DashboardFasil;

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  content: { flexGrow: 1, padding: spacing.lg },
  logoutButton: { marginRight: spacing.lg, minHeight: minimumTouchSize, justifyContent: "center" },
  logoutText: { color: colors.danger, fontWeight: "bold" },
  inlineError: { marginBottom: spacing.md, borderRadius: radius.md, backgroundColor: colors.dangerSoft, color: colors.danger, padding: spacing.md },
  classLabel: { color: colors.textMuted, fontSize: 12, textTransform: "uppercase", letterSpacing: 0.6 },
  className: { fontSize: 22, fontWeight: "700", color: colors.text, marginTop: 2, marginBottom: spacing.lg },
  studentCard: { backgroundColor: colors.surface, borderRadius: radius.lg, padding: spacing.lg, marginBottom: spacing.lg, elevation: 2, shadowColor: colors.text, shadowOpacity: 0.08, shadowOffset: { width: 0, height: 2 }, shadowRadius: 5 },
  studentHeader: { flexDirection: "row", alignItems: "center", justifyContent: "space-between", gap: 8 },
  studentName: { flex: 1, fontSize: 18, fontWeight: "700", color: colors.text },
  addButton: { backgroundColor: colors.primary, paddingHorizontal: 10, minHeight: minimumTouchSize, borderRadius: radius.sm, justifyContent: "center" },
  addButtonText: { color: colors.onPrimary, fontWeight: "600" },
  noKurtiText: { marginTop: 14, color: colors.textMuted, fontStyle: "italic" },
  monthSection: { marginTop: 18 },
  monthTitle: { fontSize: 16, fontWeight: "700", color: colors.text, marginBottom: spacing.sm },
  weekButton: { flexDirection: "row", justifyContent: "space-between", alignItems: "center", borderWidth: 1, borderColor: colors.border, backgroundColor: colors.surface, padding: spacing.md, minHeight: minimumTouchSize, borderRadius: radius.md, marginBottom: spacing.sm },
  weekTitle: { fontSize: 16, fontWeight: "700", color: colors.text },
  activityCount: { color: colors.textMuted, marginTop: spacing.xs },
  chevron: { fontSize: 24, color: colors.primary, lineHeight: 24 },
});
