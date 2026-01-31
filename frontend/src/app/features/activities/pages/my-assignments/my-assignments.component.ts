import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { FormBuilder, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { ActivityService } from '../../../../core/services/activity.service';
import { AuthService } from '../../../../core/services/auth.service';
import { IActivity, IUser } from '../../../../core/models';

@Component({
  selector: 'app-my-assignments',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './my-assignments.component.html',
  styleUrls: ['./my-assignments.component.scss']
})
export class MyAssignmentsComponent implements OnInit {
  activities: IActivity[] = [];
  filteredActivities: IActivity[] = [];
  isLoading = false;
  errorMessage = '';
  currentUser: IUser | null = null;
  filtersForm!: FormGroup;

  stats = {
    total: 0,
    pending: 0,
    inProgress: 0,
    completed: 0
  };

  constructor(
    private activityService: ActivityService,
    private authService: AuthService,
    private router: Router,
    private fb: FormBuilder
  ) {
    this.initFiltersForm();
  }

  ngOnInit(): void {
    this.currentUser = this.authService.getCurrentUserValue();
    this.loadActivities();
  }

  initFiltersForm(): void {
    this.filtersForm = this.fb.group({
      status: ['']
    });
  }

  loadActivities(): void {
    this.isLoading = true;
    this.activityService.getActivities().subscribe({
      next: (activities) => {
        this.activities = activities;
        this.applyFilters();
        this.calculateStats();
        this.isLoading = false;
      },
      error: () => {
        this.errorMessage = 'Error al cargar asignaciones';
        this.isLoading = false;
      }
    });
  }

  applyFilters(): void {
    const status = this.filtersForm.get('status')?.value;

    this.filteredActivities = this.activities.filter(activity => {
      if (status && activity.status !== status) return false;
      return true;
    });
  }

  calculateStats(): void {
    this.stats.total = this.activities.length;
    this.stats.pending = this.activities.filter(a => a.status === 'PENDING').length;
    this.stats.inProgress = this.activities.filter(a => a.status === 'IN_PROGRESS').length;
    this.stats.completed = this.activities.filter(a => a.status === 'COMPLETED').length;
  }

  clearFilters(): void {
    this.filtersForm.patchValue({ status: '' });
    this.applyFilters();
  }

  goToActivity(id: string): void {
    this.router.navigate(['/activities', id]);
  }

  startActivity(activity: IActivity): void {
    this.activityService.startActivity(activity.id).subscribe({
      next: (updatedActivity) => {
        const index = this.activities.findIndex(a => a.id === activity.id);
        if (index !== -1) {
          this.activities[index] = updatedActivity;
        }
        this.applyFilters();
        this.calculateStats();
      },
      error: () => {
        this.errorMessage = 'Error al iniciar actividad';
      }
    });
  }

  completeActivity(activity: IActivity): void {
    this.activityService.completeActivity(activity.id).subscribe({
      next: (updatedActivity) => {
        const index = this.activities.findIndex(a => a.id === activity.id);
        if (index !== -1) {
          this.activities[index] = updatedActivity;
        }
        this.applyFilters();
        this.calculateStats();
      },
      error: () => {
        this.errorMessage = 'Error al completar actividad';
      }
    });
  }

  getStatusClass(status: string): string {
    switch (status) {
      case 'PENDING':
        return 'pending';
      case 'IN_PROGRESS':
        return 'process';
      case 'COMPLETED':
        return 'completed';
      case 'CANCELLED':
        return 'cancelled';
      default:
        return '';
    }
  }

  getPriorityClass(priority: string): string {
    switch (priority) {
      case 'URGENT':
        return 'urgent';
      case 'HIGH':
        return 'high';
      case 'MEDIUM':
        return 'medium';
      case 'LOW':
        return 'low';
      default:
        return '';
    }
  }

  canStart(activity: IActivity): boolean {
    return activity.status === 'PENDING';
  }

  canComplete(activity: IActivity): boolean {
    return activity.status === 'IN_PROGRESS';
  }

  isOverdue(activity: IActivity): boolean {
    if (!activity.scheduledEnd) return false;
    const scheduledEnd = new Date(activity.scheduledEnd);
    const now = new Date();
    return scheduledEnd < now && activity.status !== 'COMPLETED';
  }
}
