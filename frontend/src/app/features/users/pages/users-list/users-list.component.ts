import { Component, OnInit, OnDestroy, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { UserService } from '../../../../core/services/user.service';
import { AuthService } from '../../../../core/services/auth.service';
import { IUser } from '../../../../core/models';
import { Subscription } from 'rxjs';
import { ModalDialogComponent } from '../../../../shared/components/modal-dialog/modal-dialog.component';

@Component({
  selector: 'app-users-list',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, ModalDialogComponent],
  templateUrl: './users-list.component.html',
  styleUrls: ['./users-list.component.scss']
})
export class UsersListComponent implements OnInit {
  users: IUser[] = [];
  filteredUsers: IUser[] = [];
  isLoading = false;
  errorMessage = '';
  isSubmitting = false;
  currentUser: IUser | null = null;
  showFilters = false;
  showCreateModal = false;
  showEditModal = false;
  selectedUser: IUser | null = null;
  filtersForm!: FormGroup;
  createForm!: FormGroup;
  editForm!: FormGroup;
  private subscription?: Subscription;

  stats = {
    total: 0,
    admin: 0,
    coordinator: 0,
    technician: 0,
    attendee: 0,
    active: 0,
    inactive: 0
  };

  constructor(
    private userService: UserService,
    private authService: AuthService,
    private fb: FormBuilder,
    private cdr: ChangeDetectorRef
  ) {
    this.initForms();
  }

  ngOnInit(): void {
    this.currentUser = this.authService.getCurrentUserValue();
    // Keep current user in sync with any later login/session changes
    this.subscription = this.authService.currentUser$.subscribe((u) => {
      this.currentUser = u;
      console.log('UsersList - Current user updated:', this.currentUser);
    });
    console.log('UsersList - Current user:', this.currentUser);
    console.log('UsersList - Can create user?', this.canCreateUser());
    this.loadUsers();
  }

  ngOnDestroy(): void {
    this.subscription?.unsubscribe();
  }

