import React, { useEffect, useState } from 'react';
import { StyleSheet, FlatList, View, TouchableOpacity, Image, RefreshControl, ActivityIndicator } from 'react-native';
import { useRouter } from 'expo-router';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { Ionicons } from '@expo/vector-icons';

import { ThemedText } from '@/components/themed-text';
import { ThemedView } from '@/components/themed-view';
import { Colors as ThemeColors, Spacing, Fonts, Palette } from '@/constants/theme';
import { BookingService } from '@/services/booking.service';
import { Booking } from '@/types/booking';
import { useThemeColor } from '@/hooks/use-theme-color';

const STATUS_COLORS: Record<string, string> = {
  confirmed: Palette.success,
  pending: Palette.warning,
  cancelled: Palette.error,
  checked_in: Palette.info,
  completed: Palette.gray,
};

export default function MyBookingsScreen() {
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const [bookings, setBookings] = useState<Booking[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  
  const primaryColor = useThemeColor({}, 'primary');

  const fetchBookings = async () => {
    try {
      const data = await BookingService.getAll();
      setBookings(data);
    } catch (error) {
      console.error('Failed to fetch bookings', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    fetchBookings();
  }, []);

  const onRefresh = () => {
    setRefreshing(true);
    fetchBookings();
  };

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  };

  const renderItem = ({ item }: { item: Booking }) => {
    const isRoom = !!item.room_type_id;
    const title = isRoom ? item.room_type?.name : item.exclusive_resort_rental?.name;
    const image = isRoom ? item.room_type?.image : item.exclusive_resort_rental?.image;
    
    // Fallback image logic
    const imageSource = image 
      ? { uri: image } 
      : require('@/assets/images/reservation/service-option1.png');

    return (
      <TouchableOpacity 
        style={styles.card}
        activeOpacity={0.9}
        onPress={() => {
            // Future: Navigate to detail
        }}
      >
        <View style={styles.cardHeader}>
            <View style={[styles.statusBadge, { backgroundColor: STATUS_COLORS[item.status] || Palette.gray }]}>
                <ThemedText style={styles.statusText}>{item.status.toUpperCase()}</ThemedText>
            </View>
            <ThemedText style={styles.dateText}>Ref: #{item.id}</ThemedText>
        </View>

        <View style={styles.cardBody}>
            <Image source={imageSource} style={styles.cardImage} />
            <View style={styles.cardContent}>
                <ThemedText type="subtitle" numberOfLines={1} style={styles.cardTitle}>{title || 'Unknown Accommodation'}</ThemedText>
                
                <View style={styles.infoRow}>
                    <Ionicons name="calendar-outline" size={14} color={Palette.gray} />
                    <ThemedText style={styles.infoText}>
                        {formatDate(item.check_in)} - {formatDate(item.check_out)}
                    </ThemedText>
                </View>

                <View style={styles.infoRow}>
                    <Ionicons name="people-outline" size={14} color={Palette.gray} />
                    <ThemedText style={styles.infoText}>{item.pax_count} Guests</ThemedText>
                </View>

                <View style={styles.priceContainer}>
                    <ThemedText style={styles.priceLabel}>Total</ThemedText>
                    <ThemedText style={[styles.priceValue, { color: primaryColor }]}>
                        ₱{Number(item.total_price).toLocaleString()}
                    </ThemedText>
                </View>
            </View>
        </View>
        
        <View style={styles.cardFooter}>
             <View style={styles.paymentInfo}>
                <View style={[styles.paymentBadge, { backgroundColor: item.payment_status === 'paid' ? '#DCFCE7' : '#FEF3C7' }]}>
                    <ThemedText style={[styles.paymentText, { color: item.payment_status === 'paid' ? '#166534' : '#92400E' }]}>
                        {item.payment_status === 'paid' ? 'PAID' : 'UNPAID'}
                    </ThemedText>
                </View>
                <ThemedText style={styles.paymentMethod}>{item.payment_method}</ThemedText>
             </View>
             <Ionicons name="chevron-forward" size={20} color={Palette.gray} />
        </View>
      </TouchableOpacity>
    );
  };

  return (
    <ThemedView style={[styles.container, { paddingTop: insets.top }]}>
     

      {loading ? (
        <View style={styles.centerContainer}>
            <ActivityIndicator size="large" color={primaryColor} />
        </View>
      ) : bookings.length === 0 ? (
        <View style={styles.centerContainer}>
            <Image source={require('@/assets/images/reservation/cozyreminders1.png')} style={styles.emptyImage} />
            <ThemedText type="subtitle" style={styles.emptyTitle}>No bookings yet</ThemedText>
            <ThemedText style={styles.emptyText}>Looks like you haven't booked your stay yet.</ThemedText>
            <TouchableOpacity 
                style={[styles.bookButton, { backgroundColor: primaryColor }]}
                onPress={() => router.push('/(tabs)/explore')}
            >
                <ThemedText style={styles.bookButtonText}>Find a Room</ThemedText>
            </TouchableOpacity>
        </View>
      ) : (
        <FlatList
            data={bookings}
            renderItem={renderItem}
            keyExtractor={(item) => item.id.toString()}
            contentContainerStyle={styles.listContent}
            refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
            showsVerticalScrollIndicator={false}
        />
      )}
    </ThemedView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F9FAFB',
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: Spacing.lg,
    paddingBottom: Spacing.md,
    backgroundColor: '#fff',
    borderBottomWidth: 1,
    borderBottomColor: '#F3F4F6',
  },
  backButton: {
    padding: Spacing.xs,
    marginRight: Spacing.sm,
  },
  headerTitle: {
    fontSize: 20,
    fontFamily: Fonts.rounded,
  },
  listContent: {
    padding: Spacing.lg,
    gap: Spacing.lg,
  },
  card: {
    backgroundColor: '#fff',
    borderRadius: 16,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 8,
    elevation: 2,
    overflow: 'hidden',
    marginBottom: Spacing.sm,
    borderWidth: 1,
    borderColor: '#F3F4F6',
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: Spacing.md,
    paddingVertical: Spacing.sm,
    borderBottomWidth: 1,
    borderBottomColor: '#F9FAFB',
  },
  statusBadge: {
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 6,
  },
  statusText: {
    color: '#fff',
    fontSize: 10,
    fontWeight: '700',
  },
  dateText: {
    fontSize: 12,
    color: Palette.gray,
  },
  cardBody: {
    flexDirection: 'row',
    padding: Spacing.md,
  },
  cardImage: {
    width: 80,
    height: 80,
    borderRadius: 12,
    backgroundColor: '#F3F4F6',
  },
  cardContent: {
    flex: 1,
    marginLeft: Spacing.md,
    justifyContent: 'space-between',
  },
  cardTitle: {
    fontSize: 16,
    marginBottom: 4,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 4,
  },
  infoText: {
    fontSize: 12,
    color: Palette.gray,
    marginLeft: 6,
  },
  priceContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 4,
  },
  priceLabel: {
    fontSize: 12,
    color: Palette.gray,
    marginRight: 4,
  },
  priceValue: {
    fontSize: 16,
    fontWeight: '700',
  },
  cardFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: Spacing.md,
    paddingVertical: Spacing.sm,
    backgroundColor: '#F9FAFB',
    borderTopWidth: 1,
    borderTopColor: '#F3F4F6',
  },
  paymentInfo: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  paymentBadge: {
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 4,
    marginRight: 8,
  },
  paymentText: {
    fontSize: 10,
    fontWeight: '700',
  },
  paymentMethod: {
    fontSize: 12,
    color: Palette.gray,
    textTransform: 'capitalize',
  },
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: Spacing.xl,
  },
  emptyImage: {
    width: 150,
    height: 150,
    marginBottom: Spacing.lg,
    opacity: 0.5,
  },
  emptyTitle: {
    marginBottom: Spacing.xs,
    color: Palette.gray,
  },
  emptyText: {
    textAlign: 'center',
    color: Palette.gray,
    marginBottom: Spacing.xl,
  },
  bookButton: {
    paddingVertical: Spacing.md,
    paddingHorizontal: Spacing.xl,
    borderRadius: 12,
  },
  bookButtonText: {
    color: '#fff',
    fontWeight: '600',
    fontSize: 16,
  },
});