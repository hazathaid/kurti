import { useEffect, useMemo, useRef, useState } from "react";
import {
  Alert,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View,
} from "react-native";
import api from "../api/client";
import { getApiError } from "../api/errors";
import { LoadingState } from "../components/StateViews";
import { colors, minimumTouchSize, radius, spacing } from "../theme/tokens";

const emptyActivity = () => ({ aktivitas: "", amanah_rumah: "", capaian: "" });

const CreateMultipleKurtiScreen = ({ navigation, route }) => {
  const { muridId, classroomId } = route.params;
  const [bulan, setBulan] = useState("");
  const [pekan, setPekan] = useState("");
  const [activities, setActivities] = useState([emptyActivity()]);
  const [errors, setErrors] = useState({});
  const [saving, setSaving] = useState(false);
  const activityRefs = useRef([]);
  const leavingAfterSave = useRef(false);

  const isDirty = useMemo(
    () =>
      Boolean(
        bulan ||
          pekan ||
          activities.some((item) =>
            Object.values(item).some((value) => value.length > 0),
          ),
      ),
    [activities, bulan, pekan],
  );

  useEffect(() => {
    const unsubscribe = navigation.addListener("beforeRemove", (event) => {
      if (!isDirty || leavingAfterSave.current) return;

      event.preventDefault();
      Alert.alert(
        "Buang perubahan?",
        "Data yang belum disimpan akan hilang.",
        [
          { text: "Tetap di Form", style: "cancel" },
          {
            text: "Buang",
            style: "destructive",
            onPress: () => {
              leavingAfterSave.current = true;
              navigation.dispatch(event.data.action);
            },
          },
        ],
      );
    });

    return unsubscribe;
  }, [isDirty, navigation]);

  const clearError = (key) => {
    setErrors((current) => {
      if (!current[key]) return current;
      const next = { ...current };
      delete next[key];
      return next;
    });
  };

  const addRow = () => setActivities((current) => [...current, emptyActivity()]);

  const removeRow = (index) => {
    if (activities.length === 1) return;

    const remove = () => {
      setActivities((current) => current.filter((_, rowIndex) => rowIndex !== index));
      setErrors({});
    };
    const rowHasContent = Object.values(activities[index]).some((value) => value.trim());

    if (rowHasContent) {
      Alert.alert("Hapus aktivitas?", "Isian pada baris ini akan hilang.", [
        { text: "Batal", style: "cancel" },
        { text: "Hapus", style: "destructive", onPress: remove },
      ]);
    } else {
      remove();
    }
  };

  const updateActivity = (index, field, value) => {
    setActivities((current) =>
      current.map((item, rowIndex) =>
        rowIndex === index ? { ...item, [field]: value } : item,
      ),
    );
    clearError(`activities.${index}.${field}`);
  };

  const validate = () => {
    const nextErrors = {};
    if (!/^\d{4}-(0[1-9]|1[0-2])$/.test(bulan.trim())) {
      nextErrors.bulan = "Gunakan format YYYY-MM, misalnya 2026-07.";
    }
    if (!/^[1-5]$/.test(pekan.trim())) {
      nextErrors.pekan = "Pekan harus berupa angka 1 sampai 5.";
    }
    activities.forEach((item, index) => {
      if (!item.aktivitas.trim()) {
        nextErrors[`activities.${index}.aktivitas`] = "Aktivitas wajib diisi.";
      }
    });

    setErrors(nextErrors);
    if (nextErrors.bulan) bulanRef.current?.focus();
    else if (nextErrors.pekan) pekanRef.current?.focus();
    else {
      const invalidIndex = activities.findIndex((item) => !item.aktivitas.trim());
      if (invalidIndex >= 0) activityRefs.current[invalidIndex]?.focus();
    }
    return Object.keys(nextErrors).length === 0;
  };

  const bulanRef = useRef(null);
  const pekanRef = useRef(null);

  const handleSaveAll = async () => {
    if (saving || !validate()) return;

    const trimmedBulan = bulan.trim();
    const trimmedPekan = pekan.trim();
    const kurtis = activities.map((item) => ({
      bulan: trimmedBulan,
      pekan: trimmedPekan,
      aktivitas: item.aktivitas.trim(),
      amanah_rumah: item.amanah_rumah.trim(),
      capaian: item.capaian.trim(),
    }));

    setSaving(true);
    try {
      const { data } = await api.post("/kurtis", {
        murid_id: muridId,
        classroom_id: classroomId,
        kurtis,
      });
      if (data.status !== "success") throw new Error("Unexpected create response");

      leavingAfterSave.current = true;
      Alert.alert("Berhasil", "Semua aktivitas Kurti berhasil disimpan.", [
        {
          text: "OK",
          onPress: () =>
            navigation.navigate({
              name: "DashboardFasil",
              params: { kurtiCreatedAt: Date.now() },
              merge: true,
            }),
        },
      ]);
    } catch (error) {
      const apiError = getApiError(error);
      if (apiError.type === "validation") {
        const validationErrors = {};
        Object.entries(apiError.errors).forEach(([key, messages]) => {
          const localKey = key
            .replace(/^kurtis\.\d+\.bulan$/, "bulan")
            .replace(/^kurtis\.\d+\.pekan$/, "pekan")
            .replace(/^kurtis\.(\d+)\.(.+)$/, "activities.$1.$2");
          validationErrors[localKey] = Array.isArray(messages) ? messages[0] : messages;
        });
        setErrors(validationErrors);
      }
      Alert.alert("Gagal Menyimpan", apiError.message);
    } finally {
      setSaving(false);
    }
  };

  const renderError = (key) =>
    errors[key] ? <Text style={styles.fieldError}>{errors[key]}</Text> : null;

  return (
    <KeyboardAvoidingView style={styles.screen} behavior={Platform.OS === "ios" ? "padding" : undefined}>
      <ScrollView contentContainerStyle={styles.container} keyboardShouldPersistTaps="handled">
      <View style={styles.groupBox}>
        <Text style={styles.sectionTitle}>Periode</Text>
        <Text style={styles.label}>Bulan *</Text>
        <TextInput
          ref={bulanRef}
          style={[styles.input, errors.bulan && styles.inputError]}
          value={bulan}
          onChangeText={(value) => {
            setBulan(value);
            clearError("bulan");
          }}
          placeholder="YYYY-MM"
          autoCapitalize="none"
        />
        {renderError("bulan")}

        <Text style={styles.label}>Pekan *</Text>
        <TextInput
          ref={pekanRef}
          style={[styles.input, errors.pekan && styles.inputError]}
          value={pekan}
          onChangeText={(value) => {
            setPekan(value);
            clearError("pekan");
          }}
          placeholder="1-5"
          keyboardType="number-pad"
          maxLength={1}
        />
        {renderError("pekan")}
      </View>

      {activities.map((item, index) => {
        const activityErrorKey = `activities.${index}.aktivitas`;
        return (
          <View key={index} style={styles.rowBox}>
            <Text style={styles.sectionTitle}>Aktivitas {index + 1}</Text>
            <Text style={styles.label}>Aktivitas *</Text>
            <TextInput
              ref={(ref) => {
                activityRefs.current[index] = ref;
              }}
              style={[styles.input, errors[activityErrorKey] && styles.inputError]}
              value={item.aktivitas}
              onChangeText={(value) => updateActivity(index, "aktivitas", value)}
              placeholder="Masukkan aktivitas"
            />
            {renderError(activityErrorKey)}

            <Text style={styles.label}>Amanah Rumah</Text>
            <TextInput
              style={styles.input}
              value={item.amanah_rumah}
              onChangeText={(value) => updateActivity(index, "amanah_rumah", value)}
              placeholder="Opsional"
            />

            <Text style={styles.label}>Capaian</Text>
            <TextInput
              style={styles.input}
              value={item.capaian}
              onChangeText={(value) => updateActivity(index, "capaian", value)}
              placeholder="Opsional"
            />

            {activities.length > 1 ? (
              <TouchableOpacity onPress={() => removeRow(index)} style={styles.removeButton} accessibilityRole="button" accessibilityLabel={`Hapus aktivitas ${index + 1}`}>
                <Text style={styles.buttonText}>Hapus Aktivitas</Text>
              </TouchableOpacity>
            ) : null}
          </View>
        );
      })}

      <TouchableOpacity onPress={addRow} style={styles.addButton} disabled={saving} accessibilityRole="button" accessibilityLabel="Tambah baris aktivitas">
        <Text style={styles.buttonText}>Tambah Aktivitas</Text>
      </TouchableOpacity>

      <TouchableOpacity
        onPress={handleSaveAll}
        style={[styles.saveButton, saving && styles.disabledButton]}
        disabled={saving}
        accessibilityRole="button"
        accessibilityLabel="Simpan semua aktivitas Kurti"
      >
        {saving ? <LoadingState message="" color={colors.onPrimary} /> : null}
        <Text style={styles.buttonText}>{saving ? "Menyimpan..." : "Simpan Semua"}</Text>
      </TouchableOpacity>
      </ScrollView>
    </KeyboardAvoidingView>
  );
};

