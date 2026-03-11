import { StyleSheet, View, ScrollView, TouchableOpacity, Dimensions } from 'react-native';
import { useRouter } from 'expo-router';
import { Image } from 'expo-image';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { WebView } from 'react-native-webview';
import { Ionicons } from '@expo/vector-icons';

import { ThemedText } from '@/components/themed-text';
import { ThemedView } from '@/components/themed-view';
import { useAuth } from '@/hooks/useAuth';
import { Palette as Colors, Spacing, Fonts } from '@/constants/theme';

const { width } = Dimensions.get('window');

const FEATURED_SERVICES = [
  { id: '1', title: 'Luxury Rooms', subtitle: 'Starting at ₱1,500', image: require('@/assets/images/reservation/service-option1.png'), route: '/(tabs)/explore' },
  { id: '2', title: 'Exclusive Events', subtitle: 'Weddings & Parties', image: require('@/assets/images/reservation/service-option2.png'), route: '/(tabs)/explore' },
  { id: '3', title: 'Relaxing Pool', subtitle: 'Day & Night Access', image: require('@/assets/images/reservation/service-option4.png'), route: '/(tabs)/explore' },
];

const USER_MAP_URL = 'https://www.bing.com/maps?cp=9.324678~123.302822&lvl=18&sp=point.9.324678_123.302822_Janilyn%27s%20Place&sty=r';

