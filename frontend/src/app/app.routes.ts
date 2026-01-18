import { Routes } from '@angular/router';
import { AuthGuard } from './guards/auth.guard';
import { AdminGuard } from './guards/admin.guard';

export const routes: Routes = [

  {
    path: 'login',
    loadComponent: () =>
      import('./pages/login/login.component')
        .then(m => m.LoginComponent)
  },

  {
    path: 'clients',
    loadComponent: () =>
      import('./pages/clients/clients.component')
        .then(m => m.ClientsComponent),
    canActivate: [AuthGuard, AdminGuard] // 🔒 SOLO ADMIN
  },

  {
    path: 'appointments',
    loadComponent: () =>
      import('./pages/appointments/appointments.component')
        .then(m => m.AppointmentsComponent),
    canActivate: [AuthGuard]
  },

  {
    path: 'my-appointments',
    loadComponent: () =>
      import('./pages/my-appointments/my-appointments.component')
        .then(m => m.MyAppointmentsComponent),
    canActivate: [AuthGuard]
  },
  {
  path: 'register',
  loadComponent: () =>
    import('./pages/register/register.component')
      .then(m => m.RegisterComponent)
}
,

  { path: '**', redirectTo: 'login' }
];
