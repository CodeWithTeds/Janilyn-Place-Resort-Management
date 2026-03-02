import api from './api';
import { RoomType, ExclusiveResortRental, RoomAvailability } from '@/types/room';

export const RoomService = {
  async getAll() {
    const response = await api.get<{ room_types: RoomType[]; exclusive_rentals: ExclusiveResortRental[] }>('/rooms');
    return response.data;
  },

  async getRoom(id: number) {
    const response = await api.get<RoomType>(`/rooms/${id}`);
    return response.data;
  },

  async getRental(id: number) {
    const response = await api.get<ExclusiveResortRental>(`/rentals/${id}`);
    return response.data;
  },

  async checkAvailability(params: { check_in: string; check_out: string; type: 'room' | 'exclusive'; id: number }) {
    const response = await api.post<RoomAvailability>('/check-availability', params);
    return response.data;
  },

  async getAvailableUnits(params: { room_type_id: number; check_in: string; check_out: string }) {
    const response = await api.get<{ id: number; name: string }[]>('/available-units', { params });
    return response.data;
  },
};