export default function HomeScreen() {
  const { user, signOut } = useAuth();
  const router = useRouter();
  const insets = useSafeAreaInsets();

  return (
    <ThemedView style={styles.container}>
      <ScrollView 
        contentContainerStyle={[styles.scrollContent, { paddingTop: insets.top + Spacing.md }]}
        showsVerticalScrollIndicator={false}
      >
        {/* Header Section */}
        <View style={styles.header}>
          <View>
            <ThemedText style={styles.greeting}>Good day,</ThemedText>
            <ThemedText type="title" style={styles.username}>{user?.name?.split(' ')[0] || 'Guest'}</ThemedText>
          </View>
          <TouchableOpacity onPress={signOut} style={styles.profileButton}>
            <Image 
              source={require('@/assets/images/reservation/customer.png')} 
              style={styles.profileImage} 
              contentFit="cover"
            />
          </TouchableOpacity>
        </View>

        {/* Hero Section */}
        <View style={styles.heroContainer}>
          <View style={styles.heroCard}>
            <Image
              source={require('@/assets/images/reservation/welcome.png')}
              style={styles.heroImage}
              contentFit="cover"
            />
            <View style={styles.heroOverlay}>
              <View style={styles.heroTextContainer}>
                <ThemedText type="title" style={styles.heroTitle}>Escape to Paradise</ThemedText>
                <ThemedText style={styles.heroSubtitle}>Your exclusive getaway in Dumaguete</ThemedText>
              </View>
              <TouchableOpacity 
                style={styles.heroButton}
                onPress={() => router.push('/(tabs)/explore')}
                activeOpacity={0.9}
              >
                <ThemedText style={styles.heroButtonText}>Book Now</ThemedText>
                <Ionicons name="arrow-forward" size={20} color="#fff" />
              </TouchableOpacity>
            </View>
          </View>
        </View>

        {/* Quick Actions Grid */}
        <View style={styles.section}>
          <ThemedText type="subtitle" style={styles.sectionTitle}>Quick Actions</ThemedText>
          <View style={styles.actionsGrid}>
            <TouchableOpacity 
              style={[styles.actionCard, { backgroundColor: '#e0f2fe' }]}
              onPress={() => router.push('/(tabs)/explore')}
            >
              <View style={[styles.iconCircle, { backgroundColor: '#0ea5e9' }]}>
                <Ionicons name="bed-outline" size={24} color="#fff" />
              </View>
              <ThemedText style={styles.actionTitle}>View Rooms</ThemedText>
              <ThemedText style={styles.actionSubtitle}>Check availability</ThemedText>
            </TouchableOpacity>

            <TouchableOpacity 
              style={[styles.actionCard, { backgroundColor: '#f0fdf4' }]}
              onPress={() => router.push('/booking' as any)}
            >
              <View style={[styles.iconCircle, { backgroundColor: '#22c55e' }]}>
                <Ionicons name="calendar-outline" size={24} color="#fff" />
              </View>
              <ThemedText style={styles.actionTitle}>My Bookings</ThemedText>
              <ThemedText style={styles.actionSubtitle}>Manage stays</ThemedText>
            </TouchableOpacity>
          </View>
        </View>

        {/* Featured Services */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <ThemedText type="subtitle" style={styles.sectionTitle}>Featured</ThemedText>
            <TouchableOpacity onPress={() => router.push('/(tabs)/explore')}>
              <ThemedText style={styles.seeAllText}>See All</ThemedText>
            </TouchableOpacity>
          </View>
          
          <ScrollView 
            horizontal 
            showsHorizontalScrollIndicator={false} 
            contentContainerStyle={styles.servicesScroll}
            decelerationRate="fast"
            snapToInterval={width * 0.7 + Spacing.md}
          >
            {FEATURED_SERVICES.map((service) => (
              <TouchableOpacity 
                key={service.id} 
                style={styles.serviceCard}
                onPress={() => router.push(service.route as any)}
                activeOpacity={0.9}
              >
                <Image source={service.image} style={styles.serviceImage} contentFit="cover" />
                <View style={styles.serviceOverlay}>
                  <ThemedText style={styles.serviceTitle}>{service.title}</ThemedText>
                  <ThemedText style={styles.serviceSubtitle}>{service.subtitle}</ThemedText>
                </View>
              </TouchableOpacity>
            ))}
          </ScrollView>
        </View>

        {/* Location Map */}
        <View style={styles.section}>
          <ThemedText type="subtitle" style={styles.sectionTitle}>Visit Us</ThemedText>
          <View style={styles.mapCard}>
            <WebView
              source={{ uri: USER_MAP_URL }}
              style={styles.map}
              scrollEnabled={false}
            />
            <View style={styles.addressOverlay}>
               <Ionicons name="location" size={16} color={Colors.primary} />
               <ThemedText style={styles.addressText}>48 DB Catapusan St., Piapi, Dumaguete City</ThemedText>
            </View>
          </View>
        </View>

        <View style={{ height: 100 }} /> 
      </ScrollView>
    </ThemedView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f8fafc', // Light gray background for modern feel
  },
  scrollContent: {
    paddingBottom: Spacing.xl,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: Spacing.lg,
    marginBottom: Spacing.lg,
  },
  greeting: {
    fontSize: 14,
    color: '#64748b',
    fontFamily: Fonts.sans,
  },
  username: {
    fontSize: 24,
    fontFamily: Fonts.rounded,
  },
  profileButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    overflow: 'hidden',
    borderWidth: 2,
    borderColor: '#fff',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  profileImage: {
    width: '100%',
    height: '100%',
  },
  heroContainer: {
    paddingHorizontal: Spacing.lg,
    marginBottom: Spacing.xl,
  },
  heroCard: {
    height: 360, // Taller hero
    borderRadius: 32,
    overflow: 'hidden',
    shadowColor: Colors.primary,
    shadowOffset: { width: 0, height: 10 },
    shadowOpacity: 0.2,
    shadowRadius: 20,
    elevation: 10,
    backgroundColor: '#000',
  },
  heroImage: {
    width: '100%',
    height: '100%',
    opacity: 0.9,
  },
  heroOverlay: {
    ...StyleSheet.absoluteFillObject,
    padding: Spacing.xl,
    justifyContent: 'flex-end',
    backgroundColor: 'rgba(0,0,0,0.2)', // Subtle darken
  },
  heroTextContainer: {
    marginBottom: Spacing.lg,
  },
  heroTitle: {
    color: '#fff',
    fontSize: 32,
    lineHeight: 38,
    marginBottom: 8,
    textShadowColor: 'rgba(0,0,0,0.3)',
    textShadowOffset: { width: 0, height: 2 },
    textShadowRadius: 4,
  },
  heroSubtitle: {
    color: 'rgba(255,255,255,0.9)',
    fontSize: 16,
    fontFamily: Fonts.sans,
  },
  heroButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: Colors.primary,
    paddingVertical: 14,
    paddingHorizontal: 24,
    borderRadius: 100,
    alignSelf: 'flex-start',
    gap: 8,
  },
  heroButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16,
  },
  section: {
    marginBottom: Spacing.xl,
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: Spacing.lg,
    marginBottom: Spacing.md,
  },
  sectionTitle: {
    fontSize: 20,
    fontFamily: Fonts.rounded,
    marginLeft: Spacing.lg,
  },
  seeAllText: {
    color: Colors.primary,
    fontWeight: '600',
    marginRight: Spacing.lg,
  },
  actionsGrid: {
    flexDirection: 'row',
    gap: Spacing.md,
    paddingHorizontal: Spacing.lg,
  },
  actionCard: {
    flex: 1,
    padding: Spacing.lg,
    borderRadius: 24,
    alignItems: 'flex-start',
    justifyContent: 'center',
  },
  iconCircle: {
    width: 48,
    height: 48,
    borderRadius: 24,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: Spacing.md,
  },
  actionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#0f172a',
    marginBottom: 2,
  },
  actionSubtitle: {
    fontSize: 12,
    color: '#64748b',
  },
  servicesScroll: {
    paddingHorizontal: Spacing.lg,
    gap: Spacing.md,
    paddingBottom: Spacing.md, // For shadow
  },
  serviceCard: {
    width: width * 0.7,
    height: 200,
    borderRadius: 24,
    overflow: 'hidden',
    backgroundColor: '#fff',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 4,
  },
  serviceImage: {
    width: '100%',
    height: '100%',
  },
  serviceOverlay: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    padding: Spacing.md,
    backgroundColor: 'rgba(0,0,0,0.4)', // Gradient-like
    paddingBottom: Spacing.lg,
  },
  serviceTitle: {
    color: '#fff',
    fontSize: 18,
    fontWeight: 'bold',
  },
  serviceSubtitle: {
    color: 'rgba(255,255,255,0.9)',
    fontSize: 12,
  },
  mapCard: {
    marginHorizontal: Spacing.lg,
    height: 200,
    borderRadius: 24,
    overflow: 'hidden',
    backgroundColor: '#fff',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 4,
  },
  map: {
    flex: 1,
  },
  addressOverlay: {
    position: 'absolute',
    bottom: Spacing.md,
    left: Spacing.md,
    right: Spacing.md,
    backgroundColor: '#fff',
    padding: Spacing.sm,
    paddingHorizontal: Spacing.md,
    borderRadius: 12,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  addressText: {
    fontSize: 12,
    color: '#0f172a',
    flex: 1,
  },
});
