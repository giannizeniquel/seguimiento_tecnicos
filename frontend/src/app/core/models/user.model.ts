export interface IUser {
  id: string;
  email: string;
  name: string;
  role: 'ADMIN' | 'COORDINATOR' | 'TECHNICIAN' | 'ATTENDEE';
  phone: string | null;
  isActive: boolean;
  createdAt: string;
  updatedAt: string;
}

export interface ILoginRequest {
  email: string;
  password: string;
}

export interface ILoginResponse {
  token: string;
  user: IUser;
}

export interface ICreateUserRequest {
  email: string;
  password: string;
  name: string;
  role: 'ADMIN' | 'COORDINATOR' | 'TECHNICIAN' | 'ATTENDEE';
  phone?: string;
}

export interface IUpdateUserRequest {
  email?: string;
  name?: string;
  role?: 'ADMIN' | 'COORDINATOR' | 'TECHNICIAN' | 'ATTENDEE';
  phone?: string;
  password?: string;
  isActive?: boolean;
}
