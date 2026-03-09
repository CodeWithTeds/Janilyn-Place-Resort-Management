import React, { useEffect, useState } from 'react';
import { StyleSheet, FlatList, View, TouchableOpacity, Image, RefreshControl, ActivityIndicator, Modal, TextInput, Pressable } from 'react-native';
import { useRouter } from 'expo-router';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { Ionicons } from '@expo/vector-icons';

import { ThemedText } from '@/components/themed-text';
import { ThemedView } from '@/components/themed-view';
import { Colors as ThemeColors, Spacing, Fonts, Palette } from '@/constants/theme';
import { BookingService } from '@/services/booking.service';
import { Booking } from '@/types/booking';
import { useThemeColor } from '@/hooks/use-theme-color';
import { Button } from '@/components/ui/Button';

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
  const [feedbackVisible, setFeedbackVisible] = useState(false);
  const [activeBookingId, setActiveBookingId] = useState<number | null>(null);
  const [rating, setRating] = useState<number | null>(null);
  const [comment, setComment] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [successVisible, setSuccessVisible] = useState(false);
  const [infoVisible, setInfoVisible] = useState(false);
  const [infoRating, setInfoRating] = useState<number | null>(null);
  const [infoComment, setInfoComment] = useState('');
  const [submitted, setSubmitted] = useState<Set<number>>(new Set());
  
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
            <ThemedText style={styles.dateText}>{formatDate(item.created_at)}</ThemedText>
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
            {item.status === 'confirmed' && item.payment_status === 'paid' ? (
               <Pressable
                 onPress={async () => {
                   setActiveBookingId(item.id);
                   try {
                     const res = await BookingService.getFeedback(item.id);
                     if (res.has_feedback) {
                       setInfoRating(res.feedback?.rating ?? null);
                       setInfoComment(res.feedback?.comment ?? '');
                       setInfoVisible(true);
                       return;
                     }
                     setRating(null);
                     setComment('');
                   } catch (e) {
                     setRating(null);
                     setComment('');
                   }
                   setFeedbackVisible(true);
                 }}
                 style={[styles.feedbackButton, { borderColor: primaryColor }]}
               >
                 <ThemedText style={[styles.feedbackButtonText, { color: primaryColor }]}>Leave Feedback</ThemedText>
               </Pressable>
             ) : (
               <Ionicons name="chevron-forward" size={20} color={Palette.gray} />
             )}
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
            <ThemedText style={styles.emptyText}>Looks like you haven’t booked your stay yet.</ThemedText>
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

      <Modal
        visible={feedbackVisible}
        animationType="slide"
        transparent
        onRequestClose={() => setFeedbackVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <ThemedText type="subtitle" style={styles.modalTitle}>Your Feedback</ThemedText>
            <View style={styles.starsRow}>
              {[1,2,3,4,5].map((i) => (
                <Pressable key={i} onPress={() => setRating(i)} style={styles.starWrapper}>
                  <Ionicons name={i <= (rating ?? 0) ? 'star' : 'star-outline'} size={28} color={primaryColor} />
                </Pressable>
              ))}
            </View>
            <TextInput
              value={comment}
              onChangeText={setComment}
              placeholder="Share your experience (required)"
              placeholderTextColor="#9CA3AF"
              multiline
              style={styles.commentInput}
            />
            <View style={styles.modalActions}>
              <Button
                title="Cancel"
                variant="outline"
                onPress={() => {
                  setFeedbackVisible(false);
                  setActiveBookingId(null);
                  setRating(null);
                  setComment('');
                }}
                style={{ flex: 1, marginRight: Spacing.sm }}
              />
              <Button
                title="Submit"
                loading={submitting}
                onPress={async () => {
                  if (!activeBookingId) return;
                  if (!comment.trim()) return;
                  try {
                    setSubmitting(true);
                    await BookingService.submitFeedback(activeBookingId, { rating, comment: comment.trim() });
                    setFeedbackVisible(false);
                    setSuccessVisible(true);
                    setSubmitted(prev => new Set([...Array.from(prev), activeBookingId]));
                    setActiveBookingId(null);
                    setRating(null);
                    setComment('');
                  } catch (e) {
                    const err: any = e as any;
                    const already = err?.response?.status === 409 || /already submitted/i.test(err?.response?.data?.message || '');
                    if (already) {
                      setFeedbackVisible(false);
                      setInfoRating(null);
                      setInfoComment('');
                      setInfoVisible(true);
                    }
                  } finally {
                    setSubmitting(false);
                  }
                }}
                style={{ flex: 1 }}
              />
            </View>
          </View>
        </View>
      </Modal>

      <Modal
        visible={successVisible}
        animationType="fade"
        transparent
        onRequestClose={() => setSuccessVisible(false)}
      >
        <View style={styles.alertOverlay}>
          <View style={styles.alertContent}>
            <Ionicons name="checkmark-circle" size={56} color={primaryColor} style={styles.alertIcon} />
            <ThemedText type="subtitle" style={styles.alertTitle}>Thank you!</ThemedText>
            <ThemedText style={styles.alertMessage}>Your feedback has been submitted.</ThemedText>
            <Button title="OK" onPress={() => setSuccessVisible(false)} style={{ marginTop: Spacing.md }} />
          </View>
        </View>
      </Modal>

      <Modal
        visible={infoVisible}
        animationType="fade"
        transparent
        onRequestClose={() => setInfoVisible(false)}
      >
        <View style={styles.alertOverlay}>
          <View style={styles.alertContent}>
            <Ionicons name="information-circle" size={56} color={Palette.info} style={styles.alertIcon} />
            <ThemedText type="subtitle" style={styles.alertTitle}>Feedback already submitted</ThemedText>
            {infoRating ? (
              <View style={{ flexDirection: 'row', marginBottom: Spacing.sm }}>
                {[1,2,3,4,5].map(i => (
                  <Ionicons key={i} name={i <= (infoRating ?? 0) ? 'star' : 'star-outline'} size={22} color={primaryColor} />
                ))}
              </View>
            ) : null}
            {!!infoComment && <ThemedText style={styles.alertMessage}>{infoComment}</ThemedText>}
            <Button title="Close" onPress={() => setInfoVisible(false)} style={{ marginTop: Spacing.md }} />
          </View>
        </View>
      </Modal>
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
  feedbackButton: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 8,
    borderWidth: 1,
  },
  feedbackButtonText: {
    fontSize: 12,
    fontWeight: '600',
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
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.3)',
    justifyContent: 'flex-end',
  },
  modalContent: {
    backgroundColor: '#fff',
    padding: Spacing.lg,
    borderTopLeftRadius: 16,
    borderTopRightRadius: 16,
  },
  modalTitle: {
    marginBottom: Spacing.md,
  },
  starsRow: {
    flexDirection: 'row',
    marginBottom: Spacing.md,
  },
  starWrapper: {
    marginRight: 8,
  },
  commentInput: {
    minHeight: 100,
    borderWidth: 1,
    borderColor: '#E5E7EB',
    borderRadius: 12,
    padding: Spacing.md,
    textAlignVertical: 'top',
    marginBottom: Spacing.md,
    color: '#111827',
  },
  modalActions: {
    flexDirection: 'row',
  },
  alertOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.35)',
    alignItems: 'center',
    justifyContent: 'center',
    padding: Spacing.lg,
  },
  alertContent: {
    width: '100%',
    maxWidth: 360,
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: Spacing.lg,
    alignItems: 'center',
  },
  alertIcon: {
    marginBottom: Spacing.sm,
  },
  alertTitle: {
    marginBottom: Spacing.xs,
    textAlign: 'center',
  },
  alertMessage: {
    textAlign: 'center',
    color: Palette.gray,
  },
});
