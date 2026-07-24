import { useCallback, useContext, useEffect, useState } from "react";
import {
  Alert,
  RefreshControl,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View,
} from "react-native";
import api from "../api/client";
import { getApiErrorMessage } from "../api/errors";
import { EmptyState, ErrorState, LoadingState } from "../components/StateViews";
import { AuthContext } from "../contexts/AuthContext";
import { colors, minimumTouchSize, radius, spacing } from "../theme/tokens";

const dateLabel = (date) =>
  new Intl.DateTimeFormat("id-ID", { day: "numeric", month: "short", year: "numeric" })
    .format(new Date(`${date}T00:00:00`));

const WeeklyReportsScreen = ({ navigation, route }) => {
  const { user } = useContext(AuthContext);
  const [reports, setReports] = useState([]);
  const [selected, setSelected] = useState(null);
  const [feedback, setFeedback] = useState("");
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState("");

  const fetchReports = useCallback(async (refresh = false) => {
    if (refresh) setRefreshing(true);
    else setLoading(true);
    setError("");
    try {
      const { data } = await api.get("/weekly-reports");
      setReports(Array.isArray(data.data) ? data.data : []);
    } catch (requestError) {
      setError(getApiErrorMessage(requestError));
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  const openReport = useCallback(async (reportOrId) => {
    const id = typeof reportOrId === "object" ? reportOrId.id : reportOrId;
    setLoading(true);
    setError("");
    try {
      const { data } = await api.get(`/weekly-reports/${id}`);
      setSelected(data.data);
      setFeedback(data.data.parent_feedback || "");
      setReports((current) => current.map((item) => item.id === id ? data.data : item));
    } catch (requestError) {
      setError(getApiErrorMessage(requestError));
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    if (route.params?.reportId) openReport(route.params.reportId);
    else fetchReports();
  }, [fetchReports, openReport, route.params?.reportId, route.params?.createdAt]);

  const saveFeedback = async () => {
    if (saving) return;
    setSaving(true);
    try {
      const { data } = await api.put(`/weekly-reports/${selected.id}/feedback`, {
        parent_feedback: feedback,
      });
      setSelected((current) => ({ ...current, ...data.data }));
      Alert.alert("Berhasil", "Feedback berhasil disimpan.");
    } catch (requestError) {
      Alert.alert("Gagal", getApiErrorMessage(requestError));
    } finally {
      setSaving(false);
    }
  };

  if (loading) return <LoadingState message="Memuat weekly report..." fullScreen />;
  if (error && reports.length === 0 && !selected) {
    return <ErrorState title="Weekly report belum dapat dimuat" message={error} onRetry={() => fetchReports()} />;
  }

  if (selected) {
    return (
      <ScrollView style={styles.container} contentContainerStyle={styles.content}>
        <TouchableOpacity onPress={() => { setSelected(null); navigation.setParams({ reportId: undefined }); }} style={styles.backButton}>
          <Text style={styles.backText}>← Daftar report</Text>
        </TouchableOpacity>
        <View style={styles.card}>
          <Text style={styles.eyebrow}>WEEKLY REPORT</Text>
          <Text style={styles.title}>{selected.murid?.name}</Text>
          <Text style={styles.meta}>Pekan mulai {dateLabel(selected.week_start)} · {selected.fasil?.name}</Text>
          <View style={[styles.badge, selected.read_at ? styles.readBadge : styles.unreadBadge]}>
            <Text style={selected.read_at ? styles.readText : styles.unreadText}>
              {selected.read_at ? "Sudah dibaca orang tua" : "Belum dibaca orang tua"}
            </Text>
          </View>

          <ReportSection title="Ringkasan perkembangan" value={selected.summary} />
          <ReportSection title="Pencapaian" value={selected.achievements} optional />
          <ReportSection title="Catatan / tindak lanjut" value={selected.notes} optional />

          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Feedback orang tua (opsional)</Text>
            {user?.type === "orangtua" ? (
              <>
                <TextInput
                  value={feedback}
                  onChangeText={setFeedback}
                  multiline
                  maxLength={5000}
                  placeholder="Tulis tanggapan atau pertanyaan jika ada..."
                  style={styles.input}
                />
                <TouchableOpacity disabled={saving} onPress={saveFeedback} style={[styles.primaryButton, saving && styles.disabled]}>
                  <Text style={styles.primaryText}>{saving ? "Menyimpan..." : "Simpan Feedback"}</Text>
                </TouchableOpacity>
              </>
            ) : (
              <Text style={styles.body}>{selected.parent_feedback || "Belum ada feedback."}</Text>
            )}
          </View>
        </View>
      </ScrollView>
    );
  }

  return (
    <ScrollView
      style={styles.container}
      contentContainerStyle={styles.content}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => fetchReports(true)} />}
    >
      {error ? <Text style={styles.error}>{error}</Text> : null}
      {user?.type === "fasil" ? (
        <TouchableOpacity style={styles.primaryButton} onPress={() => navigation.navigate("CreateWeeklyReport")}>
          <Text style={styles.primaryText}>+ Buat Weekly Report</Text>
        </TouchableOpacity>
      ) : null}
      {reports.length === 0 ? (
        <EmptyState title="Belum ada weekly report" description="Laporan mingguan akan tampil di sini." />
      ) : reports.map((report) => (
        <TouchableOpacity key={report.id} style={styles.card} onPress={() => openReport(report)}>
          <View style={styles.row}>
            <View style={styles.flex}>
              <Text style={styles.title}>{report.murid?.name}</Text>
              <Text style={styles.meta}>Pekan mulai {dateLabel(report.week_start)}</Text>
            </View>
            <View style={[styles.badge, report.read_at ? styles.readBadge : styles.unreadBadge]}>
              <Text style={report.read_at ? styles.readText : styles.unreadText}>
                {report.read_at ? "Dibaca" : "Belum dibaca"}
              </Text>
            </View>
          </View>
          <Text numberOfLines={2} style={[styles.body, styles.summary]}>{report.summary}</Text>
          {report.parent_feedback ? <Text style={styles.feedbackLabel}>Ada feedback orang tua</Text> : null}
        </TouchableOpacity>
      ))}
    </ScrollView>
  );
};

const ReportSection = ({ title, value, optional }) => (
  <View style={styles.section}>
    <Text style={styles.sectionTitle}>{title}</Text>
    <Text style={styles.body}>{value || (optional ? "—" : "")}</Text>
  </View>
);

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  content: { flexGrow: 1, padding: spacing.lg },
  card: { backgroundColor: colors.surface, padding: spacing.lg, borderRadius: radius.lg, marginBottom: spacing.md, elevation: 2 },
  row: { flexDirection: "row", alignItems: "flex-start", gap: spacing.sm },
  flex: { flex: 1 },
  eyebrow: { color: colors.primary, fontSize: 12, fontWeight: "700", letterSpacing: 0.7 },
  title: { color: colors.text, fontSize: 20, fontWeight: "700" },
  meta: { color: colors.textMuted, marginTop: spacing.xs },
  body: { color: colors.text, lineHeight: 22 },
  summary: { marginTop: spacing.md },
  section: { borderTopWidth: 1, borderTopColor: colors.border, marginTop: spacing.lg, paddingTop: spacing.lg },
  sectionTitle: { color: colors.text, fontWeight: "700", marginBottom: spacing.sm },
  badge: { borderRadius: 20, paddingHorizontal: 10, paddingVertical: 5, alignSelf: "flex-start", marginTop: spacing.sm },
  readBadge: { backgroundColor: colors.successSoft || "#dcfce7" },
  unreadBadge: { backgroundColor: "#fef3c7" },
  readText: { color: colors.success, fontSize: 12, fontWeight: "700" },
  unreadText: { color: "#92400e", fontSize: 12, fontWeight: "700" },
  feedbackLabel: { color: colors.primary, fontWeight: "600", marginTop: spacing.sm },
  input: { borderWidth: 1, borderColor: colors.border, borderRadius: radius.md, minHeight: 100, padding: spacing.md, textAlignVertical: "top" },
  primaryButton: { minHeight: minimumTouchSize, backgroundColor: colors.primary, borderRadius: radius.md, alignItems: "center", justifyContent: "center", paddingHorizontal: spacing.md, marginBottom: spacing.md },
  primaryText: { color: colors.onPrimary, fontWeight: "700" },
  disabled: { opacity: 0.6 },
  backButton: { minHeight: minimumTouchSize, justifyContent: "center" },
  backText: { color: colors.primary, fontWeight: "600" },
  error: { backgroundColor: colors.dangerSoft, color: colors.danger, padding: spacing.md, borderRadius: radius.md, marginBottom: spacing.md },
});

export default WeeklyReportsScreen;
