import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../services/auth.service';

@Component({
  standalone: true,
  selector: 'app-login',
  imports: [CommonModule, FormsModule, RouterLink],
  template: `
    <h2>Login</h2>

    <input
      type="email"
      [(ngModel)]="email"
      placeholder="Email"
    />

    <input
      type="password"
      [(ngModel)]="password"
      placeholder="Password"
    />

    <button (click)="login()">Ingresar</button>
        <p>
      ¿No tenés cuenta?
      <a routerLink="/register">Registrate</a>
    </p>

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

    this.authService.login(this.email, this.password).subscribe({
      next: (res: any) => {
        if (!res?.token) {
          this.error = 'Respuesta inválida del servidor';
          return;
        }

        this.authService.saveToken(res.token);

        const role = this.authService.getUserRole();

        if (role === 'admin') {
          this.router.navigate(['/clients']);
        } else {
          this.router.navigate(['/appointments']);
        }
      },
      error: () => {
        this.error = 'Login incorrecto';
      }
    });
  }
}
