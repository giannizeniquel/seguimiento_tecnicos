import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, ActivatedRoute, Router } from '@angular/router';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivityService } from '../../../../core/services/activity.service';
import { AssignmentService } from '../../../../core/services/assignment.service';
import { UserService } from '../../../../core/services/user.service';
import { AuthService } from '../../../../core/services/auth.service';
import { IActivity, IUser } from '../../../../core/models';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-activity-detail',
  standalone: true,
  imports: [CommonModule, RouterModule, ReactiveFormsModule],
  templateUrl: './activity-detail.component.html',
  styleUrls: ['./activity-detail.component.scss']
})
export class ActivityDetailComponent implements OnInit {
  activity: IActivity | null = null;
  isLoading = false;
  errorMessage = '';
  isSubmitting = false;
  currentUser: IUser | null = null;
  availableTechnicians: IUser[] = [];
  showAssignModal = false;
  assignmentForm!: FormGroup;
  private subscription?: Subscription;

  activityForm!: FormGroup;
  isNew = false;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private activityService: ActivityService,
    private assignmentService: AssignmentService,
    private userService: UserService,
    private authService: AuthService,
    private fb: FormBuilder
  ) {
    this.initAssignmentForm();
  }

  ngOnInit(): void {
    this.currentUser = this.authService.getCurrentUserValue();
    // Keep current user in sync for permission checks
    this.subscription = this.authService.currentUser$.subscribe((u) => {
      this.currentUser = u;
    });
    this.loadActivity();
    this.loadAvailableTechnicians();
  }

  ngOnDestroy(): void {
    this.subscription?.unsubscribe();
  }

  initForm(activity?: IActivity): void {
    this.activityForm = this.fb.group({
      title: [activity?.title || '', [Validators.required]],
      description: [activity?.description || ''],
      priority: [activity?.priority || 'MEDIUM', [Validators.required]],
      scheduledStart: [activity?.scheduledStart || '', [Validators.required]],
      scheduledEnd: [activity?.scheduledEnd || null],
      locationAddress: [activity?.locationAddress || '']
    });
  }

  initAssignmentForm(): void {
    this.assignmentForm = this.fb.group({
      technicianId: ['']
    });
  }

  loadActivity(): void {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.isNew = false;
      this.isLoading = true;
      this.activityService.getActivity(id).subscribe({
        next: (activity) => {
          this.activity = activity;
          this.initForm(activity);
          this.isLoading = false;
        },
        error: () => {
          this.errorMessage = 'Error al cargar la actividad';
          this.isLoading = false;
        }
      });
    } else {
      this.isNew = true;
      this.initForm();
    }
  }

  saveActivity(): void {
    if (this.activityForm.invalid) {
      this.errorMessage = 'Por favor, complete todos los campos requeridos.';
      return;
    }

    this.isSubmitting = true;
    const activityData = this.activityForm.value;

    if (this.isNew) {
      this.activityService.createActivity(activityData).subscribe({
        next: () => {
          this.isSubmitting = false;
          this.router.navigate(['/activities']);
        },
        error: (err) => {
          this.errorMessage = err.error.message || 'Error al crear la actividad';
          this.isSubmitting = false;
        }
      });
    } else if (this.activity) {
      this.activityService.updateActivity(this.activity.id, activityData).subscribe({
        next: () => {
          this.isSubmitting = false;
          this.router.navigate(['/activities']);
        },
        error: (err) => {
          this.errorMessage = err.error.message || 'Error al actualizar la actividad';
          this.isSubmitting = false;
        }
      });
    }
  }

  goBack(): void {
    this.router.navigate(['/activities']);
  }
  
  get isFormInvalid(): boolean {
    return this.activityForm.invalid;
  }
  
  loadAvailableTechnicians(): void {
    this.userService.getTechnicians().subscribe({
      next: (technicians) => {
        this.availableTechnicians = technicians;
      },
      error: () => {
        this.availableTechnicians = [];
      }
    });
  }

  startActivity(): void {
    if (!this.activity) return;

    this.isSubmitting = true;
    this.activityService.startActivity(this.activity.id).subscribe({
      next: (updatedActivity) => {
        this.activity = updatedActivity;
        this.isSubmitting = false;
      },
      error: () => {
        this.errorMessage = 'Error al iniciar la actividad';
        this.isSubmitting = false;
      }
    });
  }

  completeActivity(): void {
    if (!this.activity) return;

    this.isSubmitting = true;
    this.activityService.completeActivity(this.activity.id).subscribe({
      next: (updatedActivity) => {
        this.activity = updatedActivity;
        this.isSubmitting = false;
      },
      error: () => {
        this.errorMessage = 'Error al completar la actividad';
        this.isSubmitting = false;
      }
    });
  }

  cancelActivity(): void {
    if (!this.activity) return;

    this.isSubmitting = true;
    this.activityService.cancelActivity(this.activity.id).subscribe({
      next: (updatedActivity) => {
        this.activity = updatedActivity;
        this.isSubmitting = false;
      },
      error: () => {
        this.errorMessage = 'Error al cancelar la actividad';
        this.isSubmitting = false;
      }
    });
  }

  openAssignModal(): void {
    this.showAssignModal = true;
    this.assignmentForm.reset();
  }

  closeAssignModal(): void {
    this.showAssignModal = false;
  }

  assignTechnician(): void {
    if (!this.activity || !this.assignmentForm.valid) return;

    const technicianId = this.assignmentForm.value.technicianId;
    this.isSubmitting = true;

    this.assignmentService.assignActivity(
      this.activity.id,
      { technicianId, assignmentNotes: '' }
    ).subscribe({
      next: (assignment) => {
        if (this.activity && assignment.technician) {
          this.activity.assignedTo = assignment.technician;
        }
        this.showAssignModal = false;
        this.isSubmitting = false;
      },
      error: () => {
        this.errorMessage = 'Error al asignar técnico';
        this.isSubmitting = false;
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

  get canStart(): boolean {
    return this.activity?.status === 'PENDING';
  }

  get canComplete(): boolean {
    return this.activity?.status === 'IN_PROGRESS';
  }

  get canCancel(): boolean {
    return this.activity?.status === 'PENDING' || this.activity?.status === 'IN_PROGRESS';
  }

  get canAssign(): boolean {
    return this.currentUser?.role !== 'TECHNICIAN' && (this.activity?.status === 'PENDING');
  }
}
