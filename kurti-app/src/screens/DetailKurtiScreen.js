import { useCallback, useContext, useEffect, useState } from "react";
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
import { getApiErrorMessage } from "../api/errors";
import { EmptyState, ErrorState, LoadingState } from "../components/StateViews";
import { AuthContext } from "../contexts/AuthContext";
import { colors, minimumTouchSize, radius, spacing } from "../theme/tokens";

const DetailKurtiScreen = ({ route }) => {
  const { muridId, groupId } = route.params;
  const { user } = useContext(AuthContext);

  const [kurtiData, setKurtiData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [expanded, setExpanded] = useState(false);
  const [groupInfo, setGroupInfo] = useState(null);
  const [muridInfo, setMuridInfo] = useState(null);
  const [errorMessage, setErrorMessage] = useState("");

  const [savingId, setSavingId] = useState(null); // untuk track kurti mana yg disave
  const [catatanMap, setCatatanMap] = useState({}); // simpan catatan per kurti

  const fetchKurtiDetail = useCallback(async () => {
    setLoading(true);
    setErrorMessage("");
    try {
      const response = await api.get(`/kurtis/${muridId}/${groupId}`);
      const json = response.data;

      if (json.group) {
        setKurtiData(json.group.kurtis || []);
        setGroupInfo({ bulan: json.group.bulan, pekan: json.group.pekan });
        setMuridInfo(json.murid);

        // isi catatan awal ke map
        const initialCatatan = {};
        (json.group.kurtis || []).forEach((k) => {
          initialCatatan[k.id] = k.catatan_orangtua || "";
        });
        setCatatanMap(initialCatatan);
      } else {
        setErrorMessage("Data grup tidak ditemukan.");
      }
    } catch (error) {
      setErrorMessage(getApiErrorMessage(error));
    } finally {
      setLoading(false);
    }
  }, [groupId, muridId]);

  useEffect(() => {
    fetchKurtiDetail();
  }, [fetchKurtiDetail]);

  const handleSave = async (kurtiId) => {
    try {
      setSavingId(kurtiId);
      await api.put(`/kurtis/${kurtiId}/catatan`, {
        catatan_orangtua: catatanMap[kurtiId],
      });

      Alert.alert("Berhasil", "Catatan orang tua berhasil disimpan");
    } catch (error) {
      Alert.alert("Error", getApiErrorMessage(error));
    } finally {
      setSavingId(null);
    }
  };

  if (loading) {
    return <LoadingState message="Memuat detail Kurti..." fullScreen />;
  }

  if (errorMessage) {
    return <ErrorState title="Detail belum dapat dimuat" message={errorMessage} onRetry={fetchKurtiDetail} />;
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
          {kurtiData.map((kurti, index) => (
            <List.Accordion
              key={`kurti-${kurti.id}`}
              title={`Aktivitas: ${kurti.aktivitas}`}
              left={(props) => <List.Icon {...props} icon="book" />}
              expanded={expanded === index}
              onPress={() => setExpanded(expanded === index ? false : index)}
              accessibilityLabel={`Buka detail aktivitas ${kurti.aktivitas}`}
            >
              <View style={styles.detailBox}>
                <Text style={styles.label}>Amanah Rumah:</Text>
                <Text>{kurti.amanah_rumah || "-"}</Text>

                <Text style={styles.label}>Capaian:</Text>
                <Text>{kurti.capaian || "-"}</Text>

                <Text style={styles.label}>Catatan Orang Tua:</Text>
                {user?.type === "fasil" ? (
                  <Text style={styles.readonly}>
                    {kurti.catatan_orangtua ?? "-"}
                  </Text>
                ) : (
                  <TextInput
                    style={styles.input}
                    value={catatanMap[kurti.id]}
                    onChangeText={(text) =>
                      setCatatanMap((prev) => ({ ...prev, [kurti.id]: text }))
                    }
                    placeholder="Masukkan catatan orang tua"
                    multiline
                  />
                )}

                {user?.type === "orangtua" && (
                  <TouchableOpacity
                    style={[
                      styles.button,
                      savingId === kurti.id && { backgroundColor: "#ccc" },
                    ]}
                    onPress={() => handleSave(kurti.id)}
                    disabled={savingId === kurti.id}
                    accessibilityRole="button"
                    accessibilityLabel={`Simpan catatan untuk aktivitas ${kurti.aktivitas}`}
                  >
                    <Text style={styles.buttonText}>
                      {savingId === kurti.id ? "Menyimpan..." : "Simpan"}
                    </Text>
                  </TouchableOpacity>
                )}
              </View>
            </List.Accordion>
          ))}
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
  buttonText: { color: "#fff", fontWeight: "bold", fontSize: 16 },
});

export default DetailKurtiScreen;
