import { Ionicons } from "@expo/vector-icons";
import { useCallback, useEffect, useLayoutEffect, useRef, useState } from "react";
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
import { colors, minimumTouchSize, radius, spacing } from "../theme/tokens";

const MONTH_NAMES = [
  "Januari",
  "Februari",
  "Maret",
  "April",
  "Mei",
  "Juni",
  "Juli",
  "Agustus",
  "September",
  "Oktober",
  "November",
  "Desember",
];

const formatMonth = (value) => {
  const [year, month] = String(value || "").split("-");
  const monthName = MONTH_NAMES[Number(month) - 1];
  return monthName && year ? `${monthName} ${year}` : value || "Periode";
};

const periodKey = (period) => `${period.bulan}-${period.pekan}`;

const ProgressBar = ({ percentage }) => (
  <View
    style={styles.progressTrack}
    accessibilityRole="progressbar"
    accessibilityValue={{ min: 0, max: 100, now: percentage }}
  >
    <View style={[styles.progressFill, { width: `${percentage}%` }]} />
  </View>
);

const WeeklyReportScreen = ({ navigation }) => {
  const [report, setReport] = useState(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [errorMessage, setErrorMessage] = useState("");
  const requestId = useRef(0);

  useLayoutEffect(() => {
    navigation.setOptions({ title: "Laporan Mingguan" });
  }, [navigation]);

  const fetchReport = useCallback(async (period = null, initial = false) => {
    const currentRequest = ++requestId.current;
    setErrorMessage("");
    if (initial) setLoading(true);
    else setRefreshing(true);

    try {
      const params = period ? { bulan: period.bulan, pekan: period.pekan } : undefined;
      const { data } = await api.get("/weekly-report", { params });
      if (data.status !== "success" || !data.data) {
        throw new Error("Unexpected weekly report response");
      }

      if (currentRequest === requestId.current) setReport(data.data);
    } catch (error) {
      if (currentRequest !== requestId.current) return;
      setErrorMessage(
        error.response
          ? getApiErrorMessage(error)
          : error.message === "Unexpected weekly report response"
            ? "Data laporan tidak dapat dibaca. Silakan coba lagi."
            : getApiErrorMessage(error),
      );
    } finally {
      if (currentRequest === requestId.current) {
        setLoading(false);
        setRefreshing(false);
      }
    }
  }, []);

  useEffect(() => {
    fetchReport(null, true);
    return () => {
      requestId.current += 1;
    };
  }, [fetchReport]);

  if (loading) {
    return <LoadingState message="Memuat laporan mingguan..." fullScreen />;
  }

  if (errorMessage && !report) {
    return (
      <ErrorState
        title="Laporan belum dapat dimuat"
        message={errorMessage}
        onRetry={() => fetchReport(null, true)}
      />
    );
  }

  const selectedPeriod = report?.selected_period;
  const summary = report?.summary || {};
  const students = report?.students || [];

  return (
    <ScrollView
      style={styles.container}
      contentContainerStyle={styles.content}
      refreshControl={
        <RefreshControl
          refreshing={refreshing}
          onRefresh={() => fetchReport(selectedPeriod, false)}
          colors={[colors.primary]}
          tintColor={colors.primary}
        />
      }
    >
      {errorMessage ? <Text style={styles.inlineError}>{errorMessage}</Text> : null}

      {!selectedPeriod ? (
        <EmptyState
          title="Belum ada laporan"
          description="Laporan mingguan akan tersedia setelah aktivitas Kurti ditambahkan."
        />
      ) : (
        <>
          <View style={styles.headingRow}>
            <View style={styles.headingIcon}>
              <Ionicons name="calendar-outline" size={22} color={colors.primary} />
            </View>
            <View style={styles.headingCopy}>
              <Text style={styles.eyebrow}>{report.classroom || "Aktivitas anak"}</Text>
              <Text style={styles.heading}>{formatMonth(selectedPeriod.bulan)}</Text>
            </View>
          </View>

          <Text style={styles.sectionLabel}>Pilih periode</Text>
          <ScrollView
            horizontal
            showsHorizontalScrollIndicator={false}
            contentContainerStyle={styles.periodList}
          >
            {(report.periods || []).map((period) => {
              const selected = periodKey(period) === periodKey(selectedPeriod);
              return (
                <TouchableOpacity
                  key={periodKey(period)}
                  style={[styles.periodButton, selected && styles.periodButtonSelected]}
                  accessibilityRole="button"
                  accessibilityState={{ selected, disabled: refreshing }}
                  accessibilityLabel={`${formatMonth(period.bulan)}, pekan ${period.pekan}`}
                  disabled={refreshing || selected}
                  onPress={() => fetchReport(period, false)}
                >
                  <Text style={[styles.periodMonth, selected && styles.periodTextSelected]}>
                    {formatMonth(period.bulan)}
                  </Text>
                  <Text style={[styles.periodWeek, selected && styles.periodTextSelected]}>
                    Pekan {period.pekan}
                  </Text>
                </TouchableOpacity>
              );
            })}
          </ScrollView>

          <View style={styles.summaryBand}>
            <View style={styles.summaryHeader}>
              <View>
                <Text style={styles.summaryLabel}>Kelengkapan catatan</Text>
                <Text style={styles.summaryValue}>{summary.completion_percentage || 0}%</Text>
              </View>
              <View style={styles.summaryIcon}>
                <Ionicons name="checkmark-done" size={24} color={colors.success} />
              </View>
            </View>
            <ProgressBar percentage={summary.completion_percentage || 0} />
            <Text style={styles.summaryCaption}>
              {summary.notes_filled || 0} dari {summary.activities || 0} aktivitas sudah diberi catatan
            </Text>
            <View style={styles.metricRow}>
              <View style={styles.metric}>
                <Text style={styles.metricValue}>{summary.students || 0}</Text>
                <Text style={styles.metricLabel}>Murid</Text>
              </View>
              <View style={styles.metricDivider} />
              <View style={styles.metric}>
                <Text style={styles.metricValue}>{summary.activities || 0}</Text>
                <Text style={styles.metricLabel}>Aktivitas</Text>
              </View>
              <View style={styles.metricDivider} />
              <View style={styles.metric}>
                <Text style={[styles.metricValue, styles.pendingValue]}>{summary.notes_pending || 0}</Text>
                <Text style={styles.metricLabel}>Belum diisi</Text>
              </View>
            </View>
          </View>

          <Text style={styles.sectionTitle}>Ringkasan murid</Text>
          {students.map((student) => (
            <TouchableOpacity
              key={student.murid_id}
              style={styles.studentRow}
              accessibilityRole="button"
              accessibilityLabel={`Buka laporan ${student.name}, ${student.completion_percentage} persen lengkap`}
              onPress={() =>
                navigation.navigate("KurtiDetail", {
                  muridId: student.murid_id,
                  groupId: selectedPeriod.group_id,
                  groupName: `Pekan ${selectedPeriod.pekan}`,
                })
              }
            >
              <View style={styles.avatar}>
                <Text style={styles.avatarText}>{student.name?.trim()?.charAt(0)?.toUpperCase() || "?"}</Text>
              </View>
              <View style={styles.studentContent}>
                <View style={styles.studentHeader}>
                  <Text style={styles.studentName} numberOfLines={1}>{student.name}</Text>
                  <Text style={styles.studentPercentage}>{student.completion_percentage}%</Text>
                </View>
                <ProgressBar percentage={student.completion_percentage} />
                <Text style={styles.studentCaption}>
                  {student.notes_filled} dari {student.total_activities} catatan terisi
                </Text>
              </View>
              <Ionicons name="chevron-forward" size={20} color={colors.textMuted} />
            </TouchableOpacity>
          ))}
        </>
      )}
    </ScrollView>
  );
};

export default WeeklyReportScreen;

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  content: { flexGrow: 1, padding: spacing.lg, paddingBottom: spacing.xxl },
  inlineError: { marginBottom: spacing.md, borderRadius: radius.md, backgroundColor: colors.dangerSoft, color: colors.danger, padding: spacing.md },
  headingRow: { flexDirection: "row", alignItems: "center", marginBottom: spacing.xl },
  headingIcon: { width: minimumTouchSize, height: minimumTouchSize, borderRadius: radius.md, backgroundColor: colors.primarySoft, alignItems: "center", justifyContent: "center", marginRight: spacing.md },
  headingCopy: { flex: 1 },
  eyebrow: { color: colors.textMuted, fontSize: 13 },
  heading: { color: colors.text, fontSize: 22, fontWeight: "700", marginTop: 2 },
  sectionLabel: { color: colors.textMuted, fontSize: 12, textTransform: "uppercase", letterSpacing: 0.6, marginBottom: spacing.sm },
  periodList: { gap: spacing.sm, paddingRight: spacing.lg, paddingBottom: spacing.xs },
  periodButton: { minHeight: minimumTouchSize, minWidth: 112, borderRadius: radius.md, borderWidth: 1, borderColor: colors.border, backgroundColor: colors.surface, paddingHorizontal: spacing.md, paddingVertical: spacing.sm, justifyContent: "center" },
  periodButtonSelected: { backgroundColor: colors.primary, borderColor: colors.primary },
  periodMonth: { color: colors.text, fontSize: 13, fontWeight: "600" },
  periodWeek: { color: colors.textMuted, fontSize: 12, marginTop: 2 },
  periodTextSelected: { color: colors.onPrimary },
  summaryBand: { marginTop: spacing.xl, marginHorizontal: -spacing.lg, backgroundColor: colors.surface, borderTopWidth: 1, borderBottomWidth: 1, borderColor: colors.border, padding: spacing.lg },
  summaryHeader: { flexDirection: "row", alignItems: "center", justifyContent: "space-between" },
  summaryLabel: { color: colors.textMuted, fontSize: 14 },
  summaryValue: { color: colors.text, fontSize: 30, fontWeight: "700", marginTop: spacing.xs },
  summaryIcon: { width: minimumTouchSize, height: minimumTouchSize, borderRadius: radius.md, backgroundColor: "#dcfce7", alignItems: "center", justifyContent: "center" },
  progressTrack: { height: 7, borderRadius: 4, overflow: "hidden", backgroundColor: colors.track, marginTop: spacing.sm },
  progressFill: { height: "100%", borderRadius: 4, backgroundColor: colors.success },
  summaryCaption: { color: colors.textMuted, fontSize: 13, marginTop: spacing.sm },
  metricRow: { flexDirection: "row", alignItems: "stretch", marginTop: spacing.lg, borderTopWidth: 1, borderColor: colors.border, paddingTop: spacing.lg },
  metric: { flex: 1, alignItems: "center" },
  metricDivider: { width: 1, backgroundColor: colors.border },
  metricValue: { color: colors.text, fontSize: 20, fontWeight: "700" },
  pendingValue: { color: colors.warning },
  metricLabel: { color: colors.textMuted, fontSize: 12, marginTop: 2 },
  sectionTitle: { color: colors.text, fontSize: 17, fontWeight: "700", marginTop: spacing.xl, marginBottom: spacing.md },
  studentRow: { minHeight: 88, flexDirection: "row", alignItems: "center", backgroundColor: colors.surface, borderWidth: 1, borderColor: colors.border, borderRadius: radius.md, padding: spacing.md, marginBottom: spacing.sm },
  avatar: { width: 40, height: 40, borderRadius: 20, backgroundColor: colors.primarySoft, alignItems: "center", justifyContent: "center", marginRight: spacing.md },
  avatarText: { color: colors.primary, fontSize: 17, fontWeight: "700" },
  studentContent: { flex: 1, marginRight: spacing.md },
  studentHeader: { flexDirection: "row", justifyContent: "space-between", alignItems: "center" },
  studentName: { flex: 1, color: colors.text, fontSize: 15, fontWeight: "700", marginRight: spacing.sm },
  studentPercentage: { color: colors.success, fontSize: 13, fontWeight: "700" },
  studentCaption: { color: colors.textMuted, fontSize: 12, marginTop: spacing.xs },
});
