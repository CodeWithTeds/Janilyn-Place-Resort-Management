import api from './api';

export interface DashboardData {
  user: {
    name: string;
    email: string;
  };
  stats: {
    total_orders: number;
    pending_orders: number;
    notifications: number;
  };
  recent_activity: {
    id: number;
    title: string;
    date: string;
    status: string;
  }[];
}

export const getDashboardData = async (): Promise<DashboardData> => {
  try {
    const response = await api.get('/dashboard');
    return response.data.data;
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
    throw error;
  }
};