export default CreateMultipleKurtiScreen;

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.background },
  container: { padding: spacing.lg, backgroundColor: colors.background },
  groupBox: { marginBottom: spacing.lg, padding: spacing.md, borderRadius: radius.md, backgroundColor: colors.surface },
  rowBox: { marginBottom: spacing.lg, padding: spacing.md, borderWidth: 1, borderRadius: radius.md, borderColor: colors.border, backgroundColor: colors.surface },
  sectionTitle: { fontSize: 17, fontWeight: "700", color: colors.text },
  label: { fontWeight: "600", marginTop: spacing.md, color: colors.text },
  input: { borderWidth: 1, borderColor: colors.border, borderRadius: radius.sm, padding: 10, marginTop: spacing.xs, color: colors.text },
  inputError: { borderColor: colors.danger },
  fieldError: { color: colors.danger, fontSize: 12, marginTop: spacing.xs },
  addButton: { backgroundColor: colors.primary, minHeight: minimumTouchSize, justifyContent: "center", borderRadius: radius.sm, alignItems: "center", marginBottom: spacing.md },
  saveButton: { flexDirection: "row", gap: spacing.sm, justifyContent: "center", backgroundColor: colors.success, minHeight: minimumTouchSize, borderRadius: radius.sm, alignItems: "center", marginBottom: 40 },
  disabledButton: { opacity: 0.6 },
  removeButton: { backgroundColor: colors.danger, minHeight: minimumTouchSize, justifyContent: "center", borderRadius: radius.sm, alignItems: "center", marginTop: spacing.md },
  buttonText: { color: colors.onPrimary, fontWeight: "700" },
});
