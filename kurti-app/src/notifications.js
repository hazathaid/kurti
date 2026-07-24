import Constants from "expo-constants";
import * as Notifications from "expo-notifications";
import { Platform } from "react-native";
import api from "./api/client";

Notifications.setNotificationHandler({
  handleNotification: async () => ({
    shouldPlaySound: true,
    shouldSetBadge: false,
    shouldShowBanner: true,
    shouldShowList: true,
  }),
});

export const registerForPushNotifications = async () => {
  try {
    if (Platform.OS === "android") {
      await Notifications.setNotificationChannelAsync("default", {
        name: "Kurti",
        importance: Notifications.AndroidImportance.DEFAULT,
      });
    }

    const currentPermissions = await Notifications.getPermissionsAsync();
    const permissions = currentPermissions.status === "undetermined"
      ? await Notifications.requestPermissionsAsync()
      : currentPermissions;

    if (permissions.status !== "granted") return;

    const projectId = Constants.expoConfig?.extra?.eas?.projectId
      || Constants.easConfig?.projectId;
    const token = (await Notifications.getExpoPushTokenAsync({ projectId })).data;

    await api.post("/save-fcm-token", { fcm_token: token });
  } catch {
    // Notification setup is optional and must not interrupt the signed-in session.
  }
};

export const getNotificationRoute = (response) => {
  const data = response?.notification?.request?.content?.data;
  if (data?.weeklyReportId != null) {
    return {
      name: "WeeklyReports",
      params: { reportId: data.weeklyReportId },
    };
  }
  if (data?.muridId == null || data?.groupId == null) return null;

  return {
    name: "KurtiDetail",
    params: {
      muridId: data.muridId,
      groupId: data.groupId,
    },
  };
};

export { Notifications };
