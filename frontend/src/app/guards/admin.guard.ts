import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

export const adminGuard: CanActivateFn = () => {

  const auth = inject(AuthService);
  const router = inject(Router);

  const role = auth.getUserRole();

  if (role === 'admin') {
    return true;
  }

  router.navigate(['/appointments']);
  return false;
};
