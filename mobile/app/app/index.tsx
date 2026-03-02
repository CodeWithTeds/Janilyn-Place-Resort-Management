import { Image as ExpoImage } from 'expo-image';
import { StyleSheet, TouchableOpacity, View, Dimensions, ScrollView, Image } from 'react-native';
import { useRouter, Redirect } from 'expo-router';
import { StatusBar } from 'expo-status-bar';
import { WebView } from 'react-native-webview';

import { ThemedText } from '@/components/themed-text';
import { ThemedView } from '@/components/themed-view';
import { useThemeColor } from '@/hooks/use-theme-color';
import { useAuth } from '../hooks/useAuth';

const { width, height } = Dimensions.get('window');

// Image Assets Data
const SERVICES = [
  { id: '1', title: 'Events', source: require('@/assets/images/reservation/service-option1.png') },
  { id: '2', title: 'Groups', source: require('@/assets/images/reservation/service-option2.png') },
  { id: '3', title: 'Pool', source: require('@/assets/images/reservation/service-option4.png') },
  { id: '4', title: 'Family', source: require('@/assets/images/reservation/service-option5.png') },
  { id: '5', title: 'Night', source: require('@/assets/images/reservation/service-option6.png') },
];

const HIGHLIGHTS = [
  { id: 'h1', title: 'Cozy Atmosphere', subtitle: 'Relax & Unwind', source: require('@/assets/images/reservation/cozyreminders1.png'), height: 250 },
  { id: 'h1b', title: 'Peaceful Vibes', subtitle: 'Nature & Calm', source: require('@/assets/images/reservation/cozyreminders2.png'), height: 220 },
  { id: 'h2', title: 'Weekday Promo', subtitle: 'Special Rates', source: require('@/assets/images/reservation/weekdaypromo.png'), height: 200 },
  { id: 'h3', title: 'Happy Memories', subtitle: 'Our Guests', source: require('@/assets/images/reservation/customer.png'), height: 220 },
  { id: 'h4', title: 'Our Stories', subtitle: 'Read the Blog', source: require('@/assets/images/reservation/blog.png'), height: 180 },
];

const USER_MAP_URL = 'https://www.bing.com/maps/search?v=2&pc=FACEBK&mid=8100&mkt=en-US&fbclid=IwY2xjawQSpXRleHRuA2FlbQIxMABicmlkETFkd2RZdk9RRTVaV3VJaDVkc3J0YwZhcHBfaWQQMjIyMDM5MTc4ODIwMDg5MgABHpm_hbHmON4aMmPln14UMtVaKhEmBYLNlIznejGrVsQyvH4knMOrr7HP6_zn_aem_YaxTNXtYBAMR8nDBgB_WXg&FORM=FBKPL1&style=r&q=Janilyn%27s+Place&ss=id.ypid%3A8144x12560190181312123740&cp=9.324567%7E123.302886&lvl=22';

