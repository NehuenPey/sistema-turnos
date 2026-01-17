import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';


@Component({
  standalone: true,
  selector: 'app-login',
  imports: [CommonModule, FormsModule],
  template: `
    <h2>Login</h2>

    <input type="email" [(ngModel)]="email" placeholder="Email">
    <input type="password" [(ngModel)]="password" placeholder="Password">

    <button (click)="login()">Ingresar</button>

    <p *ngIf="error">{{ error }}</p>
  `
})
export class LoginComponent {
  email = '';
  password = '';
  error = '';

  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

 login() {
  if (!this.email || !this.password) {
    this.error = 'Email y contraseña son obligatorios';
    return;
  }

  this.authService.login(this.email, this.password)
    .subscribe({
      next: (res: any) => {
        if (!res || !res.token) {
          this.error = 'Respuesta inválida del servidor';
          return;
        }

        this.authService.saveToken(res.token);
        this.router.navigate(['/clients']);
      },
      error: (err) => {
        console.error('LOGIN ERROR:', err);
        this.error = 'Login incorrecto';
      }
    });
}



}
