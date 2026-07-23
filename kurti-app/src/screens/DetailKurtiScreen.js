import { useCallback, useContext, useEffect, useRef, useState } from "react";
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
import { List } from "react-native-paper";
import api from "../api/client";
import { getApiError, getApiErrorMessage } from "../api/errors";
import { EmptyState, ErrorState, LoadingState } from "../components/StateViews";
import { AuthContext } from "../contexts/AuthContext";
import { colors, minimumTouchSize, radius, spacing } from "../theme/tokens";

const DetailKurtiScreen = ({ route }) => {
  const { muridId, groupId } = route.params;
  const { user } = useContext(AuthContext);

  const [kurtiData, setKurtiData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [expandedId, setExpandedId] = useState(null);
  const [groupInfo, setGroupInfo] = useState(null);
  const [muridInfo, setMuridInfo] = useState(null);
  const [loadError, setLoadError] = useState(null);
  const [savingIds, setSavingIds] = useState({});
  const [catatanMap, setCatatanMap] = useState({});
  const requestIdRef = useRef(0);

  const fetchKurtiDetail = useCallback(async () => {
    const requestId = ++requestIdRef.current;
    setLoading(true);
    setLoadError(null);

    try {
      const response = await api.get(`/kurtis/${muridId}/${groupId}`);
      if (requestId !== requestIdRef.current) return;

      const json = response.data;

      if (json.group) {
        const kurtis = json.group.kurtis || [];
        setKurtiData(kurtis);
        setGroupInfo({ bulan: json.group.bulan, pekan: json.group.pekan });
        setMuridInfo(json.murid);

        const initialCatatan = {};
        kurtis.forEach((k) => {
          initialCatatan[k.id] = k.catatan_orangtua || "";
        });
        setCatatanMap(initialCatatan);
      } else {
        setLoadError({
          type: "notFound",
          message: "Data grup tidak ditemukan.",
        });
      }
    } catch (error) {
      if (requestId === requestIdRef.current) {
        setLoadError(getApiError(error));
      }
    } finally {
      if (requestId === requestIdRef.current) {
        setLoading(false);
      }
    }
  }, [groupId, muridId]);

  useEffect(() => {
    setKurtiData([]);
    setGroupInfo(null);
    setMuridInfo(null);
    setCatatanMap({});
    setSavingIds({});
    setExpandedId(null);
    setLoadError(null);
    fetchKurtiDetail();

    return () => {
      requestIdRef.current += 1;
    };
  }, [fetchKurtiDetail]);

  const handleSave = async (kurtiId) => {
    if (savingIds[kurtiId]) return;

    const draft = catatanMap[kurtiId] ?? "";
    setSavingIds((current) => ({ ...current, [kurtiId]: true }));

    try {
      const response = await api.put(`/kurtis/${kurtiId}/catatan`, {
        catatan_orangtua: draft,
      });
      const savedNote = response.data?.data?.catatan_orang_tua ?? draft;

      setCatatanMap((current) => ({ ...current, [kurtiId]: savedNote }));
      setKurtiData((current) =>
        current.map((kurti) =>
          kurti.id === kurtiId
            ? { ...kurti, catatan_orangtua: savedNote }
            : kurti,
        ),
      );

      Alert.alert("Berhasil", "Catatan orang tua berhasil disimpan");
    } catch (error) {
      Alert.alert("Error", getApiErrorMessage(error));
    } finally {
      setSavingIds((current) => {
        const next = { ...current };
        delete next[kurtiId];
        return next;
      });
    }
  };

  if (loading) {
    return <LoadingState message="Memuat detail Kurti..." fullScreen />;
  }

  if (loadError) {
    const title =
      loadError.type === "forbidden"
        ? "Akses detail ditolak"
        : loadError.type === "notFound"
          ? "Detail tidak ditemukan"
          : "Detail belum dapat dimuat";

    return (
      <ErrorState
        title={title}
        message={loadError.message}
        onRetry={fetchKurtiDetail}
      />
    );
  }

  return (
    <KeyboardAvoidingView style={styles.container} behavior={Platform.OS === "ios" ? "padding" : undefined}>
      <ScrollView contentContainerStyle={styles.content} keyboardShouldPersistTaps="handled">
        {muridInfo && (
          <Text style={styles.header}>
            {muridInfo.name} - Bulan {groupInfo?.bulan}, Pekan {groupInfo?.pekan}
          </Text>
        )}

        {kurtiData.length === 0 ? (
          <EmptyState title="Belum ada data Kurti" description="Aktivitas untuk periode ini belum tersedia." />
        ) : (
          <List.Section>
            {kurtiData.map((kurti) => {
              const isSaving = Boolean(savingIds[kurti.id]);
              const activityLabel = kurti.aktivitas || "-";

              return (
                <List.Accordion
                  key={`kurti-${kurti.id}`}
                  title={`Aktivitas: ${activityLabel}`}
                  left={(props) => <List.Icon {...props} icon="book" />}
                  expanded={expandedId === kurti.id}
                  onPress={() =>
                    setExpandedId((current) =>
                      current === kurti.id ? null : kurti.id,
                    )
                  }
                  accessibilityLabel={`Buka detail aktivitas ${activityLabel}`}
                >
                  <View style={styles.detailBox}>
                    <Text style={styles.label}>Amanah Rumah:</Text>
                    <Text>{kurti.amanah_rumah || "-"}</Text>

                    <Text style={styles.label}>Capaian:</Text>
                    <Text>{kurti.capaian || "-"}</Text>

                    <Text style={styles.label}>Catatan Orang Tua:</Text>
                    {user?.type !== "orangtua" ? (
                      <Text style={styles.readonly}>
                        {kurti.catatan_orangtua || "-"}
                      </Text>
                    ) : (
                      <TextInput
                        style={styles.input}
                        value={catatanMap[kurti.id] ?? ""}
                        onChangeText={(text) =>
                          setCatatanMap((current) => ({
                            ...current,
                            [kurti.id]: text,
                          }))
                        }
                        placeholder="Masukkan catatan orang tua"
                        maxLength={255}
                        multiline
                        editable={!isSaving}
                        accessibilityLabel={`Catatan orang tua untuk aktivitas ${activityLabel}`}
                      />
                    )}

                    {user?.type === "orangtua" && (
                      <TouchableOpacity
                        style={[styles.button, isSaving && styles.buttonDisabled]}
                        onPress={() => handleSave(kurti.id)}
                        disabled={isSaving}
                        accessibilityRole="button"
                        accessibilityState={{ disabled: isSaving, busy: isSaving }}
                        accessibilityLabel={`Simpan catatan untuk aktivitas ${activityLabel}`}
                      >
                        <Text style={styles.buttonText}>
                          {isSaving ? "Menyimpan..." : "Simpan"}
                        </Text>
                      </TouchableOpacity>
                    )}
                  </View>
                </List.Accordion>
              );
            })}
          </List.Section>
        )}
      </ScrollView>
    </KeyboardAvoidingView>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  content: { flexGrow: 1, padding: spacing.lg },
  header: { fontSize: 18, fontWeight: "bold", marginBottom: 12 },
  detailBox: { padding: spacing.md, backgroundColor: colors.surface, borderRadius: radius.md },
  label: { fontWeight: "bold", marginTop: 8 },
  readonly: {
    padding: 8,
    backgroundColor: "#f0f0f0",
    borderRadius: 6,
    color: "#333",
  },
  input: {
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 6,
    padding: 8,
    minHeight: 60,
    textAlignVertical: "top",
    marginTop: 4,
  },
  button: {
    backgroundColor: colors.success,
    minHeight: minimumTouchSize,
    justifyContent: "center",
    borderRadius: 6,
    alignItems: "center",
    marginTop: 12,
  },
  buttonDisabled: { backgroundColor: colors.border },
  buttonText: { color: "#fff", fontWeight: "bold", fontSize: 16 },
});

export default DetailKurtiScreen;
