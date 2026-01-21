import { Routes } from '@angular/router';
import { AuthGuard } from '../core/guards/auth.guard';

export const routes: Routes = [
  {
    path: '',
    redirectTo: 'login',
    pathMatch: 'full'
  },
  {
    path: 'login',
    loadComponent: () => import('../features/auth/pages/login/login.component')
      .then(m => m.LoginComponent)
  },
  {
    path: 'dashboard',
    loadComponent: () => import('../features/dashboard/pages/dashboard/dashboard.component')
      .then(m => m.DashboardComponent),
    canActivate: [AuthGuard]
  },
  {
    path: 'activities',
    loadComponent: () => import('../features/activities/pages/activities-list/activities-list.component')
      .then(m => m.ActivitiesListComponent),
    canActivate: [AuthGuard]
  },
  {
    path: 'activities/:id',
    loadComponent: () => import('../features/activities/pages/activity-detail/activity-detail.component')
      .then(m => m.ActivityDetailComponent),
    canActivate: [AuthGuard]
  },
  {
    path: 'my-assignments',
    loadComponent: () => import('../features/activities/pages/my-assignments/my-assignments.component')
      .then(m => m.MyAssignmentsComponent),
    canActivate: [AuthGuard]
  },
  {
    path: 'users',
    loadComponent: () => import('../features/users/pages/users-list/users-list.component')
      .then(m => m.UsersListComponent),
    canActivate: [AuthGuard]
  },
  {
    path: '**',
    redirectTo: 'login'
  }
];
