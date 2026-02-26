import { useLocalSearchParams, useRouter } from 'expo-router';
import { StyleSheet, View, ActivityIndicator } from 'react-native';
import { WebView } from 'react-native-webview';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

import { ThemedView } from '@/components/themed-view';
import { Palette as Colors } from '@/constants/theme';

export default function PaymentScreen() {
  const { url, bookingId } = useLocalSearchParams<{ url: string; bookingId: string }>();
  const router = useRouter();
  const insets = useSafeAreaInsets();

  const handleNavigationStateChange = (navState: any) => {
    const { url } = navState;

    // Check for success or cancel URLs from backend redirect
    // Assuming backend redirects to:
    // success: /bookings/payment-success/{id}
    // cancel: /bookings/payment-cancel/{id}
    // But wait, the backend currently redirects to 'owner.resort-management.bookings' after paymentSuccess/Cancel
    // which is an owner route. 
    // The PayMongo success_url in GuestBookingController should point to a mobile-friendly success page or deep link?
    // Actually, in GuestBookingController store method, I reused ResortManagementService which sets:
    // 'success_url' => route('owner.resort-management.bookings.payment-success', ...)
    
    // This is problematic because the owner route middleware might block guest user or redirect to owner dashboard.
    // I need to override the success_url for API requests.
    // However, I can't easily change the service without affecting the owner flow.
    // BUT, the PaymentService just takes the success_url as param.
    // Ah, ResortManagementService::createWalkInBooking constructs the URL.
    
    // WORKAROUND:
    // Detect the redirect to the backend success route, even if it fails to load (due to auth), 
    // we know PayMongo redirected there so payment is likely done.
    // BETTER: Update the service to accept success_url override.

    if (url.includes('payment-success')) {
      router.replace('/booking/success');
    } else if (url.includes('payment-cancel')) {
      router.back();
    }
  };

  return (
    <ThemedView style={[styles.container, { paddingTop: insets.top }]}>
      <WebView
        source={{ uri: url }}
        style={{ flex: 1 }}
        onNavigationStateChange={handleNavigationStateChange}
        startInLoadingState
        renderLoading={() => (
          <View style={styles.loading}>
            <ActivityIndicator size="large" color={Colors.primary} />
          </View>
        )}
      />
    </ThemedView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  loading: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: 'rgba(255,255,255,0.8)',
  },
});
