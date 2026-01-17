import { Routes } from '@angular/router';
import { AuthGuard } from './guards/auth.guard';
import { adminGuard } from './guards/admin.guard';

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
    canActivate: [AuthGuard]
  },
  {
    path: 'appointments',
    loadComponent: () =>
      import('./pages/appointments/appointments.component')
        .then(m => m.AppointmentsComponent),
    canActivate: [AuthGuard]
  }
  ,{
    path: 'my-appointments',
    loadComponent: () =>
      import('./pages/my-appointments/my-appointments.component')
        .then(m => m.MyAppointmentsComponent),
    canActivate: [AuthGuard]
  },
  {
  path: 'clients',
  loadComponent: () =>
    import('./pages/clients/clients.component')
      .then(m => m.ClientsComponent),
  canActivate: [AuthGuard, adminGuard]
},



  { path: '**', redirectTo: 'login' }
];
