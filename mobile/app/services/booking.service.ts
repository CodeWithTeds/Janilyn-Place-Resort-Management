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
};
