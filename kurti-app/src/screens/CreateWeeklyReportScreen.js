import { useEffect, useState } from "react";
import { Alert, ScrollView, StyleSheet, Text, TextInput, TouchableOpacity, View } from "react-native";
import api from "../api/client";
import { getApiErrorMessage } from "../api/errors";
import { LoadingState } from "../components/StateViews";
import { colors, minimumTouchSize, radius, spacing } from "../theme/tokens";

const mondayString = () => {
  const date = new Date();
  const day = date.getDay() || 7;
  date.setDate(date.getDate() - day + 1);
  return date.toISOString().slice(0, 10);
};

const CreateWeeklyReportScreen = ({ navigation }) => {
  const [students, setStudents] = useState([]);
  const [muridId, setMuridId] = useState(null);
  const [weekStart, setWeekStart] = useState(mondayString());
  const [summary, setSummary] = useState("");
  const [achievements, setAchievements] = useState("");
  const [notes, setNotes] = useState("");
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    api.get("/weekly-reports/students")
      .then(({ data }) => setStudents(data.data || []))
      .catch((error) => Alert.alert("Gagal", getApiErrorMessage(error)))
      .finally(() => setLoading(false));
  }, []);

  const submit = async () => {
    if (!muridId || !summary.trim() || !/^\d{4}-\d{2}-\d{2}$/.test(weekStart)) {
      Alert.alert("Data belum lengkap", "Pilih murid, isi tanggal YYYY-MM-DD, dan ringkasan.");
      return;
    }
    setSaving(true);
    try {
      await api.post("/weekly-reports", {
        murid_id: muridId,
        week_start: weekStart,
        summary,
        achievements: achievements || null,
        notes: notes || null,
      });
      Alert.alert("Berhasil", "Weekly report dikirim ke orang tua.", [
        { text: "OK", onPress: () => navigation.navigate("WeeklyReports", { createdAt: Date.now() }) },
      ]);
    } catch (error) {
      Alert.alert("Gagal", getApiErrorMessage(error));
    } finally {
      setSaving(false);
    }
  };

  if (loading) return <LoadingState message="Memuat murid..." fullScreen />;

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content} keyboardShouldPersistTaps="handled">
      <Text style={styles.label}>Pilih murid</Text>
      <View style={styles.studentList}>
        {students.map((student) => (
          <TouchableOpacity
            key={student.id}
            onPress={() => setMuridId(student.id)}
            style={[styles.student, muridId === student.id && styles.studentSelected]}
          >
            <Text style={[styles.studentText, muridId === student.id && styles.studentTextSelected]}>{student.name}</Text>
          </TouchableOpacity>
        ))}
      </View>
      <Field label="Tanggal awal pekan (YYYY-MM-DD)" value={weekStart} onChangeText={setWeekStart} />
      <Field label="Ringkasan perkembangan" value={summary} onChangeText={setSummary} multiline required />
      <Field label="Pencapaian (opsional)" value={achievements} onChangeText={setAchievements} multiline />
      <Field label="Catatan / tindak lanjut (opsional)" value={notes} onChangeText={setNotes} multiline />
      <TouchableOpacity disabled={saving} onPress={submit} style={[styles.button, saving && styles.disabled]}>
        <Text style={styles.buttonText}>{saving ? "Mengirim..." : "Kirim ke Orang Tua"}</Text>
      </TouchableOpacity>
    </ScrollView>
  );
};

const Field = ({ label, multiline, required, ...props }) => (
  <View style={styles.field}>
    <Text style={styles.label}>{label}{required ? " *" : ""}</Text>
    <TextInput {...props} multiline={multiline} maxLength={5000} style={[styles.input, multiline && styles.multiline]} />
  </View>
);

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  content: { padding: spacing.lg },
  label: { color: colors.text, fontWeight: "700", marginBottom: spacing.sm },
  studentList: { flexDirection: "row", flexWrap: "wrap", gap: spacing.sm, marginBottom: spacing.lg },
  student: { borderWidth: 1, borderColor: colors.border, borderRadius: 20, paddingHorizontal: spacing.md, paddingVertical: 10 },
  studentSelected: { backgroundColor: colors.primary, borderColor: colors.primary },
  studentText: { color: colors.text },
  studentTextSelected: { color: colors.onPrimary, fontWeight: "700" },
  field: { marginBottom: spacing.lg },
  input: { borderWidth: 1, borderColor: colors.border, borderRadius: radius.md, backgroundColor: colors.surface, padding: spacing.md },
  multiline: { minHeight: 100, textAlignVertical: "top" },
  button: { backgroundColor: colors.primary, minHeight: minimumTouchSize, borderRadius: radius.md, alignItems: "center", justifyContent: "center" },
  buttonText: { color: colors.onPrimary, fontWeight: "700" },
  disabled: { opacity: 0.6 },
});

export default CreateWeeklyReportScreen;
