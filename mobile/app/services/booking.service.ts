import api from './api';
import { Booking, CreateBookingData } from '@/types/booking';

export const BookingService = {
  async getAll() {
    const response = await api.get<Booking[]>('/bookings');
    return response.data;
  },

  async create(data: CreateBookingData) {
    const response = await api.post<{ message: string; booking: Booking; checkout_url: string | null }>('/bookings', data);
    return response.data;
  },

  async getFeedback(bookingId: number) {
    const response = await api.get<{ has_feedback: boolean; feedback?: { rating?: number | null; comment: string; created_at: string } }>(`/bookings/${bookingId}/feedback`);
    return response.data;
  },

  async submitFeedback(bookingId: number, data: { rating?: number | null; comment: string }) {
    const response = await api.post<{ message: string }>(`/bookings/${bookingId}/feedback`, data);
    return response.data;
  },
};
