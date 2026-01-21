import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { ActivityService } from '../../../core/services/activity.service';
import { IActivity } from '../../../core/models';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss']
})
export class DashboardComponent implements OnInit {
  activities: IActivity[] = [];
  isLoading = false;
  currentUser: any = null;
  stats = {
    pending: 0,
    inProgress: 0,
    completed: 0,
    cancelled: 0
  };
  recentActivities: IActivity[] = [];

  constructor(
    private activityService: ActivityService,
    private authService: AuthService,
    private router: Router
  ) {
    this.currentUser = this.authService.getCurrentUserValue();
  }

  ngOnInit(): void {
    this.loadDashboardData();
  }

  loadDashboardData(): void {
    this.isLoading = true;

    if (this.currentUser?.role === 'TECHNICIAN') {
      this.activityService.getAssignments().subscribe({
        next: (assignments) => {
          const activities = assignments.map(a => a.activity);
          this.activities = activities;
          this.calculateStats(activities);
          this.recentActivities = activities.slice(0, 5);
          this.isLoading = false;
        },
        error: () => {
          this.isLoading = false;
        }
      });
    } else {
      this.activityService.getActivities().subscribe({
        next: (activities) => {
          this.activities = activities;
          this.calculateStats(activities);
          this.recentActivities = activities.slice(0, 5);
          this.isLoading = false;
        },
        error: () => {
          this.isLoading = false;
        }
      });
    }
  }

  calculateStats(activities: IActivity[]): void {
    this.stats.pending = activities.filter(a => a.status === 'PENDING').length;
    this.stats.inProgress = activities.filter(a => a.status === 'IN_PROGRESS').length;
    this.stats.completed = activities.filter(a => a.status === 'COMPLETED').length;
    this.stats.cancelled = activities.filter(a => a.status === 'CANCELLED').length;
  }

  goToActivity(id: string): void {
    this.router.navigate(['/activities', id]);
  }

  getStatusBadgeClass(status: string): string {
    switch (status) {
      case 'PENDING':
        return 'bg-warning text-dark';
      case 'IN_PROGRESS':
        return 'bg-info text-white';
      case 'COMPLETED':
        return 'bg-success text-white';
      case 'CANCELLED':
        return 'bg-danger text-white';
      default:
        return 'bg-secondary';
    }
  }

  getStatusIcon(status: string): string {
    switch (status) {
      case 'PENDING':
        return 'bi-clock';
      case 'IN_PROGRESS':
        return 'bi-hourglass-split';
      case 'COMPLETED':
        return 'bi-check-circle';
      case 'CANCELLED':
        return 'bi-x-circle';
      default:
        return 'bi-question-circle';
    }
  }

  getPriorityBadgeClass(priority: string): string {
    switch (priority) {
      case 'URGENT':
        return 'bg-danger';
      case 'HIGH':
        return 'bg-warning text-dark';
      case 'MEDIUM':
        return 'bg-info text-white';
      case 'LOW':
        return 'bg-secondary text-white';
      default:
        return 'bg-secondary';
    }
  }
}
