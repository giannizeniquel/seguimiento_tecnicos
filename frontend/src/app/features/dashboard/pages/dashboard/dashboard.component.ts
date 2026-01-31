import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterModule } from '@angular/router';
import { ActivityService } from '../../../../core/services/activity.service';
import { AssignmentService } from '../../../../core/services/assignment.service';
import { AuthService } from '../../../../core/services/auth.service';
import { IUser } from '../../../../core/models';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss']
})
export class DashboardComponent implements OnInit {
  activities: any[] = [];
  isLoading = false;
  currentUser: IUser | null = null;
  stats = {
    pending: 0,
    inProgress: 0,
    completed: 0,
    cancelled: 0
  };
  recentActivities: any[] = [];

  constructor(
    private activityService: ActivityService,
    private assignmentService: AssignmentService,
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
      this.assignmentService.getAssignments().subscribe({
        next: (assignments: any[]) => {
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

  calculateStats(activities: any[]): void {
    this.stats.pending = activities.filter(a => a.status === 'PENDING').length;
    this.stats.inProgress = activities.filter(a => a.status === 'IN_PROGRESS').length;
    this.stats.completed = activities.filter(a => a.status === 'COMPLETED').length;
    this.stats.cancelled = activities.filter(a => a.status === 'CANCELLED').length;
  }

  goToActivity(id: string): void {
    this.router.navigate(['/activities', id]);
  }
}
