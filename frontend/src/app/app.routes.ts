import { Routes } from '@angular/router';

export const routes: Routes = [
  {
    path: '',
    redirectTo: 'login',
    pathMatch: 'full'
  },
  {
    path: 'login',
    loadComponent: () => import('./app/features/auth/pages/login/login.component')
      .then(m => m.LoginComponent)
  },
  {
    path: 'dashboard',
    loadComponent: () => import('./app/features/dashboard/pages/dashboard/dashboard.component')
      .then(m => m.DashboardComponent)
  },
  {
    path: 'activities',
    loadComponent: () => import('./app/features/activities/pages/activities-list/activities-list.component')
      .then(m => m.ActivitiesListComponent)
  },
  {
    path: 'activities/:id',
    loadComponent: () => import('./app/features/activities/pages/activity-detail/activity-detail.component')
      .then(m => m.ActivityDetailComponent)
  },
  {
    path: 'my-assignments',
    loadComponent: () => import('./app/features/activities/pages/my-assignments/my-assignments.component')
      .then(m => m.MyAssignmentsComponent)
  },
  {
    path: 'users',
    loadComponent: () => import('./app/features/users/pages/users-list/users-list.component')
      .then(m => m.UsersListComponent)
  },
  {
    path: '**',
    redirectTo: 'login'
  }
];
