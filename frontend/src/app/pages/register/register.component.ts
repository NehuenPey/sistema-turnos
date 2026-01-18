import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../services/auth.service';

@Component({
    standalone: true,
    selector: 'app-register',
    imports: [CommonModule, FormsModule, RouterLink],
    template: `
    <h2>Registro</h2>

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

    <button (click)="register()">Registrarse</button>

    <p *ngIf="error">{{ error }}</p>

    <p>
      ¿Ya tenés cuenta?
      <a routerLink="/login">Ingresar</a>
    </p>
  `
})
export class RegisterComponent {

    email = '';
    password = '';
    error = '';

    constructor(
        private authService: AuthService,
        private router: Router
    ) { }

    register() {
        if (!this.email || !this.password) {
            this.error = 'Email y contraseña obligatorios';
            return;
        }

        this.authService.register(this.email, this.password)
            .subscribe({
                next: () => {
                    this.router.navigate(['/login']);
                },
                error: (err) => {
                    console.error(err);
                    this.error = 'No se pudo registrar';
                }
            });
    }

}

