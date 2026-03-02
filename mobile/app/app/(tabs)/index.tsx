import { StyleSheet, View, ScrollView } from 'react-native';
import { useRouter } from 'expo-router';
import { Image } from 'expo-image';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { WebView } from 'react-native-webview';

import { ThemedText } from '@/components/themed-text';
import { ThemedView } from '@/components/themed-view';
import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/Button';
import { Palette as Colors, Spacing, Fonts } from '@/constants/theme';

const FEATURED_SERVICES = [
  { id: '1', title: 'Rooms', image: require('@/assets/images/reservation/service-option1.png'), route: '/(tabs)/explore' },
  { id: '2', title: 'Events', image: require('@/assets/images/reservation/service-option2.png'), route: '/(tabs)/explore' },
  { id: '3', title: 'Pool', image: require('@/assets/images/reservation/service-option4.png'), route: '/(tabs)/explore' },
];

const MAP_URL = 'https://www.bing.com/maps/embed?h=400&w=500&cp=9.324678~123.302822&lvl=16&typ=d&sty=r&src=SHELL&FORM=MBEDV8';
// Using the embed version or similar for better display if possible, but the user gave a full search URL.
// The user's URL: https://www.bing.com/maps/search?v=2&pc=FACEBK&mid=8100&mkt=en-US&fbclid=IwY2xjawQSpXRleHRuA2FlbQIxMABicmlkETFkd2RZdk9RRTVaV3VJaDVkc3J0YwZhcHBfaWQQMjIyMDM5MTc4ODIwMDg5MgABHpm_hbHmON4aMmPln14UMtVaKhEmBYLNlIznejGrVsQyvH4knMOrr7HP6_zn_aem_YaxTNXtYBAMR8nDBgB_WXg&FORM=FBKPL1&q=48+DB+Catapusan+St.%2C+Piapi%2C+Dumaguete+City%2C+Philippines%2C+6200&cp=9.324678%7E123.302822&lvl=16&style=r
// Let's use the provided URL directly as requested.
const USER_MAP_URL = 'https://www.bing.com/maps?cp=9.324678~123.302822&lvl=18&sp=point.9.324678_123.302822_Janilyn%27s%20Place&sty=r';

export default function HomeScreen() {
  const { user, signOut } = useAuth();
  const router = useRouter();
  const insets = useSafeAreaInsets();

  return (
    <ThemedView style={[styles.container, { paddingTop: insets.top }]}>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        {/* Header Section */}
        <View style={styles.header}>
          <View>
            <ThemedText style={styles.greeting}>Welcome back,</ThemedText>
            <ThemedText type="title" style={styles.username}>{user?.name || 'Guest'}</ThemedText>
          </View>
          <Button 
            title="Log Out" 
            variant="outline"
            onPress={signOut}
            style={styles.logoutButton}
          />
        </View>

        {/* Hero Section */}
        <View style={styles.hero}>
          <Image
            source={require('@/assets/images/reservation/welcome.png')}
            style={styles.heroImage}
            contentFit="cover"
          />
          <View style={styles.heroOverlay}>
            <ThemedText type="title" style={styles.heroTitle}>Book Your Perfect Stay</ThemedText>
            <ThemedText style={styles.heroSubtitle}>Experience luxury and comfort in Dumaguete</ThemedText>
            <Button 
              title="Book Now" 
              onPress={() => router.push('/(tabs)/explore')} 
              style={styles.heroButton}
            />
          </View>
        </View>

        {/* Featured Services */}
        <View style={styles.section}>
          <ThemedText type="subtitle" style={styles.sectionTitle}>Our Services</ThemedText>
          <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.servicesScroll}>
            {FEATURED_SERVICES.map((service) => (
              <View key={service.id} style={styles.serviceCard}>
                <Image source={service.image} style={styles.serviceImage} />
                <ThemedText style={styles.serviceTitle}>{service.title}</ThemedText>
              </View>
            ))}
          </ScrollView>
        </View>

        {/* Quick Actions */}
        <View style={styles.section}>
          <ThemedText type="subtitle" style={styles.sectionTitle}>Quick Actions</ThemedText>
          <View style={styles.actionsGrid}>
            <Button 
              title="View Rooms" 
              variant="secondary" 
              onPress={() => router.push('/(tabs)/explore')} 
              style={styles.actionButton}
            />
             <Button 
              title="My Bookings" 
              variant="outline" 
              onPress={() => { /* Navigate to bookings list if implemented */ }} 
              style={styles.actionButton}
            />
          </View>
        </View>

        {/* Location Map */}
        <View style={styles.section}>
          <ThemedText type="subtitle" style={styles.sectionTitle}>Our Location</ThemedText>
          <ThemedText style={styles.addressText}>48 DB Catapusan St., Piapi, Dumaguete City</ThemedText>
          <View style={styles.mapContainer}>
             <WebView
              source={{ uri: USER_MAP_URL }}
              style={styles.map}
              scrollEnabled={false}
              scalesPageToFit={true}
            />
          </View>
        </View>

      </ScrollView>
    </ThemedView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  scrollContent: {
    paddingBottom: Spacing.xl,
  },
  addressText: {
    fontSize: 14,
    color: Colors.gray,
    marginBottom: Spacing.sm,
  },
  mapContainer: {
    height: 300,
    borderRadius: 16,
    overflow: 'hidden',
    backgroundColor: '#f0f0f0',
    borderWidth: 1,
    borderColor: '#e2e8f0',
  },
  map: {
    flex: 1,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: Spacing.lg,
    paddingVertical: Spacing.md,
  },
  greeting: {
    fontSize: 14,
    color: Colors.gray,
  },
  username: {
    fontFamily: Fonts.rounded,
    fontSize: 20,
  },
  logoutButton: {
    height: 36,
    paddingHorizontal: Spacing.md,
  },
  hero: {
    margin: Spacing.lg,
    height: 200,
    borderRadius: 16,
    overflow: 'hidden',
    position: 'relative',
  },
  heroImage: {
    width: '100%',
    height: '100%',
  },
  heroOverlay: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: 'rgba(0,0,0,0.3)',
    padding: Spacing.lg,
    justifyContent: 'center',
    alignItems: 'flex-start',
  },
  heroTitle: {
    color: '#fff',
    fontSize: 24,
    marginBottom: Spacing.xs,
  },
  heroSubtitle: {
    color: '#fff',
    fontSize: 14,
    marginBottom: Spacing.md,
    opacity: 0.9,
  },
  heroButton: {
    height: 40,
    paddingHorizontal: Spacing.xl,
  },
  section: {
    marginTop: Spacing.lg,
  },
  sectionTitle: {
    paddingHorizontal: Spacing.lg,
    marginBottom: Spacing.md,
  },
  servicesScroll: {
    paddingHorizontal: Spacing.lg,
    gap: Spacing.md,
  },
  serviceCard: {
    width: 120,
    marginRight: Spacing.sm,
  },
  serviceImage: {
    width: 120,
    height: 120,
    borderRadius: 12,
    marginBottom: Spacing.xs,
  },
  serviceTitle: {
    textAlign: 'center',
    fontWeight: '600',
  },
  actionsGrid: {
    paddingHorizontal: Spacing.lg,
    gap: Spacing.md,
  },
  actionButton: {
    marginBottom: Spacing.sm,
  },
});