export default function WelcomeScreen() {
  const router = useRouter();
  const primaryColor = useThemeColor({}, 'primary');
  const secondaryColor = useThemeColor({}, 'secondary');
  
  const { user, isLoading } = useAuth();

  // If user is already authenticated, redirect to tabs
  if (!isLoading && user) {
    return <Redirect href={'/(tabs)' as any} />;
  }

  return (
    <View style={styles.container}>
      <StatusBar style="light" />
      
      {/* Fixed Hero Background */}
      <View style={styles.heroFixed}>
        <Image
          source={require('@/assets/images/reservation/welcome.png')}
          style={styles.heroImage}
          resizeMode="cover"
        />
        <View style={styles.heroOverlay} />
      </View>

      <ScrollView 
        style={styles.scrollView}
        contentContainerStyle={styles.scrollContent}
        showsVerticalScrollIndicator={false}
      >
        {/* Spacer to push content below hero */}
        <View style={styles.heroSpacer} />

        {/* Main Content Card */}
        <ThemedView style={styles.contentCard}>
          {/* Handle Bar */}
          <View style={styles.handleBar} />

          {/* Intro Section */}
          <View style={styles.headerContainer}>
            <ThemedText type="title" style={[styles.title, { color: primaryColor }]}>
              Janilyn&apos;s Place
            </ThemedText>
            <ThemedText style={[styles.subtitle, { color: secondaryColor }]}>
              The Hidden Gem of Dumaguete City
            </ThemedText>
            <ThemedText style={styles.description}>
              Your exclusive getaway for resort rentals, overnight stays, and memorable events.
            </ThemedText>
            
            {/* Auth Buttons */}
            <View style={styles.authButtonsContainer}>
                <TouchableOpacity
                style={[styles.button, { backgroundColor: primaryColor, marginBottom: 12 }]}
                onPress={() => router.push('/(auth)/login' as any)}
                activeOpacity={0.8}
                >
                <ThemedText style={styles.buttonText}>Log In</ThemedText>
                </TouchableOpacity>

                <TouchableOpacity
                style={[styles.button, { backgroundColor: 'transparent', borderWidth: 2, borderColor: primaryColor }]}
                onPress={() => router.push('/(auth)/register' as any)}
                activeOpacity={0.8}
                >
                <ThemedText style={[styles.buttonText, { color: primaryColor }]}>Create Account</ThemedText>
                </TouchableOpacity>
            </View>

          </View>

          {/* Services Section (Horizontal Scroll) */}
          <View style={styles.sectionContainer}>
            <View style={styles.sectionHeader}>
              <ThemedText type="subtitle">Our Services</ThemedText>
              <ThemedText style={{ color: primaryColor, fontSize: 14 }}>See All</ThemedText>
            </View>
            <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.horizontalScroll}>
              {SERVICES.map((item) => (
                <View key={item.id} style={styles.serviceCard}>
                  <ExpoImage source={item.source} style={styles.serviceImage} contentFit="cover" />
                  <View style={styles.serviceOverlay} />
                  <ThemedText style={styles.serviceTitle}>{item.title}</ThemedText>
                </View>
              ))}
            </ScrollView>
          </View>

          {/* Highlights Section (Masonry-ish Vertical) */}
          <View style={styles.sectionContainer}>
            <ThemedText type="subtitle" style={styles.sectionTitle}>Highlights</ThemedText>
            <View style={styles.highlightsGrid}>
              {HIGHLIGHTS.map((item) => (
                <View key={item.id} style={[styles.highlightCard, { height: item.height }]}>
                  <ExpoImage source={item.source} style={styles.highlightImage} contentFit="cover" />
                  <View style={styles.highlightOverlay}>
                    <ThemedText style={styles.highlightTitle}>{item.title}</ThemedText>
                    <ThemedText style={styles.highlightSubtitle}>{item.subtitle}</ThemedText>
                  </View>
                </View>
              ))}
            </View>
          </View>

          {/* Location Map */}
          <View style={styles.sectionContainer}>
            <ThemedText type="subtitle" style={styles.sectionTitle}>Our Location</ThemedText>
            <ThemedText style={{ color: secondaryColor, fontSize: 14, marginBottom: 8 }}>48 DB Catapusan St., Piapi, Dumaguete City</ThemedText>
            <View style={styles.mapContainer}>
               <WebView
                source={{ uri: USER_MAP_URL }}
                style={styles.map}
                scrollEnabled={false}
                scalesPageToFit={true}
              />
            </View>
          </View>

           {/* Footer */}
           <View style={styles.footer}>
              <ThemedText style={styles.footerText}>© 2026 Janilyn&apos;s Place</ThemedText>
           </View>

        </ThemedView>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#000',
  },
  heroFixed: {
    position: 'absolute',
    top: 0,
    left: 0,
    width: width,
    height: height * 0.6, // Fixed height for hero
    backgroundColor: '#1a1a1a', // Fallback color
  },
  heroImage: {
    width: '100%',
    height: '100%',
  },
  heroOverlay: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: 'rgba(0,0,0,0.2)',
  },
  scrollView: {
    flex: 1,
  },
  scrollContent: {
    flexGrow: 1,
  },
  heroSpacer: {
    height: height * 0.5, // Push content down so hero is visible initially
  },
  contentCard: {
    flex: 1,
    borderTopLeftRadius: 32,
    borderTopRightRadius: 32,
    backgroundColor: '#fff', // Use theme color in real app
    paddingTop: 12,
    paddingBottom: 40,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: -2 },
    shadowOpacity: 0.1,
    shadowRadius: 10,
    elevation: 5,
    minHeight: height * 0.6,
  },
  handleBar: {
    width: 40,
    height: 4,
    backgroundColor: '#E5E7EB',
    borderRadius: 2,
    alignSelf: 'center',
    marginBottom: 24,
  },
  headerContainer: {
    paddingHorizontal: 24,
    alignItems: 'center',
    marginBottom: 32,
  },
  title: {
    fontSize: 28,
    fontWeight: '800',
    textAlign: 'center',
    marginBottom: 4,
  },
  subtitle: {
    fontSize: 14,
    fontWeight: '600',
    textTransform: 'uppercase',
    marginBottom: 16,
    letterSpacing: 1,
    textAlign: 'center',
  },
  description: {
    textAlign: 'center',
    color: '#666',
    lineHeight: 22,
    marginBottom: 24,
    paddingHorizontal: 10,
  },
  authButtonsContainer: {
    width: '100%',
    marginTop: 8,
  },
  button: {
    width: '100%',
    paddingVertical: 16,
    borderRadius: 16,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  buttonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '700',
  },
  sectionContainer: {
    marginBottom: 32,
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 24,
    marginBottom: 16,
  },
  sectionTitle: {
    paddingHorizontal: 24,
    marginBottom: 16,
  },
  horizontalScroll: {
    paddingHorizontal: 24,
    paddingRight: 8, // Balance spacing
  },
  serviceCard: {
    width: 140,
    height: 180,
    borderRadius: 20,
    marginRight: 16,
    overflow: 'hidden',
    position: 'relative',
    backgroundColor: '#eee',
  },
  serviceImage: {
    width: '100%',
    height: '100%',
  },
  serviceOverlay: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: 'rgba(0,0,0,0.3)',
  },
  serviceTitle: {
    position: 'absolute',
    bottom: 12,
    left: 12,
    color: '#fff',
    fontWeight: '700',
    fontSize: 16,
  },
  highlightsGrid: {
    paddingHorizontal: 24,
    gap: 16,
  },
  highlightCard: {
    width: '100%',
    borderRadius: 24,
    overflow: 'hidden',
    marginBottom: 0,
    position: 'relative',
    backgroundColor: '#eee',
  },
  highlightImage: {
    width: '100%',
    height: '100%',
  },
  highlightOverlay: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    padding: 20,
    backgroundColor: 'rgba(0,0,0,0.4)',
  },
  highlightTitle: {
    color: '#fff',
    fontSize: 20,
    fontWeight: 'bold',
    marginBottom: 4,
  },
  highlightSubtitle: {
    color: 'rgba(255,255,255,0.9)',
    fontSize: 14,
  },
  footer: {
    alignItems: 'center',
    paddingBottom: 40,
    opacity: 0.5,
  },
  footerText: {
    color: '#9CA3AF',
    fontSize: 12,
  },
  mapContainer: {
    height: 300,
    borderRadius: 16,
    overflow: 'hidden',
    backgroundColor: '#f0f0f0',
    borderWidth: 1,
    borderColor: '#e2e8f0',
    marginTop: 12,
  },
  map: {
    flex: 1,
  },
});