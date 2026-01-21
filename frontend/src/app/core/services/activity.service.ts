import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { IActivity, ICreateActivityRequest, IUpdateActivityRequest } from '../models';

@Injectable({
  providedIn: 'root'
})
export class ActivityService {
  constructor(private apiService: ApiService) {}

  getActivities(filters?: any): Observable<IActivity[]> {
    return this.apiService.get<IActivity[]>('/activities', filters);
  }

  getActivity(id: string): Observable<IActivity> {
    return this.apiService.get<IActivity>(`/activities/${id}`);
  }

  createActivity(activity: ICreateActivityRequest): Observable<IActivity> {
    return this.apiService.post<IActivity>('/activities', activity);
  }

  updateActivity(id: string, activity: IUpdateActivityRequest): Observable<IActivity> {
    return this.apiService.put<IActivity>(`/activities/${id}`, activity);
  }

  deleteActivity(id: string): Observable<{ message: string }> {
    return this.apiService.delete<{ message: string }>(`/activities/${id}`);
  }

  startActivity(id: string): Observable<IActivity> {
    return this.apiService.post<IActivity>(`/activities/${id}/start`, {});
  }

  completeActivity(id: string): Observable<IActivity> {
    return this.apiService.post<IActivity>(`/activities/${id}/complete`, {});
  }

  cancelActivity(id: string): Observable<IActivity> {
    return this.apiService.post<IActivity>(`/activities/${id}/cancel`, {});
  }
}
