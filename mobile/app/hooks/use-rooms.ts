import { useState, useCallback } from 'react';
import { RoomService } from '@/services/room.service';
import { RoomType, ExclusiveResortRental } from '@/types/room';

export function useRooms() {
  const [rooms, setRooms] = useState<RoomType[]>([]);
  const [rentals, setRentals] = useState<ExclusiveResortRental[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const fetchRooms = useCallback(async () => {
    try {
      setLoading(true);
      setError(null);
      const data = await RoomService.getAll();
      setRooms(data.room_types);
      setRentals(data.exclusive_rentals);
    } catch (err: any) {
      setError(err.message || 'Failed to fetch rooms');
    } finally {
      setLoading(false);
    }
  }, []);

  return {
    rooms,
    rentals,
    loading,
    error,
    fetchRooms,
  };
}
