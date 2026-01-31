import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { IUser } from '../models';

@Injectable({
  providedIn: 'root'
})
export class UserService {
  constructor(private apiService: ApiService) {}

  getUsers(filters?: any): Observable<IUser[]> {
    return this.apiService.get<IUser[]>('/users', filters);
  }

  getUser(id: string): Observable<IUser> {
    return this.apiService.get<IUser>(`/users/${id}`);
  }

  getTechnicians(): Observable<IUser[]> {
    return this.getUsers({ role: 'TECHNICIAN', isActive: true });
  }

  createUser(user: any): Observable<IUser> {
    return this.apiService.post<IUser>('/users', user);
  }

  updateUser(id: string, user: any): Observable<IUser> {
    return this.apiService.put<IUser>(`/users/${id}`, user);
  }

  deleteUser(id: string): Observable<{ message: string }> {
    return this.apiService.delete<{ message: string }>(`/users/${id}`);
  }

  toggleActive(id: string): Observable<IUser> {
    return this.apiService.put<IUser>(`/users/${id}/toggle-active`, {});
  }
}
