import { createNavigationContainerRef, NavigationContainer } from "@react-navigation/native";
import { createNativeStackNavigator } from "@react-navigation/native-stack";
import { useContext, useEffect, useRef } from "react";
import Toast from "react-native-toast-message";
import { LoadingState } from "./src/components/StateViews";
import { AuthContext, AuthProvider } from "./src/contexts/AuthContext";
import {
  getNotificationRoute,
  Notifications,
  registerForPushNotifications,
} from "./src/notifications";

// screens
import CreateMultipleKurtiScreen from "./src/screens/CreateMultipleKurtiScreen";
import DashboardFasil from "./src/screens/DashboardFasil";
import DashboardOrtu from "./src/screens/DashboardOrtu";
import KurtiDetail from "./src/screens/DetailKurtiScreen";
import LoginScreen from "./src/screens/LoginScreen";
import WeeklyReportsScreen from "./src/screens/WeeklyReportsScreen";
import CreateWeeklyReportScreen from "./src/screens/CreateWeeklyReportScreen";

const Stack = createNativeStackNavigator();
const navigationRef = createNavigationContainerRef();

function NotificationBridge() {
  const { user } = useContext(AuthContext);
  const handledNotificationId = useRef(null);

  useEffect(() => {
    if (!user) return;

    registerForPushNotifications();

    const openNotification = (response) => {
      const notificationId = response?.notification?.request?.identifier;
      const route = getNotificationRoute(response);

      if (!route || notificationId === handledNotificationId.current) return;
      handledNotificationId.current = notificationId;

      if (navigationRef.isReady()) {
        navigationRef.navigate(route.name, route.params);
        Notifications.clearLastNotificationResponseAsync();
      }
    };

    Notifications.getLastNotificationResponseAsync().then(openNotification);
    const subscription = Notifications.addNotificationResponseReceivedListener(openNotification);

    return () => subscription.remove();
  }, [user]);

  return null;
}

function RootNavigator() {
  const { user, loading } = useContext(AuthContext);

  if (loading) {
    return <LoadingState message="Memuat sesi..." fullScreen light />;
  }

  return (
    <Stack.Navigator>
      {user ? (
        user?.type === "orangtua" ? (
          <>
            <Stack.Screen
              name="DashboardOrtu"
              component={DashboardOrtu}
            />
            <Stack.Screen name="WeeklyReport" component={WeeklyReportScreen} />
            <Stack.Screen name="KurtiDetail" component={KurtiDetail} />
            <Stack.Screen name="WeeklyReports" component={WeeklyReportsScreen} options={{ title: "Weekly Report" }} />
          </>
        ) : (
          <>
            <Stack.Screen
              name="DashboardFasil"
              component={DashboardFasil}
            />
            <Stack.Screen name="WeeklyReport" component={WeeklyReportScreen} />
            <Stack.Screen name="KurtiDetail" component={KurtiDetail} />
            <Stack.Screen name="CreateMultipleKurti" component={CreateMultipleKurtiScreen} />
            <Stack.Screen name="WeeklyReports" component={WeeklyReportsScreen} options={{ title: "Weekly Report" }} />
            <Stack.Screen name="CreateWeeklyReport" component={CreateWeeklyReportScreen} options={{ title: "Buat Weekly Report" }} />
          </>
        )
      ) : (
        <Stack.Screen
          name="Login"
          component={LoginScreen}
          options={{ headerShown: false }}
        />
      )}
    </Stack.Navigator>
  );
}

export default function App() {
  return (
    <AuthProvider>
      <NavigationContainer ref={navigationRef}>
        <NotificationBridge />
        <RootNavigator />
      </NavigationContainer>
      <Toast />
    </AuthProvider>
  );
}
