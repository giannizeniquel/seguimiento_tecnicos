import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { IAssignment, ICreateAssignmentRequest, IUpdateAssignmentRequest } from '../models';

@Injectable({
  providedIn: 'root'
})
export class AssignmentService {
  constructor(private apiService: ApiService) {}

  getAssignments(filters?: any): Observable<IAssignment[]> {
    return this.apiService.get<IAssignment[]>('/assignments', filters);
  }

  getAssignment(id: string): Observable<IAssignment> {
    return this.apiService.get<IAssignment>(`/assignments/${id}`);
  }

  createAssignment(assignment: ICreateAssignmentRequest): Observable<IAssignment> {
    return this.apiService.post<IAssignment>('/assignments', assignment);
  }

  updateAssignment(id: string, assignment: IUpdateAssignmentRequest): Observable<IAssignment> {
    return this.apiService.put<IAssignment>(`/assignments/${id}`, assignment);
  }

  deleteAssignment(id: string): Observable<{ message: string }> {
    return this.apiService.delete<{ message: string }>(`/assignments/${id}`);
  }
}
