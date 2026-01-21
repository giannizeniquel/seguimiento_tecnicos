import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';
import { ApiService } from './api.service';
import { IUser, ILoginRequest, ILoginResponse, ICreateUserRequest, IUpdateUserRequest } from '../models';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private currentUserSubject = new BehaviorSubject<IUser | null>(null);
  public currentUser$ = this.currentUserSubject.asObservable();

  constructor(private apiService: ApiService) {}

  login(credentials: ILoginRequest): Observable<ILoginResponse> {
    return this.apiService.post<ILoginResponse>('/auth/login', credentials);
  }

  logout(): Observable<{ message: string }> {
    return this.apiService.post<{ message: string }>('/auth/logout', {});
  }

  getCurrentUser(): Observable<IUser> {
    return this.apiService.get<IUser>('/auth/me');
  }

  createUser(user: ICreateUserRequest): Observable<IUser> {
    return this.apiService.post<IUser>('/users', user);
  }

  getUsers(filters?: any): Observable<IUser[]> {
    return this.apiService.get<IUser[]>('/users', filters);
  }

  getUser(id: string): Observable<IUser> {
    return this.apiService.get<IUser>(`/users/${id}`);
  }

  updateUser(id: string, user: IUpdateUserRequest): Observable<IUser> {
    return this.apiService.put<IUser>(`/users/${id}`, user);
  }

  deleteUser(id: string): Observable<{ message: string }> {
    return this.apiService.delete<{ message: string }>(`/users/${id}`);
  }

  toggleUserActive(id: string): Observable<IUser> {
    return this.apiService.put<IUser>(`/users/${id}/toggle-active`, {});
  }

  setCurrentUser(user: IUser | null): void {
    this.currentUserSubject.next(user);
  }

  getCurrentUserValue(): IUser | null {
    return this.currentUserSubject.value;
  }

  isLoggedIn(): boolean {
    return !!localStorage.getItem('token');
  }

  logoutClient(): void {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    this.setCurrentUser(null);
  }

  saveToken(token: string): void {
    localStorage.setItem('token', token);
  }

  getToken(): string | null {
    return localStorage.getItem('token');
  }
}
