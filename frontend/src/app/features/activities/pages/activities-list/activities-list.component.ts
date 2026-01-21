import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { ActivityService } from '../../../../core/services/activity.service';
import { IActivity } from '../../../../core/models';

@Component({
  selector: 'app-activities-list',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './activities-list.component.html',
  styleUrls: ['./activities-list.component.scss']
})
export class ActivitiesListComponent implements OnInit {
  activities: IActivity[] = [];
  filteredActivities: IActivity[] = [];
  isLoading = false;
  showFilters = false;
  currentPage = 1;
  itemsPerPage = 10;
  currentUser: any = null;
  filtersForm: FormGroup;

  constructor(
    private activityService: ActivityService,
    private fb: FormBuilder,
    private router: Router,
    @Inject(ActivityService) private _activityService: ActivityService
  ) {
    this.activityService = _activityService;
    this.initFiltersForm();
  }

  ngOnInit(): void {
    this.loadActivities();
  }

  initFiltersForm(): void {
    this.filtersForm = this.fb.group({
      status: [''],
      priority: [''],
      search: ['']
    });
  }

  loadActivities(): void {
    this.isLoading = true;
    this.activityService.getActivities().subscribe({
      next: (activities) => {
        this.activities = activities;
        this.applyFilters();
        this.isLoading = false;
      },
      error: () => {
        this.isLoading = false;
      }
    });
  }

  applyFilters(): void {
    const status = this.filtersForm.get('status')?.value;
    const priority = this.filtersForm.get('priority')?.value;
    const search = this.filtersForm.get('search')?.value?.toLowerCase();

    this.filteredActivities = this.activities.filter(activity => {
      if (status && activity.status !== status) return false;
      if (priority && activity.priority !== priority) return false;
      if (search && !activity.title.toLowerCase().includes(search)) return false;
      return true;
    });

    this.currentPage = 1;
  }

  clearFilters(): void {
    this.filtersForm.patchValue({
      status: '',
      priority: '',
      search: ''
    });
    this.applyFilters();
    this.showFilters = false;
  }

  toggleFilters(): void {
    this.showFilters = !this.showFilters;
  }

  createActivity(): void {
    this.router.navigate(['/activities', 'new']);
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

  get pagedActivities(): IActivity[] {
    const start = (this.currentPage - 1) * this.itemsPerPage;
    const end = start + this.itemsPerPage;
    return this.filteredActivities.slice(start, end);
  }

  get totalPages(): number {
    return Math.ceil(this.filteredActivities.length / this.itemsPerPage);
  }

  previousPage(): void {
    if (this.currentPage > 1) {
      this.currentPage--;
    }
  }

  nextPage(): void {
    if (this.currentPage < this.totalPages) {
      this.currentPage++;
    }
  }
}
