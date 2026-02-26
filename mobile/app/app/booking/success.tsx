import { useRouter } from 'expo-router';
import { StyleSheet, View } from 'react-native';
import { Image } from 'expo-image';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

import { ThemedText } from '@/components/themed-text';
import { ThemedView } from '@/components/themed-view';
import { Button } from '@/components/ui/Button';
import { Spacing, Fonts, Palette as Colors } from '@/constants/theme';

export default function SuccessScreen() {
  const router = useRouter();
  const insets = useSafeAreaInsets();

  return (
    <ThemedView style={[styles.container, { paddingTop: insets.top, paddingBottom: insets.bottom }]}>
      <View style={styles.content}>
        <View style={styles.iconContainer}>
          <Image
            source={require('@/assets/images/reservation/welcome.png')} // Placeholder
            style={styles.image}
            contentFit="contain"
          />
        </View>
        
        <ThemedText type="title" style={styles.title}>Booking Confirmed!</ThemedText>
        <ThemedText style={styles.message}>
          Your booking has been successfully placed. We have sent a confirmation email to your inbox.
        </ThemedText>

        <View style={styles.buttonContainer}>
          <Button title="Go to Home" onPress={() => router.replace('/(tabs)')} />
        </View>
      </View>
    </ThemedView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  content: {
    flex: 1,
    padding: Spacing.xl,
    justifyContent: 'center',
    alignItems: 'center',
  },
  iconContainer: {
    marginBottom: Spacing.xl,
  },
  image: {
    width: 200,
    height: 200,
  },
  title: {
    fontFamily: Fonts.rounded,
    fontSize: 28,
    textAlign: 'center',
    marginBottom: Spacing.md,
    color: Colors.primary,
  },
  message: {
    textAlign: 'center',
    color: Colors.gray,
    fontSize: 16,
    lineHeight: 24,
    marginBottom: Spacing.xxl,
  },
  buttonContainer: {
    width: '100%',
  },
});