  initForms(): void {
    this.filtersForm = this.fb.group({
      role: [''],
      status: [''],
      search: ['']
    });

    this.createForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
      name: ['', Validators.required],
      role: ['TECHNICIAN', Validators.required],
      phone: ['']
    });

    this.editForm = this.fb.group({
      name: ['', Validators.required],
      role: ['', Validators.required],
      phone: ['']
    });
  }

  loadUsers(): void {
    this.isLoading = true;
    this.userService.getUsers().subscribe({
      next: (users) => {
        this.users = users;
        this.applyFilters();
        this.calculateStats();
        this.isLoading = false;
      },
      error: () => {
        this.errorMessage = 'Error al cargar usuarios';
        this.isLoading = false;
      }
    });
  }

  applyFilters(): void {
    const role = this.filtersForm.get('role')?.value;
    const status = this.filtersForm.get('status')?.value;
    const search = this.filtersForm.get('search')?.value?.toLowerCase();

    this.filteredUsers = this.users.filter(user => {
      if (role && user.role !== role) return false;
      if (status === 'active' && !user.isActive) return false;
      if (status === 'inactive' && user.isActive) return false;
      if (search && !user.name.toLowerCase().includes(search) && !user.email.toLowerCase().includes(search)) return false;
      return true;
    });
  }

  clearFilters(): void {
    this.filtersForm.patchValue({
      role: '',
      status: '',
      search: ''
    });
    this.applyFilters();
  }

  toggleFilters(): void {
    this.showFilters = !this.showFilters;
  }

  openCreateModal(event?: Event): void {
    if (event) {
      event.stopPropagation();
    }
    // Workaround: ensure UI re-renders modal even if change detection timing is off
    this.showCreateModal = false;
    this.cdr.detectChanges();
    setTimeout(() => {
      this.showCreateModal = true;
      this.createForm.reset({ role: 'TECHNICIAN' });
      this.cdr.detectChanges();
    }, 0);
  }

  closeCreateModal(): void {
    this.showCreateModal = false;
    this.createForm.reset();
  }

  openEditModal(user: IUser): void {
    this.selectedUser = user;
    this.showEditModal = true;
    this.editForm.patchValue({
      name: user.name,
      role: user.role,
      phone: user.phone
    });
  }

  closeEditModal(): void {
    this.showEditModal = false;
    this.selectedUser = null;
    this.editForm.reset();
  }

  createUser(): void {
    if (!this.createForm.valid) return;

    this.isSubmitting = true;
    this.userService.createUser(this.createForm.value).subscribe({
      next: (user) => {
        this.users.push(user);
        this.applyFilters();
        this.calculateStats();
        this.closeCreateModal();
        this.isSubmitting = false;
      },
      error: () => {
        this.errorMessage = 'Error al crear usuario';
        this.isSubmitting = false;
      }
    });
  }

  updateUser(): void {
    if (!this.editForm.valid || !this.selectedUser) return;

    this.isSubmitting = true;
    this.userService.updateUser(this.selectedUser.id, this.editForm.value).subscribe({
      next: (updatedUser) => {
        const index = this.users.findIndex(u => u.id === this.selectedUser!.id);
        if (index !== -1) {
          this.users[index] = updatedUser;
        }
        this.applyFilters();
        this.calculateStats();
        this.closeEditModal();
        this.isSubmitting = false;
      },
      error: () => {
        this.errorMessage = 'Error al actualizar usuario';
        this.isSubmitting = false;
      }
    });
  }

  deleteUser(id: string): void {
    if (!confirm('¿Estás seguro de eliminar este usuario?')) return;

    this.userService.deleteUser(id).subscribe({
      next: () => {
        this.users = this.users.filter(u => u.id !== id);
        this.applyFilters();
        this.calculateStats();
      },
      error: () => {
        this.errorMessage = 'Error al eliminar usuario';
      }
    });
  }

  toggleActive(id: string): void {
    this.userService.toggleActive(id).subscribe({
      next: (updatedUser) => {
        const index = this.users.findIndex(u => u.id === id);
        if (index !== -1) {
          this.users[index] = updatedUser;
        }
        this.applyFilters();
        this.calculateStats();
      },
      error: () => {
        this.errorMessage = 'Error al cambiar estado de usuario';
      }
    });
  }

  calculateStats(): void {
    this.stats.total = this.users.length;
    this.stats.admin = this.users.filter(u => u.role === 'ADMIN').length;
    this.stats.coordinator = this.users.filter(u => u.role === 'COORDINATOR').length;
    this.stats.technician = this.users.filter(u => u.role === 'TECHNICIAN').length;
    this.stats.attendee = this.users.filter(u => u.role === 'ATTENDEE').length;
    this.stats.active = this.users.filter(u => u.isActive).length;
    this.stats.inactive = this.users.filter(u => !u.isActive).length;
  }

  getRoleBadgeClass(role: string): string {
    switch (role) {
      case 'ADMIN':
        return 'admin';
      case 'COORDINATOR':
        return 'coordinator';
      case 'TECHNICIAN':
        return 'technician';
      case 'ATTENDEE':
        return 'attendee';
      default:
        return '';
    }
  }

  getRoleLabel(role: string): string {
    switch (role) {
      case 'ADMIN':
        return 'Administrador';
      case 'COORDINATOR':
        return 'Coordinador';
      case 'TECHNICIAN':
        return 'Técnico';
      case 'ATTENDEE':
        return 'Acudiente';
      default:
        return role;
    }
  }

  canCreateUser(): boolean {
    return this.currentUser?.role === 'ADMIN';
  }

  isEditAllowed(user: IUser): boolean {
    if (this.currentUser?.role === 'ADMIN') return true;
    if (this.currentUser?.role === 'COORDINATOR' && user.role === 'TECHNICIAN') return true;
    return false;
  }

  isDeleteAllowed(user: IUser): boolean {
    if (this.currentUser?.role !== 'ADMIN') return false;
    return user.id !== this.currentUser?.id;
  }

  canToggleActive(): boolean {
    return this.currentUser?.role === 'ADMIN';
  }
}
